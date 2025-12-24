
    <table>
        <tr>
            <td colspan="9" align="center">Mutasi Barang Keluar</td>
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
                <th align="center">Nama Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>

                <td align="center">{{strtoupper($row->no_so)}}</td>
                <td align="center">{{strtoupper($row->kode_pengiriman)}}</td>
                <td align="center">{{ Carbon\Carbon::parse($row->tgl_transaksi)->isoFormat('D MMM Y') }}</td>
                <td align="center">{{strtoupper($row->kode_item)}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="right">{{strtoupper($row->qty_item)}}</td>
                <td align="right">{{strtoupper($row->nama_satuan)}}</td>
                <td align="center">{{strtoupper($row->harga_jual)}}</td>
                <td align="left">{{$row->nama_customer}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="8" align="center">Total Barang Keluar</td>
            <td align="right">{{$dataLaporan->sum('qty_item')}}</td>
        </tr>
        <tr>
            <td colspan="8" align="center">Total Surat Jalan</td>
            <td align="right">{{$dataLaporan->count()}}</td>
        </tr>
    </table>
