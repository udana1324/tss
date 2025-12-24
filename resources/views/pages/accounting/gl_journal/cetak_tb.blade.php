<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php
        use Carbon\Carbon;
        use App\Classes\BusinessManagement\Helper;

        $rowCount = 0;
        $filler = 20;
        // $ppnPercentage = 1+($data["taxSettings"]["ppn_percentage"]/100);
        // $txtTerbilang = Helper::number_to_words($data["dataDelivery"]["grand_total"]);
        // $txtTerbilang = strtoupper("#".$txtTerbilang." Rupiah");
        // $subtotal = 0;
        // $hargajual_item = 0;
        // $subtotal_item = 0;
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
        border: solid 1px black;
    }

    .TableItemTD {
        font-size: 9px;
        border: solid 1px black;
    }

    </style>
</head>
<body>
    <div class="body">
        <table class="tabelHeader" style="width: 100%;border-collapse: collapse;margin-top:0;padding-top:0;">
            <thead>
                <tr>
                    <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">NERACA SALDO</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;border-bottom: 1px solid black;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{$data["txtPeriode"]}}</h4></td>
                </tr>
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
                @foreach($data["dataDetails"] as $item)
                    @php
                        $saldoAkhir = $item["saldo_awal"] + ($item["mutasi_debet"] - $item["mutasi_kredit"]);
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: center;">{{$item["account_number"]}}</td>
                        <td class="TableItemTD" style="text-align: left;">{{$item["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($item["saldo_awal"], 2,",",".")}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($item["mutasi_debet"], 2,",",".")}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($item["mutasi_kredit"], 2,",",".")}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($saldoAkhir, 2,",",".")}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <div id="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Hal. {PAGE_NUM} dari {PAGE_COUNT}";
                $font = $fontMetrics->getFont("serif");
                $pdf->page_text(525, 815, $text, $font, 8);
            }
        </script>
    </div>
</body>
</html>
