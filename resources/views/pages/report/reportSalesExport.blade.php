
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
                <th style="text-align:center; width:95px; height:36px"><b>TGL. INVOICE</b></th>
                <th style="text-align:center; width:130px;"><b>NO. INVOICE</b></th>
                <th style="text-align:center; width:130px;"><b>NO. SO</b></th>
                <th style="text-align:center; width:220px;"><b>NAMA PERUSAHAAN</b></th>
                <th style="text-align:center; width:130px;"><b>OUTLET</b></th>
                <th style="text-align:center; width:100px;"><b>DPP</b></th>
                <th style="text-align:center; width:100px;"><b>PPN</b></th>
                <th style="text-align:center; width:110px;"><b>TOTAL TAGIHAN</b></th>
                <th style="text-align:center; width:110px;"><b>NOMINAL BAYAR</b></th>
                <th style="text-align:center; width:100px;"><b>TGL. BAYAR</b></th>
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
                    $tanggal_bayar = "-";
                    if($row->tanggal != null){
                        $tanggal_bayar = Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM Y');
                    }
                @endphp
                <td>{{ Carbon\Carbon::parse($row->tanggal_invoice)->isoFormat('D MMM Y') }}</td>
                
                <td><b>{{strtoupper($row->kode_invoice)}}</b></td>
                <td>{{strtoupper($row->no_so)}}</td>
                <td>{{strtoupper($row->nama_customer)}}</td>
                <td>{{$row->nama_outlet == null ? "-" : strtoupper($row->nama_outlet)}}</td>
                @php
                    if($row->kode_invoice != $cek){
                    @endphp
                        <td>{{$row->dpp}}</td>
                        <td>{{$row->ppn}}</td>
                        <td><b>{{$row->grand_total}}</b></td>
                    @php
                    }
                    else{
                        $jmlfaktur = $jmlfaktur - 1;
                    @endphp
                        <td></td>
                        <td></td>
                        <td></td>
                    @php
                    }
                @endphp
                <td style="text-align:left;">{{$row->nominal_bayar}}</td>
                <td style="text-align:right;">{{$tanggal_bayar}}</td>
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
            <td colspan="7" align="center"><b>TOTAL PENJUALAN</b></td>
            <td align="right"><b>=SUM(H6:H{{$kolom}})</b></td>
        </tr>
        <tr>
            <td colspan="7" align="center"><b>TOTAL FAKTUR PENJUALAN</b></td>
            <td align="right"><b>{{$jmlfaktur}}</b></td>
        </tr>
    </table>
