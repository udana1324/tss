<table>
    <tr>
        <th align="center">No</th>
        <th align="center">Tanggal Invoice</th>
        <th align="center">No. Faktur</th>
        <th align="center">Nama Customer</th>
        <th align="center" colspan="2">Qty</th>
        <th align="center">Nama Barang</th>
        <th align="center">Harga Jual</th>
        <th align="center">Grand Total</th>
        <th align="center">DPP</th>
        <th align="center">PPn</th>
    </tr>
    @foreach ($dataLaporan as $row)
    <tr>
        <th align="center">{{$loop->iteration}}</th>
        <td align="center">{{$row->tanggal_invoice}}</td>
        <td align="left">{{strtoupper($row->kode_invoice)}}</td>
        <th align="left"><b>{{strtoupper($row->nama_customer)}}</b></th>
        <td align="center">{{$row->qty_item}}</td>
        <td align="center">{{strtoupper($row->nama_satuan)}}</td>
        <td align="left">{{strtoupper($row->nama_item)}}</td>
        @if($row->flag_ppn == "I") 
            <td align="right">{{$row->harga_jual / $ppnPercentage}}</td>
            <td align="right">{{($row->harga_jual / $ppnPercentage) * $row->qty_item + ((($row->harga_jual / $ppnPercentage) * $row->qty_item) * $ppnExcl) }}</td>
            <td align="right">{{($row->harga_jual / $ppnPercentage) * $row->qty_item}}</td>
            <td align="right">{{(($row->harga_jual / $ppnPercentage) * $row->qty_item) * $ppnExcl}}</td>
        @else
            <td align="right">{{$row->harga_jual}}</td>
            <td align="right">{{($row->harga_jual * $row->qty_item) + (($row->harga_jual * $row->qty_item) * $ppnExcl)}}</td>
            <td align="right">{{$row->harga_jual * $row->qty_item}}</td>
            <td align="right">{{($row->harga_jual * $row->qty_item) * $ppnExcl}}</td>
        @endif

    </tr>

    @endforeach
</table>
