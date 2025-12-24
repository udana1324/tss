
    <table>
        <tr>
            <td>FK</td>
            <td>KD_JENIS_TRANSAKSI</td>
            <td>FG_PENGGANTI</td>
            <td>NOMOR_FAKTUR</td>
            <td>MASA_PAJAK</td>
            <td>TAHUN_PAJAK</td>
            <td>TANGGAL_FAKTUR</td>
            <td>NPWP</td>
            <td>NAMA</td>
            <td>ALAMAT_LENGKAP</td>
            <td>JUMLAH_DPP</td>
            <td>JUMLAH_PPN</td>
            <td>JUMLAH_PPNBM</td>
            <td>ID_KETERANGAN_TAMBAHAN</td>
            <td>FG_UANG_MUKA</td>
            <td>UANG_MUKA_DPP</td>
            <td>UANG_MUKA_PPN</td>
            <td>UANG_MUKA_PPNBM</td>
            <td>REFERENSI</td>
            <td>KODE_DOKUMEN_PENDUKUNG</td>
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
        @foreach ($dataExport as $data)
            <tr>
                <td>{{$data['HeadRow']}}</td>
                <td>{{$data['HeadJenis']}}</td>
                <td>{{$data['FKPengganti']}}</td>
                <td>{{$data['nomor_faktur']}}</td>
                <td>{{$data['masa_pajak']}}</td>
                <td>{{$data['tahun_pajak']}}</td>
                <td>{{$data['tanggal_faktur']}}</td>
                <td>{{$data['npwp_customer']}}</td>
                <td>{{$data['nama_customer']}}</td>
                <td>{{$data['txtAlamat']}}</td>
                <td>{{$data['dpp']}}</td>
                <td>{{$data['ppn']}}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>{{$data['kode_invoice']}}</td>
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
                    <td>{{$dataDetail['nama_item']}} - {{$dataDetail['nama_satuan']}}</td>
                    @if ($data['flag_ppn'] == "I")
                        <td>{{number_format($dataDetail->harga_jual / ((100 + $taxSettings->ppn_percentage) / 100))}}</td>
                    @else
                        <td>{{number_format($dataDetail->harga_jual)}}</td>
                    @endif
                    <td>{{$dataDetail->qty}}</td>
                    @if ($data['flag_ppn'] == "I")
                        <td>{{number_format($dataDetail->qty * ($dataDetail->harga_jual / ((100 + $taxSettings->ppn_percentage) / 100)))}}</td>
                    @else
                        <td>{{number_format($dataDetail->qty * $dataDetail->harga_jual)}}</td>
                    @endif
                    <td>0</td>
                    @if ($data['flag_ppn'] == "I")
                        <td>{{round($dataDetail->qty * ($dataDetail->harga_jual / ((100 + $taxSettings->ppn_percentage) / 100)), 0)}}</td>
                    @else
                        <td>{{round($dataDetail->qty * $dataDetail->harga_jual, 0)}}</td>
                    @endif
                    @if ($data['flag_ppn'] == "I")
                        <td>{{round(($dataDetail->qty * ($dataDetail->harga_jual / ($taxSettings->ppn_percentage/100))) * $taxSettings->ppn_percentage/100, 0)}}</td>
                    @else
                        <td>{{round(($dataDetail->qty * $dataDetail->harga_jual) * $taxSettings->ppn_percentage/100 , 0)}}</td>
                    @endif
                    <td>0</td>
                    <td>0</td>
                </tr>
            @endforeach
        @endforeach
    </table>
