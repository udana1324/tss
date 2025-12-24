
    <table>
        <tr>
            <td colspan="5" align="center">Outstanding PO</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th align="center">Tanggal PO</th>
                <th align="center">Nomor Transaksi</th>
                <th align="center">Nama Supplier</th>
                <th align="center">Nama Barang</th>
                <th align="center">Jumlah Outstanding</th>
                <th align="center">Satuan</th>
                <th align="center">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataOutstanding as $row)
            <tr>

                <td align="center">{{$row->tanggal_po}}</td>
                <td align="center">{{strtoupper($row->no_po)}}</td>
                <td align="left">{{$row->nama_supplier}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="right">{{number_format($row->outstanding_qty)}}</td>
                <td align="center">{{strtoupper($row->nama_satuan)}}</td>
                <td align="right">{{number_format($row->harga_beli)}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
