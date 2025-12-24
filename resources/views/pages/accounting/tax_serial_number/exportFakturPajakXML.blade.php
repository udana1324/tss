<?xml version="1.0" encoding="utf-8" ?>
<TaxInvoiceBulk xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="TaxInvoice.xsd">
	<TIN>{{str_replace(['.', '-'], '', '0'.$data['dataPreference']->npwp_pt)}}</TIN>
	<ListOfTaxInvoice>
        @foreach ($data['dataExport'] as $dataFP)
        <TaxInvoice>
			<TaxInvoiceDate>{{$dataFP['tanggal_faktur']}}</TaxInvoiceDate>
			<TaxInvoiceOpt>Normal</TaxInvoiceOpt>
			<TrxCode>04</TrxCode>
			<AddInfo/>
			<CustomDoc/>
			<CustomDocMonthYear/>
			<RefDesc>{{strtoupper($dataFP['kode_invoice'])}}</RefDesc>
			<FacilityStamp/>
			<SellerIDTKU>{{str_replace(['.', '-'], '', '0'.$data['dataPreference']->npwp_pt)}}000000</SellerIDTKU>
			<BuyerTin>{{str_replace(['.', '-'], '', $dataFP['npwp_customer'])}}</BuyerTin>
			<BuyerDocument>{{$dataFP['dokumen_customer']}}</BuyerDocument>
			<BuyerCountry>IND</BuyerCountry>
            @if ($dataFP['jenis_customer'] == "I")
			<BuyerDocumentNumber>{{$dataFP['ktp_customer']}}</BuyerDocumentNumber>
            @else
            <BuyerDocumentNumber/>
            @endif
			<BuyerName>{{$dataFP['nama_customer']}}</BuyerName>
			<BuyerAdress>{{$dataFP['txtAlamat']}}</BuyerAdress>
			<BuyerEmail></BuyerEmail>
			@if ($dataFP['jenis_customer'] == "I")
			<BuyerIDTKU>{{$dataFP['ktp_customer']}}000000</BuyerIDTKU>
            @else
            <BuyerIDTKU>{{str_replace(['.', '-'], '', $dataFP['npwp_customer'])}}000000</BuyerIDTKU>
            @endif
			<ListOfGoodService>
                @foreach ($dataFP['detailFaktur'] as $dataDetail)
                <GoodService>
					<Opt>A</Opt>
					<Code>{{$dataDetail['kode_kategori_pajak'] ?? "000000";}}</Code>
					<Name>{{str_replace(['"', "'"], '', $dataDetail['nama_item'])}}</Name>
                    @if ($dataFP['flag_ppn'] == "I")
                        @php
                            $hargaSatuan = round($dataDetail['harga_jual'] / $data['ppnPercentageInc'], 2);
                        @endphp
                    @else
                        @php
                            $hargaSatuan = round($dataDetail['harga_jual'], 2);
                        @endphp
                    @endif

                    @php
                        $hargaTotal = $dataDetail['qty'] * $hargaSatuan;
                        $diskon = $hargaTotal * $dataFP['diskon'] / 100;
                        $dppProduk = $hargaTotal - $diskon;
                        $ppnProduk = $dppProduk * $data['ppnPercentageExc'];

                        $dppLain = floor($dppProduk  / 12 * 11);

                        $ppnLain = round($dppLain * 12 / 100);


                    @endphp

					<Unit>{{$dataDetail['kode_satuan_pajak'] ?? "UM.0033";}}</Unit>
					<Price>{{$hargaSatuan}}</Price>
					<Qty>{{$dataDetail->qty}}</Qty>
					<TotalDiscount>{{$diskon}}</TotalDiscount>
					<TaxBase>{{$dppProduk}}</TaxBase>
					<OtherTaxBase>{{$dppLain}}</OtherTaxBase>
					<VATRate>12</VATRate>
					<VAT>{{$ppnLain}}</VAT>
					<STLGRate>0</STLGRate>
					<STLG>0</STLG>
				</GoodService>
                @endforeach
			</ListOfGoodService>
		</TaxInvoice>
        @endforeach
	</ListOfTaxInvoice>
</TaxInvoiceBulk>
