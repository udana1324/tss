<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php
        use Carbon\Carbon;
        use App\Classes\BusinessManagement\Helper;

        $rowCount = 0;
        $filler = 20;
    @endphp
    <style>
    @page {
        margin-top: 15px; /* Set top margin to 0 */
        /* You can also set other margins if needed */
        /* margin-right: 0px; */
        /* margin-bottom: 0px; */
        /* margin-left: 0px; */
    }
    .page-break {
        page-break-after: always;
    }

    .TableItemTH {
        font-size: 11px;
    }

    .TableItemTD {
        font-size: 9px;
    }

    </style>
</head>
<body>
    <table class="tabelHeader" style="width: 100%;border-collapse: collapse;margin-top:0;padding-top:0;">
        <tr>
            <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">NERACA SALDO</h4></td>
        </tr>
        <tr>
            <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
        </tr>
        <tr>
            <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{$txtPeriode}}</h4></td>
        </tr>
    </table>
    <table class="tabelHeader" style="width: 100%;border-collapse: collapse;border:1px solid black;">
        <thead>
            <tr style="text-align:center;font-size: 11px;">
                <td class="TableItemTH">No. Akun</td>
                <td class="TableItemTH">Nama Akun</td>
                <td class="TableItemTH">Saldo Awal</td>
                <td class="TableItemTH">Mutasi Debet</td>
                <td class="TableItemTH">Mutasi Kredit</td>
                <td class="TableItemTH">Saldo Akhir</td>
            </tr>
        </thead>
        <tbody>
            @foreach($dataDetails as $item)
                @php
                    $saldoAkhir = $item["saldo_awal"] + ($item["mutasi_debet"] - $item["mutasi_kredit"]);
                @endphp
                <tr>
                    <td class="TableItemTD" style="text-align: center;">{{$item["account_number"]}}</td>
                    <td class="TableItemTD" style="text-align: left;">{{$item["account_name"]}}</td>
                    <td class="TableItemTD" style="text-align: right;">{{$item["saldo_awal"]}}</td>
                    <td class="TableItemTD" style="text-align: right;">{{$item["mutasi_debet"]}}</td>
                    <td class="TableItemTD" style="text-align: right;">{{$item["mutasi_kredit"]}}</td>
                    <td class="TableItemTD" style="text-align: right;">{{$saldoAkhir}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
