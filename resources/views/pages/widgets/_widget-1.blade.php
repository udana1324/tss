{{-- Mixed Widget 1 --}}

<div class="card card-custom mb-5">
    {{-- Header --}}
    <div class="card-header border-0 bg-danger py-5">
        <h3 class="card-title font-weight-bolder text-white">Pendapatan per Bulan (Tahun {{\Carbon\Carbon::now()->format('Y')}})</h3>
    </div>
    {{-- Body --}}
    <div class="card-body p-0 position-relative overflow-hidden">
        {{-- Chart --}}
        <div id="kt_mixed_widget_1_chart_custom_verstand" class="card-rounded-bottom bg-danger" style="height: 300px"></div>

        {{-- Stats --}}
        <div class="card-spacer mt-n25">
            {{-- Row --}}
            <div class="m-0">
                <div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
                    <div class="symbol symbol-40 symbol-primary mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-primary">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								    <!--! Font Awesome Free 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2022 Fonticons, Inc. -->
								    <path d="M.0003 64C.0003 46.33 14.33 32 32 32H112C191.5 32 256 96.47 256 176C256 234.8 220.8 285.3 170.3 307.7L221.7 436.1C228.3 452.5 220.3 471.1 203.9 477.7C187.5 484.3 168.9 476.3 162.3 459.9L106.3 320H64V448C64 465.7 49.67 480 32 480C14.33 480 0 465.7 0 448L.0003 64zM64 256H112C156.2 256 192 220.2 192 176C192 131.8 156.2 96 112 96H64V256zM400 160C461.9 160 512 210.1 512 272C512 333.9 461.9 384 400 384H352V480C352 497.7 337.7 512 320 512C302.3 512 288 497.7 288 480V192C288 174.3 302.3 160 320 160H400zM448 272C448 245.5 426.5 224 400 224H352V320H400C426.5 320 448 298.5 448 272z" fill="white" />
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
                    <a class="text-muted font-weight-bold font-size-h6">
                        TOTAL PIUTANG
                    </a>
                    <p class="text-primary font-weight-bold font-size-h6 mt-2" id="ttlPiutangDashboard">
                        Rp 0.00
                    </p>
                </div>
                <br>
                <div class="col bg-light-danger px-6 py-8 rounded-xl mb-7">
                    <div class="symbol symbol-40 symbol-light-danger mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-3x svg-icon-danger">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group-chat.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M3.28077641,9 L20.7192236,9 C21.2715083,9 21.7192236,9.44771525 21.7192236,10 C21.7192236,10.0817618 21.7091962,10.163215 21.6893661,10.2425356 L19.5680983,18.7276069 C19.234223,20.0631079 18.0342737,21 16.6576708,21 L7.34232922,21 C5.96572629,21 4.76577697,20.0631079 4.43190172,18.7276069 L2.31063391,10.2425356 C2.17668518,9.70674072 2.50244587,9.16380623 3.03824078,9.0298575 C3.11756139,9.01002735 3.1990146,9 3.28077641,9 Z M12,12 C11.4477153,12 11,12.4477153 11,13 L11,17 C11,17.5522847 11.4477153,18 12,18 C12.5522847,18 13,17.5522847 13,17 L13,13 C13,12.4477153 12.5522847,12 12,12 Z M6.96472382,12.1362967 C6.43125772,12.2792385 6.11467523,12.8275755 6.25761704,13.3610416 L7.29289322,17.2247449 C7.43583503,17.758211 7.98417199,18.0747935 8.51763809,17.9318517 C9.05110419,17.7889098 9.36768668,17.2405729 9.22474487,16.7071068 L8.18946869,12.8434035 C8.04652688,12.3099374 7.49818992,11.9933549 6.96472382,12.1362967 Z M17.0352762,12.1362967 C16.5018101,11.9933549 15.9534731,12.3099374 15.8105313,12.8434035 L14.7752551,16.7071068 C14.6323133,17.2405729 14.9488958,17.7889098 15.4823619,17.9318517 C16.015828,18.0747935 16.564165,17.758211 16.7071068,17.2247449 L17.742383,13.3610416 C17.8853248,12.8275755 17.5687423,12.2792385 17.0352762,12.1362967 Z" fill="#000000"/>
                                </g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
                    <a class="text-muted font-weight-bold font-size-h6 mt-2">
                        TOTAL HUTANG
                    </a>
                    <p class="text-danger font-weight-bold font-size-h6 mt-2" id="ttlHutangDashBoard">
                        Rp 0.00
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom mb-5">
	<!--begin::Header-->
	<div class="card-header border-0 bg-success">
		<div class="card-title">
			<div class="card-label">
				<div class="font-weight-bolder text-white">Highlights 30 Hari Terakhir</div>
				<div class="font-size-sm text-muted mt-2"></div>
			</div>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body p-0 position-relative overflow-hidden">
		<!--begin::Items-->
		<div class="flex-grow-1 card-spacer">
			<!--begin::Item-->
			<div class="d-flex align-items-center justify-content-between mb-10">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol symbol-40 symbol-primary mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-primary">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Home/Library.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								    <!--! Font Awesome Free 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2022 Fonticons, Inc. -->
								    <path d="M.0003 64C.0003 46.33 14.33 32 32 32H112C191.5 32 256 96.47 256 176C256 234.8 220.8 285.3 170.3 307.7L221.7 436.1C228.3 452.5 220.3 471.1 203.9 477.7C187.5 484.3 168.9 476.3 162.3 459.9L106.3 320H64V448C64 465.7 49.67 480 32 480C14.33 480 0 465.7 0 448L.0003 64zM64 256H112C156.2 256 192 220.2 192 176C192 131.8 156.2 96 112 96H64V256zM400 160C461.9 160 512 210.1 512 272C512 333.9 461.9 384 400 384H352V480C352 497.7 337.7 512 320 512C302.3 512 288 497.7 288 480V192C288 174.3 302.3 160 320 160H400zM448 272C448 245.5 426.5 224 400 224H352V320H400C426.5 320 448 298.5 448 272z" fill="white" />
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
					<div>
						<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Transaksi Penjualan</a>
						<div class="font-size-sm text-muted font-weight-bold mt-1">{{number_format($jmlOrder->value,0,',','.')}} faktur untuk {{number_format($jmlCust->value,0,',','.')}} pelanggan berbeda</div>
					</div>
				</div>
				<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base">Rp&nbsp;{{number_format($pemasukan->value,0,',','.')}}</div>
			</div>
			<!--end::Item-->
			<!--begin::Item-->
			<div class="d-flex align-items-center justify-content-between mb-10">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol symbol-40 symbol-light-warning mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-warning">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Devices/Mic.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                    </g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
					<div>
						<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Jumlah Pelanggan</a>
						<div class="font-size-sm text-muted font-weight-bold mt-1">Total {{number_format($ttlCust->value,0,',','.')}} Pelanggan</div>
					</div>
				</div>
				<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base">+&nbsp;{{number_format($newCust->value,0,',','.')}}&nbsp;Pelanggan&nbsp;Baru</div>
			</div>
			<!--end::Item-->
			<!--begin::Item-->
			<div class="d-flex align-items-center justify-content-between mb-10">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol symbol-40 symbol-light-success mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-success">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group-chat.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M3.28077641,9 L20.7192236,9 C21.2715083,9 21.7192236,9.44771525 21.7192236,10 C21.7192236,10.0817618 21.7091962,10.163215 21.6893661,10.2425356 L19.5680983,18.7276069 C19.234223,20.0631079 18.0342737,21 16.6576708,21 L7.34232922,21 C5.96572629,21 4.76577697,20.0631079 4.43190172,18.7276069 L2.31063391,10.2425356 C2.17668518,9.70674072 2.50244587,9.16380623 3.03824078,9.0298575 C3.11756139,9.01002735 3.1990146,9 3.28077641,9 Z M12,12 C11.4477153,12 11,12.4477153 11,13 L11,17 C11,17.5522847 11.4477153,18 12,18 C12.5522847,18 13,17.5522847 13,17 L13,13 C13,12.4477153 12.5522847,12 12,12 Z M6.96472382,12.1362967 C6.43125772,12.2792385 6.11467523,12.8275755 6.25761704,13.3610416 L7.29289322,17.2247449 C7.43583503,17.758211 7.98417199,18.0747935 8.51763809,17.9318517 C9.05110419,17.7889098 9.36768668,17.2405729 9.22474487,16.7071068 L8.18946869,12.8434035 C8.04652688,12.3099374 7.49818992,11.9933549 6.96472382,12.1362967 Z M17.0352762,12.1362967 C16.5018101,11.9933549 15.9534731,12.3099374 15.8105313,12.8434035 L14.7752551,16.7071068 C14.6323133,17.2405729 14.9488958,17.7889098 15.4823619,17.9318517 C16.015828,18.0747935 16.564165,17.758211 16.7071068,17.2247449 L17.742383,13.3610416 C17.8853248,12.8275755 17.5687423,12.2792385 17.0352762,12.1362967 Z" fill="#000000"/>
                                </g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
					<div>
						<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Transaksi Pembelian</a>
						<div class="font-size-sm text-muted font-weight-bold mt-1">{{number_format($jmlOrderPO->value,0,',','.')}} faktur dari {{number_format($jmlSupp->value,0,',','.')}} supplier berbeda</div>
					</div>
				</div>
				<div class="label label-light label-pill label-inline font-weight-bold text-dark-50">Rp&nbsp;{{number_format($pembelian->value,0,',','.')}}</div>
			</div>
			<!--end::Item-->
			<!--begin::Item-->
			<div class="d-flex align-items-center justify-content-between mb-10">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol symbol-40 symbol-light-danger mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-danger">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/General/Attachment2.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M3.5,21 L20.5,21 C21.3284271,21 22,20.3284271 22,19.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L10,7 L7.43933983,4.43933983 C7.15803526,4.15803526 6.77650439,4 6.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,19.5 C2,20.3284271 2.67157288,21 3.5,21 Z" fill="#000000" opacity="0.3"/>
                                        <path d="M12,13 C10.8954305,13 10,12.1045695 10,11 C10,9.8954305 10.8954305,9 12,9 C13.1045695,9 14,9.8954305 14,11 C14,12.1045695 13.1045695,13 12,13 Z" fill="#000000" opacity="0.3"/>
                                        <path d="M7.00036205,18.4995035 C7.21569918,15.5165724 9.36772908,14 11.9907452,14 C14.6506758,14 16.8360465,15.4332455 16.9988413,18.5 C17.0053266,18.6221713 16.9988413,19 16.5815,19 C14.5228466,19 11.463736,19 7.4041679,19 C7.26484009,19 6.98863236,18.6619875 7.00036205,18.4995035 Z" fill="#000000" opacity="0.3"/>
                                    </g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
					<div>
						<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Jumlah Vendor / Supplier</a>
						<div class="font-size-sm text-muted font-weight-bold mt-1">Total {{number_format($ttlSupp->value,0,',','.')}} Vendor</div>
					</div>
				</div>
				<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base">+&nbsp;{{number_format($newSupp->value,0,',','.')}}&nbsp;Vendor&nbsp;Baru</div>
			</div>
			<!--end::Item-->
			<!--begin::Item-->
			<div class="d-flex align-items-center justify-content-between">
				<div class="d-flex align-items-center mr-2">
					<div class="symbol symbol-40 symbol-light-info mr-3 flex-shrink-0">
						<div class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-info">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/General/Attachment2.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z" fill="#000000"/>
                                        <path d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z" fill="#000000" opacity="0.3"/>
                                    </g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</div>
					</div>
					<div>
						<a href="#" class="font-size-h6 text-dark-75 text-hover-primary font-weight-bolder">Jumlah Barang</a>
						<div class="font-size-sm text-muted font-weight-bold mt-1">Total {{number_format($ttlItem->value,0,',','.')}} Barang</div>
					</div>
				</div>
				<div class="label label-light label-inline font-weight-bold text-dark-50 py-4 px-3 font-size-base">+&nbsp;{{number_format($newItem->value,0,',','.')}}&nbsp;Barang&nbsp;Baru</div>
			</div>
			<!--end::Item-->
		</div>
		<!--end::Items-->
	</div>
	<!--end::Body-->
