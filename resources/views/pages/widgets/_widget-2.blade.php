
{{-- <div class="card card-custom mb-5">

    <div class="card-body d-flex flex-column p-5">
        <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
            <div class="d-flex flex-column mr-2">
                <a href="{{url('/TaxSerialNumber')}}" class="text-dark-75 text-hover-primary font-weight-bolder font-size-h5">Nomor Seri Faktur Pajak</a>
                <span class="text-muted font-weight-bold mt-2">Tabel Seri Faktur Pajak Aktif</span>
            </div>
        </div>
        <!--begin::Search Form-->
        <div class="mb-5 ml-5 mr-5">
            <div class="row align-items-center">
                <div class="col-lg-10">
                    <div class="row align-items-center">
                        <div class="col-md-4 my-2 my-md-0">
                            <div class="align-items-center">
                                <label style="display: inline-block;"></label>
                                <div class="input-icon">
                                    <input type="text" class="form-control" placeholder="Search..." id="list_serial_search_query"/>
                                    <span>
                                        <i class="flaticon2-search-1 text-muted"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 my-2 my-md-0">
                            <div class="align-items-center">
                                <label class="mr-3 mb-0 d-none d-md-block font-weight-bold" id="jmlInvPPN">Jumlah Invoice Belum Digenerate : 0</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end: Search Form-->

        <!--begin: Datatable-->

        <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="list_serial"></div>

        <!--end: Datatable-->
    </div>
</div> --}}

<div class="card card-custom mb-5">
    {{-- Body --}}
    <div class="card-body d-flex flex-column p-5">
        <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
            <div class="d-flex flex-column mr-2">
                <a href="{{url('/TaxSerialNumber')}}" class="text-dark-75 text-hover-primary font-weight-bolder font-size-h5">Daftar Monitor Persediaan Barang</a>
                <span class="text-muted font-weight-bold mt-2">Tabel Monitor Persediaan Barang</span>
            </div>
        </div>
        <!--begin::Search Form-->
        <div class="mb-5 row align-items-center">
            <div class="col-md-3">
                <div class="input-icon mr-3 mt-8">
                    <input type="text" class="form-control" placeholder="Search..." id="list_monitor_search_query"/>
                    <span>
                        <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <label class="mr-3 mb-2">Kode Supplier :</label>
                <select class="form-control select2" id="list_monitor_search_supplier">
                    <option value="">All</option>
                    @foreach($kodeSP as $kodeSupplier)
                    <option value="{{$kodeSupplier->value_spesifikasi}}">{{ucwords($kodeSupplier->value_spesifikasi)}}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="col-md-3">
                <label class="mr-3 mb-2">Lokasi :</label>
                <select class="form-control select2" id="list_monitor_search_index">
                    <option value="">All</option>
                    @foreach($listIndex as $index)
                    <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="mb-2">Status Stock :</label>
                <select class="form-control select2" id="list_monitor_search_status">
                    <option value="">All</option>
                    <option value="Stok Menipis">Stok Menipis</option>
                    <option value="Stok Minus">Stok Minus</option>
                    <option value="Stok Melebihi Batas">Stok Melebihi Batas</option>
                    <option value="Kosong">Kosong</option>
                </select>
            </div>
        </div>
        <!--end: Search Form-->

        <!--begin: Datatable-->

        <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="list_monitor"></div>

        <!--end: Datatable-->
    </div>
</div>

<div id="modal_detail_lokasi" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">

                <h5 class="modal-title" id="txtNamaLokasi"></h5>
            </div>
            <div class="modal-body">
                <div class="mb-7">
                    <div class="row align-items-center">
                        <div class="col-lg-12 col-xl-8">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="align-items-center">
                                        <label style="display: inline-block;"></label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="table_lokasi_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Search Form-->
                <!--end: Search Form-->
                <!--begin: Datatable-->

                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_lokasi"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


