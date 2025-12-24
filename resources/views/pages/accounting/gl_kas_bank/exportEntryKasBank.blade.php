<table>
    <tr>
        <td style="width:20px">Tahun</td>
        <td style="width:20px">Bulan</td>
        <td style="width:20px">Nomor Bukti</td>
        <td style="width:15px">Tanggal Transaksi</td>
        <td style="width:12px">Nomor Akun</td>
        <td style="width:15px">Nama Akun</td>
        <td style="width:14px">Debit</td>
        <td style="width:26px">Credit</td>
    </tr>
    @php
        $subtotalDebit = 0;
        $subtotalCredit = 0;
    @endphp
    @foreach ($dataExport as $data)
        <tr>
            <td>{{date("d/m/Y", strtotime($data['tanggal_transaksi']))}}</td>
            <td>{{\Carbon\Carbon::parse($data['tanggal_transaksi'])->format('Y')}}</td>
            <td>{{\Carbon\Carbon::parse($data['tanggal_transaksi'])->format('m')}}</td>
            <td>{{$data['nomor_kas_bank']}}</td>

            <td>{{strtoupper($data['kasbank_account_number'])}}</td>
            <td>{{strtoupper($data['kasbank_account_name'])}}</td>
            @if($data['jenis_transaksi'] == 1)
            <td align="right">{{$data['nominal_transaksi']}}</td>
            <td>0</td>
            @php
                $subtotalDebit = $subtotalDebit + $data['nominal_transaksi'];
            @endphp
            @else
            <td>0</td>
            <td align="right">{{$data['nominal_transaksi']}}</td>
            @php
                $subtotalCredit = $subtotalCredit + $data['nominal_transaksi'];
            @endphp
            @endif
        </tr>
    @endforeach
    <tr>
        <td colspan="6"></td>
        <td align="right">{{$subtotalDebit}}</td>
        <td align="right">{{$subtotalCredit}}</td>
    </tr>
</table>