</div>

<div class="card card-custom mb-5 rankPembelian">
	<!--begin::Header-->
	<div class="card-header border-0 bg-primary">
		<div class="card-title">
			<div class="card-label">
				<div class="font-weight-bolder text-white">5 Pelanggan Terbaik Dalam 30 Hari</div>
				<div class="font-size-sm text-muted mt-2"></div>
			</div>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body p-0 position-relative overflow-hidden">
		<!--begin::Items-->
		<div class="flex-grow-1 card-spacer" id="rankCustomer">
			<!--begin::Item-->
			{{-- @foreach ($rankOfCustPurchase as $dataCustSale)
			<div class="d-flex justify-content-between mb-5">
				<div class="d-flex align-items-center mr-2">
					<div>
						<a href="#" class="font-size-md text-dark-75 text-hover-primary font-weight-bolder">{{$dataCustSale->nama_customer}}</a>
						<div class="font-size-xs text-muted font-weight-bold">{{ucwords($dataCustSale->kecamatan)}}, {{ucwords($dataCustSale->kota)}}</div>
					</div>
				</div>
				<div class="label label-light-primary label-inline font-weight-bold py-4 px-3 font-size-base">Rp&nbsp;{{number_format($dataCustSale->value,0,',','.')}}</div>
			</div>
            @endforeach --}}
			<!--end::Item-->
		</div>
		<!--end::Items-->
	</div>
	<!--end::Body-->