@if(Auth::user()->user_group == "super_admin" || Auth::user()->user_group == "admin")
<div class="card card-custom mb-5">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<div class="card-title">
			<div class="card-label">
				<div class="font-size-xl font-weight-boldest">STATUS PENJUALAN</div>
				<div class="font-size-sm text-muted mt-2">Total Tagihan Rp <span id="ttlTagihanPenjualan">0</span> dari <span id="ttlFakturPenjualn">0</span> Faktur</div>
			</div>
		</div>
		<div class="card-toolbar">
			<a href="#" class="btn btn-sm btn-light font-weight-bold mr-2" id="dashboard_daterangepicker" data-toggle="tooltip" title="" data-placement="left" data-original-title="Pilih Tanggal">
				<span class="text-muted font-size-base font-weight-bold mr-2" id="dashboard_daterangepicker_title"></span>
				<span class="text-primary font-size-base font-weight-bolder" id="dashboard_daterangepicker_date"></span>
			</a>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body d-flex flex-column">
		<!--begin::Chart-->
		<div class="d-flex align-items-center mb-5 bg-light-success rounded p-5">
		    <span class="svg-icon svg-icon-success mr-5">
				<span class="svg-icon svg-icon-lg">
					<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
					{{ Metronic::getSVG("media/svg/icons/Navigation/Double-check.svg", "svg-icon-xl svg-icon-success d-block my-2") }}
					<!--end::Svg Icon-->
				</span>
			</span>

		<div class="d-flex flex-column flex-grow-1 mr-2">
			<a href="#" onclick="getDataTagihan('lunas'); return false;" class="font-weight-bolder text-hover-primary text-dark-75 font-size-lg">TAGIHAN SUDAH TERBAYAR (LUNAS)</a>

		    <div class="progress progress-xs mt-2 mb-2 bg-success-o-60">
				<div class="progress-bar bg-success" role="progressbar" id="persentaseTagihanLunas" style="width: 00%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
        		</div>

			<span class="text-dark-50 font-weight-bold">Rp&nbsp;<span id="ttlTagihanLunas">0</span> dari&nbsp;<span id="jmlTagihanLunas">0</span>&nbsp;faktur</span>
    		</div>

		<span class="font-weight-bolder text-success py-1 font-size-lg" id="persentaseTagihan1">0%</span>
    	</div>

    	<div class="d-flex align-items-center mb-5 bg-light-warning rounded p-5">
		    <span class="svg-icon svg-icon-warning mr-5">
				<span class="svg-icon svg-icon-lg">
					<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
					{{ Metronic::getSVG("media/svg/icons/Code/Warning-2.svg", "svg-icon-xl svg-icon-warning d-block my-2") }}
					<!--end::Svg Icon-->
				</span>
			</span>

		<div class="d-flex flex-column flex-grow-1 mr-2">
			<a href="#" onclick="getDataTagihan('belum_lunas'); return false;" class="font-weight-bolder text-hover-primary text-dark-75 font-size-lg">TAGIHAN BELUM DIBAYAR</a>

		    <div class="progress progress-xs mt-2 mb-2 bg-warning-o-60">
				<div class="progress-bar bg-warning" role="progressbar" id="persentaseTagihanBelumLunas" style="width: 0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
        		</div>

			<span class="text-dark-50 font-weight-bold">Rp&nbsp;<span id="ttlTagihanBelumLunas">0</span> dari&nbsp;<span id="jmlTagihanBelumLunas">0</span>&nbsp;faktur</span>
    		</div>

		<span class="font-weight-bolder text-warning py-1 font-size-lg" id="persentaseTagihan2">0%</span>
    	</div>

    	<div class="d-flex align-items-center mb-5 bg-light-danger rounded p-5">
		    <span class="svg-icon svg-icon-danger mr-5">
				<span class="svg-icon svg-icon-lg">
					<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
					{{ Metronic::getSVG("media/svg/icons/Code/Stop.svg", "svg-icon-xl svg-icon-danger d-block my-2") }}
					<!--end::Svg Icon-->
				</span>
			</span>

		<div class="d-flex flex-column flex-grow-1 mr-2">
			<a href="#" onclick="getDataTagihan('jatuh_tempo'); return false;" class="font-weight-bolder text-hover-primary text-dark-75 font-size-lg">TAGIHAN MENUNGGAK (HUTANG JATUH TEMPO)</a>

		    <div class="progress progress-xs mt-2 mb-2 bg-danger-o-60">
				<div class="progress-bar bg-danger" role="progressbar" id="persentaseTagihanJT" style="width: 0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
        		</div>

			<span class="text-dark-50 font-weight-bold">Rp&nbsp;<span id="ttlTagihanJT">0</span> dari&nbsp;<span id="jmlTagihanJT">0</span>&nbsp;faktur</span>
    		</div>

		<span class="font-weight-bolder text-danger py-1 font-size-lg" id="persentaseTagihan3">0%</span>
    	</div>

		<div class="d-flex align-items-center mb-5 bg-light-primary rounded p-5">
		    <span class="svg-icon svg-icon-primary mr-5">
				<span class="svg-icon svg-icon-lg">
					<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
					{{ Metronic::getSVG("media/svg/icons/Code/Time-schedule.svg", "svg-icon-xl svg-icon-primary d-block my-2") }}
					<!--end::Svg Icon-->
				</span>
			</span>

		<div class="d-flex flex-column flex-grow-1 mr-2">
			<a href="#" onclick="getDataTagihan('belum_jt'); return false;" class="font-weight-bolder text-hover-primary text-dark-75 font-size-lg">TAGIHAN BELUM JATUH TEMPO</a>

		    <div class="progress progress-xs mt-2 mb-2 bg-primary-o-60">
				<div class="progress-bar bg-primary" role="progressbar" id="persentaseTagihanBelumJT" style="width: 0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
        		</div>

			<span class="text-dark-50 font-weight-bold">Rp&nbsp;<span id="ttlTagihanBelumJT">0</span> dari&nbsp;<span id="jmlTagihanBelumJT">0</span>&nbsp;faktur</span>
    		</div>

		<span class="font-weight-bolder text-primary py-1 font-size-lg" id="persentaseTagihan4">0%</span>
    	</div>
		<!--end::Chart-->
	</div>
	<!--end::Body-->
    <!-- Modal form list tagihan -->
    <div id="modal_list_invoice" class="modal fade">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">

                    <h5 class="modal-title">List Tagihan</h5>
                </div>
                <div class="modal-body">
                    <form >
                        <input type="hidden" id="startDate">
                        <input type="hidden" id="endDate">
                        <table class="datatable-bordered datatable-head-custom ml-4" id="list_invoice" width="100%">
                            <thead>
                                <tr>
                                    <th align="center" style="text-align:center;">No. Invoice</th>
                                    <th align="center" style="text-align:left;">Customer</th>
                                    <th align="center" style="text-align:center;">Tanggal Invoice</th>
                                    <th align="center" style="text-align:center;">Tanggal JT</th>
                                    <th align="center" style="text-align:right;">Nominal</th>
                                </tr>
                            </thead>
                            <tbody id="list_invoice_body">

                            </tbody>
                        </table>
                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /form list tagihan -->
