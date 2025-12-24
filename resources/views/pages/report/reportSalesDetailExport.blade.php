
    <table>
        <tr>
            <td colspan="6" align="center">Laporan Penjualan Detail</td>
        </tr>
        <tr>
            <td colspan="6" align="center">Periode</td>
        </tr>
        <tr>
            <td colspan="6" align="center">{{$periode}}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th align="center">Tanggal</th>
                <th align="center">Nama Pelanggan</th>
                <th colspan="3" align="center">Nomor Transaksi</th>
                <th colspan="3" align="center">Item</th>
                <th align="center">Qty</th>
                <th align="center">Satuan</th>
                <th align="center">Harga</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>
                <td align="center">{{ Carbon\Carbon::parse($row->tanggal_invoice)->isoFormat('D MMM Y') }}</td>
                <td align="center">{{strtoupper($row->nama_customer)}}</td>
                <td align="center">{{strtoupper($row->kode_invoice)}}</td>
                <td align="center">{{strtoupper($row->no_so)}}</td>
                <td align="center">{{strtoupper($row->kode_pengiriman)}}</td>
                <td align="center">{{strtoupper($row->kode_item)}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="center">{{strtoupper($row->nama_kategori)}}</td>
                <td align="right">{{$row->qty_item}}</td>
                <td align="center">{{strtoupper($row->nama_satuan)}}</td>
                <td align="center">{{$row->harga_jual}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