</div>

<div class="card card-custom mb-5 rankPembelian">
	<!--begin::Header-->
	<div class="card-header border-0 bg-info">
		<div class="card-title">
			<div class="card-label">
				<div class="font-weight-bolder text-white">5 Barang Terlaris Dalam 30 Hari</div>
				<div class="font-size-sm text-muted mt-2"></div>
			</div>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body p-0 position-relative overflow-hidden">
		<!--begin::Items-->
		<div class="flex-grow-1 card-spacer" id="rankItems">
			<!--begin::Item-->
			{{-- @foreach ($rankOfItemSale as $dataItemSale)
			<div class="d-flex justify-content-between mb-5">
				<div class="d-flex align-items-center mr-2">
					<div>
						<a href="#" class="font-size-md text-dark-75 text-hover-primary font-weight-bolder">{{$dataItemSale->nama_item}}</a>
						<div class="font-size-xs text-muted font-weight-bold">{{ucwords($dataItemSale->nama_kategori)}}, {{ucwords($dataItemSale->nama_merk)}}</div>
					</div>
				</div>
				<div class="label label-light-info label-inline font-weight-bold py-4 px-3 font-size-base">Rp&nbsp;{{number_format($dataItemSale->value,0,',','.')}}</div>
			</div>
            @endforeach --}}
			<!--end::Item-->
		</div>
		<!--end::Items-->
	</div>
	<!--end::Body-->
</div>


