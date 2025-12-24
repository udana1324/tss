
    <table>
        <tr>
            <td colspan="6" align="center">Laporan Tukar Faktur Pembelian</td>
        </tr>
        <tr>
            <td colspan="6" align="center">Periode</td>
        </tr>
        <tr>
            <td colspan="6" align="center">{{ Carbon\Carbon::now()->isoFormat('D MMM Y') }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th align="center">Tanggal</th>
                <th colspan="2" align="center">Nomor Transaksi</th>
                <th align="center">Nama Perusahaan</th>
                <th align="center">DPP</th>
                <th align="center">PPn</th>
                <th align="center">Grand Total</th>
                <th align="center">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $row)
            <tr>
                <td align="center">{{ Carbon\Carbon::parse($row->tanggal_invoice)->isoFormat('D MMM Y') }}</td>
                <td align="center">{{strtoupper($row->kode_invoice)}}</td>
                <td align="center">{{strtoupper($row->no_po)}}</td>
                <td align="center">{{strtoupper($row->nama_supplier)}}</td>
                <td align="right">{{$row->dpp}}</td>
                <td align="right">{{$row->ppn}}</td>
                <td align="right">{{$row->grand_total}}</td>
                <td align="right">{{$row->sumPembayaran}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="4" align="center">Total Pembelian</td>
            <td align="right">{{$dataLaporan->sum('grand_total')}}</td>
        </tr>
        <tr>
            <td colspan="4" align="center">Total Faktur Pembelian</td>
            <td align="right">{{$dataLaporan->count()}}</td>
        </tr>
    </table>
