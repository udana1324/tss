
    <table>
        <tr>
            <td colspan="10" align="center"><b>LAPORAN PENJUALAN</b></td>
        </tr>
        <tr>
            <td colspan="10" align="center"><b>PERIODE</b></td>
        </tr>
        <tr>
            <td colspan="10" align="center"><b>{{$periode}}</b></td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th style="text-align:center; width:130px;"><b>Ref. Transaksi</b></th>
                <th style="text-align:center; width:130px;"><b>Tanggal</b></th>
                <th style="text-align:center; width:220px;"><b>Customer</b></th>
                <th style="text-align:center; width:110px;"><b>Nominal</b></th>
            </tr>
        </thead>
        @php
            $cek = "";
            $kolom = 6;
            $jmlfaktur = 0;
        @endphp
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>
                @php
                    $totalDpp = 0;
                    $totalPpnProduk = 0;
                @endphp


                <td><b>{{strtoupper($row->no_ref)}}</b></td>
                <td>{{ Carbon\Carbon::parse($row->tanggal_penjualan)->isoFormat('DD MMMM Y HH:mm:ss') }}</td>
                <td>{{strtoupper($row->nama_customer)}}</td>
                <td style="text-align:right;">{{$row->nominal_total}}</td>
                @php
                    $cek = $row->kode_invoice;
                    $kolom = $kolom + 1;
                    $jmlfaktur = $jmlfaktur + 1;
                @endphp
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="3" align="center"><b>Total Penjualan</b></td>
            <td align="right"><b>=SUM(D6:D{{$kolom}})</b></td>
        </tr>
        <tr>
            <td colspan="3" align="center"><b>Total Transaksi Penjualan</b></td>
            <td align="right"><b>{{$jmlfaktur}}</b></td>
        </tr>
    </table>
