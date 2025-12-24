
    <table>
        <tr>
            <td colspan="4" align="center">Kartu Stok</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                {{-- <th align="center">Kode Supplier</th> --}}
                <th align="center">Kode Barang</th>
                <th align="center">Nama Barang</th>
                <th align="center">Lokasi</th>
                <th align="center">Jumlah Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataStok as $row)
            <tr>

                {{-- <td align="center">{{strtoupper($row['value_spesifikasi'])}}</td> --}}
                <td align="center">{{strtoupper($row['kode_item'])}}</td>
                <td align="center">{{ucwords($row['nama_item'])}}</td>
                <td align="center">{{strtoupper($row['txt_index'])}}</td>
                <td align="right">{{number_format($row['stok_item'])}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
