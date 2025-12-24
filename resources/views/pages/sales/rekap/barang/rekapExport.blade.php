
    <table>
        <tr>
            <td colspan="6" align="center">Ringkasan Penjualan berdasarkan Barang</td>
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
                <th colspan="2" align="center">Nama Barang</th>
                <th align="center">Satuan</th>
                <th align="center">Dipesan</th>
                <th align="center">Terkirim</th>
                <th align="center">Kurang Kirim</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>

                <td align="center">{{strtoupper($row->kode_item)}}</td>
                <td align="center">{{strtoupper($row->nama_item)}}</td>
                <td align="center">{{ucwords($row->nama_satuan)}}</td>
                <td align="right">{{$row->qty_item}}</td>
                <td align="right">{{$row->qty_item - $row->qty_outstanding}}</td>
                <td align="center">{{$row->qty_outstanding}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
