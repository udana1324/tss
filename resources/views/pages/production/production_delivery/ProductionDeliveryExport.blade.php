<table>
    <tr>
        <th align="center">No</th>
        <th align="center">Supplier</th>
        <th align="center">Tanggal SJ</th>
        <th align="center">No. SJ</th>
        <th align="center">Item</th>
        <th align="center">Jumlah</th>
        <th align="center">Satuan</th>
        <th align="center">Status</th>
    </tr>
    @foreach ($dataLaporan as $row)
    <tr>
        <th align="center">{{$loop->iteration}}</th>
        <th align="left"><b>{{strtoupper($row->nama_supplier)}}</b></th>
        <td align="center">{{$row->tanggal_sj}}</td>
        <td align="left">{{strtoupper($row->kode_pengiriman)}}</td>
        <td align="left">{{strtoupper($row->nama_item)}}</td>
        <td align="center">{{$row->qty_item}}</td>
        <td align="center">{{strtoupper($row->nama_satuan)}}</td>
        <td align="center">{{strtoupper($row->status_pengiriman)}}</td>
    </tr>

    @endforeach
</table>
