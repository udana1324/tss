<table>
    <thead>
        <tr>
            <th align="center">Nomor SO</th>
            <th align="center">Pelanggan</th>
            <th align="center">PO Pelanggan</th>
            <th align="center">Tanggal Penjualan</th>
            <th align="center">Jumlah</th>
            <th align="center">Outstanding</th>
            <th align="center">Total (Rp)</th>
            <th align="center">Metode Pembayaran</th>
            <th align="center">Tenor</th>
            <th align="center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataLaporan as $row)
        <tr>
            <td align="left">{{strtoupper($row->no_so)}}</td>
            <td align="left">{{strtoupper($row->nama_customer)}}</td>

            @if ($row->no_po_customer != "")
                <td align="center">{{strtoupper($row->no_po_customer)}}</td>
            @else
                <td align="center">-</td>
            @endif
            <td align="center">{{ $row->tanggal_so}}</td>
            <td align="center">{{$row->jumlah_total_so}}</td>
            <td align="center">{{$row->outstanding_so}}</td>
            <td align="right">{{$row->nominal_so_ttl}}</td>
            <td align="center">{{strtoupper($row->metode_pembayaran)}}</td>
            @if ($row->metode_pembayaran == "credit")
                <td align="left">{{$row->durasi_jt}} Hari</td>
            @else
            <td align="center">-</td>
            @endif
            <td align="center">{{strtoupper($row->status_so)}}</td>
        </tr>

        @endforeach
    </tbody>
</table>
