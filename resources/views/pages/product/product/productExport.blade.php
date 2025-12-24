<table>
    <tr>
        <th align="center">No</th>
        <th align="center">Kode Barang</th>
        <th align="center">Nama Barang</th>
        <th align="center">Merk</th>
        <th align="center">Kategori</th>
        <th align="center">Satuan</th>
    </tr>
    @foreach ($dataLaporan as $row)
    <tr>
        <td align="center">{{$loop->iteration}}</td>
        <td align="center">{{strtoupper($row->kode_item)}}</td>
        <td align="left">{{$row->nama_item}}</td>
        <td align="left">{{strtoupper($row->nama_merk)}}</td>
        <td align="left">{{strtoupper($row->nama_kategori)}}</td>
        <td align="center">{{strtoupper($row->nama_satuan)}}</td>
    </tr>

    @endforeach
</table>
