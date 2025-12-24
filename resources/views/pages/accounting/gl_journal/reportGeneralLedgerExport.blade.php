
    <table>
        <tr>
            <td colspan="5" align="center">Buku Besar</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Periode</td>
        </tr>
        <tr>
            <td colspan="5" align="center">{{$periode}}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th align="left">Tanggal Transaksi</th>
                <th align="center">Keterangan</th>
                <th align="right">Debit</th>
                <th align="right">Kredit</th>
                <th align="right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldo = 0;
                $saldo1 = 0;
                $txtSaldo = "";
            @endphp
            @foreach ($dataLaporan as $row)

                @if ($row->debet == "-")
                    @php
                        $saldo = $saldo - $row->kredit;
                    @endphp
                @endif
                @if ($row->kredit == "-")
                    @php
                        $saldo = $saldo - $row->kredit;
                    @endphp
                @endif
                @php
                    $saldo1 = $saldo;
                    if ($saldo < 0) {
                        $saldo1 = $saldo * -1;
                        $txtSaldo = '('.number_format($saldo1, 2, ',', '.').')';
                    }
                    else {
                        $txtSaldo = '('.number_format($saldo1, 2, ',', '.').')';
                    }
                @endphp
                <tr>
                    <td align="center">{{ Carbon\Carbon::parse($row->tanggal_transaksi)->isoFormat('D MMM Y') }}</td>
                    <td align="center">{{ucwords($row->deskripsi)}}</td>
                    <td align="center">{{strtoupper($row->debet)}}</td>
                    <td align="right">{{strtoupper($row->kredit)}}</td>
                    <td align="right">{{ $txtSaldo }}</td>
                </tr>

            @endforeach
        </tbody>
    </table>
