@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
                <div class="card card-custom position-relative overflow-hidden">
                    <form action="{{ route('FakturPajak.Posting', $dataInv->id) }}" class="form-horizontal" id="form_add" method="POST">
                        {{ csrf_field() }}
					<!--begin::Invoice header-->
					<div class="row justify-content-center py-4 px-4 py-md-18 px-md-0 bg-primary ribbon ribbon-top ribbon-ver">
                            @if ($dataInv->flag_batal == 2)
                                <div class="ribbon-target ribbon-xl bg-warning" style="top: -2px; right: 50px;">Diganti<br></div>
                            @elseif ($dataInv->flag_batal == 1)
                                <div class="ribbon-target ribbon-xl bg-danger" style="top: -2px; right: 50px;">Batal<br></div>
                            @elseif ($dataInv->pembetulan == 1 && $dataInv->id_parent != "")
                                <div class="ribbon-target ribbon-xl bg-warning" style="top: -2px; right: 50px;">Normal-Pengganti<br></div>
                            @elseif ($dataInv->pembetulan == 0 && $dataInv->id_parent == "")
                                <div class="ribbon-target ribbon-xl bg-success" style="top: -2px; right: 50px;">Normal<br></div>
                            @endif
						<div class="col-md-9">
							<div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row">
								<div class="d-flex flex-column px-0 order-2 order-md-1">
								    <span class="d-flex flex-column font-size-h5 font-weight-bold text-white">
										<span>{{strtoupper($dataPreference->nama_pt ?? "-")}}</span>
										<span>{{strtoupper($dataPreference->alamat ?? "-")}}</span>
									</span>
								</div>
								<div class="order-1 order-md-2">
								    <h1 class="display-3 font-weight-bolder text-white">FAKTUR PAJAK PENJUALAN</h1><br>
                                        <span class="font-weight-bolder font-size-h3 text-white mr-5 mb-2">#{{$dataInv->jenis_faktur}}{{$dataInv->pembetulan}}.{{$dataInv->nomor_faktur}}</span><br />
								    <span class="font-weight-bolder font-size-h3 text-white mr-5 mb-2">#{{strtoupper($dataInv->kode_invoice)}}</span><br />
								    <span class="font-weight-bolder font-size-h3 text-white mr-5 mb-2">#{{strtoupper($dataInv->no_so)}}</span>
								</div>
							</div>
						</div>
					</div>
					<!--end::Invoice header-->
					<div class="row justify-content-center py-4 px-4 py-md-15">
						<div class="col-lg-10">
							<!--begin::Invoice body-->
							<div class="row pb-10">
								<div class="col-md-3 border-right-md">
									<!--begin::Invoice To-->
									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Customer :</div>
									<div class="font-size-lg font-weight-bold mb-10">
									    <span class="font-weight-boldest">{{strtoupper($dataInv->nama_customer)}}</span>
									    <br>{{ucwords($dataAlamat->alamat_customer)}}
									</div>
									<!--end::Invoice To-->

									<!--begin::Tanggal Invoice-->
									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Tanggal Faktur Pajak</div>
									<div class="font-size-lg font-weight-bold mb-10">{{\Carbon\Carbon::parse($dataInv->tanggal_faktur)->isoFormat('D MMMM Y')}}</div>
									<!--end::Tanggal Invoice-->
								</div>

							    <div class="col-md-9">
                                        <table class="table table-separate table-head-custom" id="table_product">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center">Nama Barang</th>

                                                    <th style="text-align: center">Qty

                                                    <th style="text-align: center">Harga Jual</th>

                                                    <th style="text-align: center">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detailFaktur as $data)
                                                @if ($dataInv->flag_ppn == "I")
                                                    @php
                                                        $hargaSatuan = $data->harga_jual / $ppnPercentageInc;
                                                    @endphp
                                                @else
                                                    @php
                                                        $hargaSatuan = $data->harga_jual;
                                                    @endphp
                                                @endif

                                                @php
                                                    $hargaTotal = $data->qty * $hargaSatuan;
                                                @endphp
                                                <tr>
                                                    <td>{{strtoupper($data->kode_item)}}-{{strtoupper($data->nama_item)}}</td>
                                                    <td style="text-align: center">{{number_format($data->qty, 2, ',', '.')}}</td>
                                                    <td style="text-align: right">Rp {{number_format($hargaSatuan, 2, ',', '.')}}</td>
                                                    <td style="text-align: right">Rp {{number_format($hargaTotal, 2, ',', '.')}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
							</div>
							<!--end::Invoice body-->

							<!--begin::Invoice footer-->
							<div class="row">
								<div class="col-md-5 pt-7 pb-5 pb-md-9">
                                        <div class="d-flex flex-column flex-md-row">
										<div class="d-flex flex-column">

											<div class="font-size-lg mt-15">
												<span class="font-weight-boldest mr-15">Catatan Faktur Pajak:</span><br>
											    <span class="mr-15">Pengganti FP : {{$dataInv->jenis_faktur_parent}}{{$dataInv->pembetulan_parent}}.{{$dataInv->nomor_faktur_parent}}</span><br>
                                                    <span class="mr-15">Tanggal FP : {{\Carbon\Carbon::parse($dataInv->tanggal_faktur_parent)->isoFormat('D MMMM Y')}}</span>
											</div>


										</div>
									</div>
								</div>
								<div class="col-md-7 pt-md-25">
								    <div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
										<div class="font-weight-boldest font-size-h5">Total Penjualan</div>
										<div class="text-right d-flex flex-column">
											<span class="font-weight-boldest font-size-h3 line-height-sm" id="qty_new">{{number_format($dataInv->ttl_qty, 2, ',', '.')}}</span>
										</div>
									</div>

									<div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
										<div class="font-weight-boldest font-size-h5">Total DPP</div>
										<div class="text-right d-flex flex-column">
											<span class="font-weight-boldest font-size-h3 line-height-sm" id="dpp_nominal_new">{{number_format($dataInv->dpp, 2, ',', '.')}}</span>
										</div>
									</div>

									<div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
										<div class="font-weight-boldest font-size-h5">Total PPN</div>
										<div class="text-right d-flex flex-column">
											<span class="font-weight-boldest font-size-h3 line-height-sm" id="ppn_nominal_new">{{number_format($dataInv->ppn, 2, ',', '.')}}</span>
										</div>
									</div>

									<div class="bg-primary rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
										<!--begin::Shape-->
										<div class="position-absolute opacity-30 top-0 right-0">
											<span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">
									<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/shapes/abstract-8.svg-->
									{{ Metronic::getSVG("media/svg/shapes/abstract-8.svg") }}
									<!--end::Svg Icon-->
											</span>
										</div>
										<!--end::Shape-->
										<div class="font-weight-boldest font-size-h5">Grand Total</div>
										<div class="text-right d-flex flex-column">
											<span class="font-weight-boldest font-size-h3 line-height-sm" id="gt_nominal_new">{{number_format($dataInv->grand_total, 2, ',', '.')}}</span>
											@php
											if ($dataInv->flag_ppn == "I"){
											    $txtPPN = 'Sudah termasuk PPn';
											}
											elseif ($dataInv->flag_ppn == "Y"){
											    $txtPPN = 'Sudah termasuk PPn';
											}
											else{
											    $txtPPN = 'Tanpa PPN';
											}
											@endphp
											<span class="font-size-sm">{{$txtPPN}}</span>
										</div>
									</div>
								</div>
							</div>
							<!--end::Invoice footer-->
						</div>
					</div>
					<!-- begin: Invoice action-->
					<div class="row justify-content-center border-top py-4 px-4 py-md-14 px-md-0">
					    <div class="d-flex justify-content-between align-items-center col-10">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                                </div>

                                <div class="mt-2 mt-sm-0">
                                    <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                    @if($dataInv->status_invoice == "posted")
                                        @if($hakAkses->approve == "Y" && $dataInv->flag_batal == 0)
                                            <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_batal" value="batal">Batal<i class="fas fa-file-signature ml-2"></i></button>
                                        @endif
                                        @if($hakAkses->revisi == "Y" && $dataInv->flag_batal == 0)
                                            <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_refresh" value="refresh">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                            <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Pembetulan<i class="fas fa-file-signature ml-2"></i></button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
					<!-- end: Invoice action-->
				</form>
				</div>

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

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

        $("#cancel").on('click', function(e) {
            var txt = "";
            var titleTxt = "";
            var status = "{{$dataInv->status_invoice}}";
            if (status == "draft") {
                titleTxt = "Batal?";
                txt = "Apakah anda ingin membatalkan posting invoice penjualan?";
            }
            else {
                titleTxt = "Keluar?";
                txt = "Apakah anda ingin kembali ke halaman utama?";
            }

            Swal.fire({
                title: titleTxt,
                text: txt,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesInvoice') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            var title = "";
            var txt = "";
            if (btn == "revisi") {
                title = "Revisi Faktur Pajak?";
                txt = "Apakah yakin ingin melakukan revisi Faktur Pajak Penjualan?<br />";
                txt += '(Faktur Pajak Pengganti akan digenerate dan status akan diupdate menjadi "Diganti")';
            }
            else if (btn == "batal") {
                title = "Pembatalan Faktur Pajak Penjualan";
                txt = "Apakah yakin ingin melakukan Pembatalan Faktur Pajak Penjualan?";
            }

            $("#submit_action").val(btn);

            Swal.fire({
                title: title,
                html: txt,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    e.preventDefault();
                    $("#form_add").off("submit").submit();
                }
                else if (result.dismiss === "cancel") {
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    e.preventDefault();
                }
            });
		});
	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
