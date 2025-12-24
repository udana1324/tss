<table>
    <tr>
        <td style="width:4px">FK</td>
        <td style="width:27px">KD_JENIS_TRANSAKSI</td>
        <td style="width:69px">FG_PENGGANTI</td>
        <td style="width:15px">NOMOR_FAKTUR</td>
        <td style="width:15px">MASA_PAJAK</td>
        <td style="width:12px">TAHUN_PAJAK</td>
        <td style="width:15px">TANGGAL_FAKTUR</td>
        <td style="width:14px">NPWP</td>
        <td style="width:26px">NAMA</td>
        <td style="width:69px">ALAMAT_LENGKAP</td>
        <td style="width:11px">JUMLAH_DPP</td>
        <td style="width:11px">JUMLAH_PPN</td>
        <td style="width:13px">JUMLAH_PPNBM</td>
        <td style="width:23px">ID_KETERANGAN_TAMBAHAN</td>
        <td style="width:14px">FG_UANG_MUKA</td>
        <td style="width:15px">UANG_MUKA_DPP</td>
        <td style="width:15px">UANG_MUKA_PPN</td>
        <td style="width:17px">UANG_MUKA_PPNBM</td>
        <td style="width:15px">REFERENSI</td>
        <td style="width:25px">KODE_DOKUMEN_PENDUKUNG</td>
    </tr>
    <tr>
        <td>LT</td>
        <td>NPWP</td>
        <td>NAMA</td>
        <td>JALAN</td>
        <td>BLOK</td>
        <td>NOMOR</td>
        <td>RT</td>
        <td>RW</td>
        <td>KECAMATAN</td>
        <td>KELURAHAN</td>
        <td>KABUPATEN</td>
        <td>PROPINSI</td>
        <td>KODE_POS</td>
        <td>NOMOR_TELEPON</td>
    </tr>
    <tr>
        <td>OF</td>
        <td>KODE_OBJEK</td>
        <td>NAMA</td>
        <td>HARGA_SATUAN</td>
        <td>JUMLAH_BARANG</td>
        <td>HARGA_TOTAL</td>
        <td>DISKON</td>
        <td>DPP</td>
        <td>PPN</td>
        <td>TARIF_PPNBM</td>
        <td>PPNBM</td>
    </tr>
     @php
        $row = 4;
    @endphp
    @foreach ($dataExport as $data)
        <tr>
            <td>{{$data['HeadRow']}}</td>
            <td>{{$data['HeadJenis']}}</td>
            <td>{{$data['FKPengganti']}}</td>
            <td>{{$data['nomor_faktur']}}</td>
            <td>{{$data['masa_pajak']}}</td>
            <td>{{$data['tahun_pajak']}}</td>
            <td>{{date("d/m/Y", strtotime($data['tanggal_faktur']))}}</td>
            <td>{{str_replace(['.', '-'], '', $data['npwp_customer'])}}</td>
            <td>{{$data['nama_customer']}}</td>
            <td>{{$data['txtAlamat']}}</td>
            @php
                $totalDpp = 0;
                $totalPpnProduk = 0;
            @endphp
            @foreach ($data['detailFaktur'] as $dataDetail)
                @if ($data['flag_ppn'] == "I")
                    @php
                        $hargaSatuan = $dataDetail->harga_jual / $ppnPercentageInc;
                    @endphp
                @else
                    @php
                        $hargaSatuan = $dataDetail->harga_jual;
                    @endphp
                @endif

                @php
                    $hargaTotal = round($dataDetail->qty * $hargaSatuan);
                    $diskon = $hargaTotal * $data['diskon'] / 100;
                    $dppProduk = $hargaTotal - $diskon;
                    $totalDpp = $totalDpp + $dppProduk;

                    $ppnProduk = round($dppProduk * $ppnPercentageExc);
                    $totalPpnProduk = $totalPpnProduk + $ppnProduk;
                @endphp
            @endforeach
            <td>{{$totalDpp}}</td>
            <td>{{$totalPpnProduk}}</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>{{strtoupper($data['kode_invoice'])}}</td>
        </tr>
        <tr>
            <td>FAPR</td>
            <td>{{strtoupper($dataPreference->nama_pt)}}</td>
            <td>{{ucwords($dataPreference->alamat_pt).", ".ucwords($dataPreference->kelurahan_pt).", ".ucwords($dataPreference->kecamatan_pt).", ".ucwords($dataPreference->kota_pt)}}</td>
        </tr>
        @foreach ($data['detailFaktur'] as $dataDetail)
            <tr>
                <td>OF</td>
                <td></td>
                <td>{{$dataDetail['nama_item']}}</td>

                @if ($data['flag_ppn'] == "I")
                    @php
                        $hargaSatuan = $dataDetail->harga_jual / $ppnPercentageInc;
                    @endphp
                @else
                    @php
                        $hargaSatuan = $dataDetail->harga_jual;
                    @endphp
                @endif

                @php
                    $hargaTotal = round($dataDetail->qty * $hargaSatuan);
                    $diskon = $hargaTotal * $data['diskon'] / 100;
                    $dppProduk = $hargaTotal - $diskon;
                    $ppnProduk = round($dppProduk * $ppnPercentageExc);
                @endphp

                <!--harga produk-->
                <td>{{$hargaSatuan}}</td>
                <!--End of harga produk-->

                <td>{{$dataDetail->qty}}</td>

                <!--Harga Total-->
                 <td>{{$hargaTotal}}</td>
                <!--End of Harga Total-->

                <!--Diskon-->
                <td>{{$diskon}}</td>
                <!--End of Diskon-->

                <!--DPP Produk-->
                <td>{{$dppProduk}}</td>
                <!--End of DPP Produk-->

                <!--PPN-->
                <td>{{$ppnProduk}}</td>
                <!--End of PPN-->

                <td>0</td>
                <td>0</td>
            </tr>
        @endforeach
    @endforeach
</table>
