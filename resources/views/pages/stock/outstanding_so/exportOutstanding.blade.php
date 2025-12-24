
    <table>
        <tr>
            <td colspan="7" align="center">Outstanding SO</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th align="center">Tanggal SO</th>
                <th align="center">Nomor Transaksi</th>
                <th align="center">Nama Pelanggan</th>
                <th align="center">Nama Barang</th>
                <th align="center">Jumlah Outstanding</th>
                <th align="center">Satuan</th>
                <th align="center">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataOutstanding as $row)
            <tr>

                <td align="center">{{$row->tanggal_so}}</td>
                <td align="center">{{strtoupper($row->no_so)}}</td>
                <td align="left">{{$row->nama_customer}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="right">{{number_format($row->qty_outstanding)}}</td>
                <td align="center">{{strtoupper($row->nama_satuan)}}</td>
                <td align="right">{{number_format($row->harga_jual)}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