</div>
@endif
<div class="card card-custom mb-5">
    <!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<div class="card-title">
			<div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
        		<span class="symbol symbol-50 symbol-light-success mr-2">
        			<span class="symbol-label">
        				<span class="svg-icon svg-icon-3x svg-icon-success">
        					{{ Metronic::getSVG("media/svg/icons/Shopping/Rupiah-success.svg", "svg-icon-3x svg-icon-danger d-block my-2") }}
        				</span>
        			</span>
        		</span>
        		<div class="d-flex flex-column">
				<span class="text-dark-75 font-weight-bolder font-size-h3">Rp&nbsp;<span id="txtOmzet">0</span></span>
        			<span class="text-muted font-weight-bold mt-1">Total&nbsp;Penjualan</span>
        		</div>
        	</div>
		</div>
		<div class="card-toolbar">
			<ul class="nav nav-pills nav-pills-sm nav-success">
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartOmzet active" data-toggle="tab" href="#kt_tab_pane_11_1">Bulanan</a>
    			</li>
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartOmzet" data-toggle="tab" href="#kt_tab_pane_11_2">Mingguan</a>
    			</li>
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartOmzet" data-toggle="tab" href="#kt_tab_pane_11_3">Harian</a>
    			</li>
    		</ul>
		</div>
	</div>
	<!--end::Header-->

	<!--begin::Body-->
	<div class="card-body p-0">
		<div id="kt_stats_widget_11_chart_custom_verstand" class="card-rounded-bottom" data-color="success" style="height: 150px; min-height: 150px;"></div>
	<div class="resize-triggers"><div class="expand-trigger"><div style="width: 386px; height: 258px;"></div></div><div class="contract-trigger"></div></div></div>
	<!--end::Body-->
</div>

<div class="card card-custom mb-5">
    <!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<div class="card-title">
			<div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
        		<span class="symbol symbol-50 symbol-light-warning mr-2">
        			<span class="symbol-label">
        				<span class="svg-icon svg-icon-3x svg-icon-warning">
        					{{ Metronic::getSVG("media/svg/icons/Shopping/Money.svg", "svg-icon-3x svg-icon-danger d-block my-2") }}
        				</span>
        			</span>
        		</span>
        		<div class="d-flex flex-column">
				<span class="text-dark-75 font-weight-bolder font-size-h3">Rp&nbsp;<span id="txtProfit">0</span></span>
        			<span class="text-muted font-weight-bold mt-1">Total&nbsp;Keuntungan</span>
        		</div>
        	</div>
		</div>
		<div class="card-toolbar">
			<ul class="nav nav-pills nav-pills-sm nav-warning">
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartProfit active" data-toggle="tab" href="#kt_tab_pane_11_1">Bulanan</a>
    			</li>
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartProfit" data-toggle="tab" href="#kt_tab_pane_11_2">Mingguan</a>
    			</li>
    			<li class="nav-item">
				<a class="nav-link py-2 px-4 btnChartProfit" data-toggle="tab" href="#kt_tab_pane_11_3">Harian</a>
    			</li>
    		</ul>
		</div>
	</div>
	<!--end::Header-->

	<!--begin::Body-->
	<div class="card-body p-0">
		<div id="kt_stats_widget_12_chart_custom_verstand" class="card-rounded-bottom" data-color="warning" style="height: 150px; min-height: 150px;"></div>
	<div class="resize-triggers"><div class="expand-trigger"><div style="width: 386px; height: 258px;"></div></div><div class="contract-trigger"></div></div></div>
	<!--end::Body-->
</div>

