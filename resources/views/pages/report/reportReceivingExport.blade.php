
    <table>
        <tr>
            <td colspan="9" align="center">Mutasi Barang Masuk</td>
        </tr>
        <tr>
            <td colspan="9" align="center">Periode</td>
        </tr>
        <tr>
            <td colspan="9" align="center">{{$periode}}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="2" align="center">Nomor Transaksi</th>
                <th align="center">Tanggal</th>
                <th colspan="2" align="center">Nama Barang</th>
                <th align="center">Qty</th>
                <th align="center">Satuan</th>
                <th align="center">Harga</th>
                <th align="center">Nama Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>

                <td align="center">{{strtoupper($row->no_po)}}</td>
                <td align="center">{{strtoupper($row->kode_penerimaan)}}</td>
                <td align="center">{{ Carbon\Carbon::parse($row->tgl_transaksi)->isoFormat('D MMM Y') }}</td>
                <td align="center">{{strtoupper($row->kode_item)}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="right">{{strtoupper($row->qty_item)}}</td>
                <td align="right">{{strtoupper($row->nama_satuan)}}</td>
                <td align="center">{{strtoupper($row->harga_beli)}}</td>
                <td align="left">{{$row->nama_supplier}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="8" align="center">Total Barang Masuk</td>
            <td align="right">{{$dataLaporan->sum('qty_item')}}</td>
        </tr>
        <tr>
            <td colspan="8" align="center">Total Surat Jalan</td>
            <td align="right">{{$dataLaporan->count()}}</td>
        </tr>
    </table>
