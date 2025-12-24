<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php
        use Carbon\Carbon;
        use App\Classes\BusinessManagement\Helper;

        $rowCount = 0;
        $filler = 20;
        $saldo = $saldoAwalDebet - $saldoAwalKredit;
        $saldoAkhir = ($saldoAwalDebet - $saldoAwalKredit) + ($mutasiDebet - $mutasiKredit);
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
            <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">BUKU BESAR</h4></td>
        </tr>
        <tr>
            <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
        </tr>
        <tr>
            <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{$txtPeriode}}</h4></td>
        </tr>
    </table>
    <table class="tabelHeader" style="width: 100%;border-collapse: collapse;border:1px solid black;">
        <tr style="font-size: 11px;">
            <td>No. Akun</td>
            <td>{{$dataAccount["account_number"]}}</td>
            <td></td>
            <td></td>
            @if ($jenisPeriode == "bulanan")
            <td>Bulan</td>
            <td>{{strval(Carbon::parse($bulan)->format('m'))}}</td>
            @else
            <td>Tahun</td>
            <td>{{strval(Carbon::parse($tahun)->format('Y'))}}</td>
            @endif
        </tr>
        <tr style="font-size: 11px;margin-top:1px;">
            <td>Nama Akun</td>
            <td>{{$dataAccount["account_name"]}}</td>
            <td></td>
            <td></td>
            @if ($jenisPeriode == "bulanan")
            <td>Tahun</td>
            <td>{{strval(Carbon::parse($bulan)->format('Y'))}}</td>
            @else
            <td></td>
            <td></td>
            @endif

        </tr>
        <tr style="font-size: 11px">
            <td>Saldo Awal </td>
            <td style="text-align:right;">{{$saldo}}</td>
            <td>Mutasi Debet</td>
            <td style="text-align:right">{{$mutasiDebet}}</td>
            <td>Saldo Akhir</td>
            <td style="text-align:right;">{{ $saldoAkhir }}</td>
        </tr>
        <tr style="font-size: 11px;margin-top:1px;">
            <td></td>
            <td></td>
            <td>Mutasi Kredit</td>
            <td style="text-align:right;">{{$mutasiKredit}}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table style="margin-top:10px;width: 100%;border:1px solid black;border-collapse: collapse;">
        <thead>
            <tr>
                <td style="font-size: 11px;text-align: center;">
                    Tanggal
                </td>
                <td style="font-size: 11px;text-align: center;">
                    Keterangan
                </td>
                <td style="font-size: 11px;text-align: center;">
                    No. Ref
                </td>
                <td style="font-size: 11px;text-align: center;">
                    Debet
                </td>
                <td style="font-size: 11px;text-align: center;">
                    Kredit
                </td>
                <td style="font-size: 11px;text-align: center;">
                    Saldo
                </td>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="5" class="TableItemTD" style="text-align: center;">Saldo Awal</td>
            <td class="TableItemTD" style="text-align: right;">{{$saldo }}</td>
        </tr>
        @foreach($dataDetails as $item)
            @php
                if ($item["side"] == "debet") {
                    $saldo = $saldo + $item["nominal"];
                }
                else {
                    $saldo = $saldo - $item["nominal"];
                }

            @endphp
            <tr>
                <td class="TableItemTD" style="text-align: center;">{{Carbon::parse($item["tanggal_transaksi"])->isoFormat('D MMMM Y')}}</td>
                <td class="TableItemTD" style="text-align: left;">{{$item["account_number"]}} - {{$item["account_name"]}}</td>
                <td class="TableItemTD" style="text-align: center;">{{strtoupper($item["kode_ref"])}}</td>
                <td class="TableItemTD" style="text-align: right;">{{$item["side"] == "debet" ? $item["nominal"] : 0; }}</td>
                <td class="TableItemTD" style="text-align: right;">{{$item["side"] == "credit" ? $item["nominal"] : 0; }}</td>
                <td class="TableItemTD" style="text-align: right;">{{$saldo }}</td>
            </tr>
        @endforeach

            <tr>
                <td colspan="3" class="TableItemTD" style="text-align: center;">Total</td>
                <td class="TableItemTD" style="text-align: right;">{{$mutasiDebet}}</td>
                <td class="TableItemTD" style="text-align: right;">{{$mutasiKredit}}</td>
                <td class="TableItemTD" style="text-align: center;"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
