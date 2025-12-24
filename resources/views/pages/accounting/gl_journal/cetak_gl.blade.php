<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php
        use Carbon\Carbon;
        use App\Classes\BusinessManagement\Helper;

        $rowCount = 0;
        $filler = 20;
        $saldo = $data["saldoAwalDebet"] - $data["saldoAwalKredit"];
        $saldoAkhir = ($data["saldoAwalDebet"] - $data["saldoAwalKredit"]) + ($data["mutasiDebet"] - $data["mutasiKredit"]);
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
            <tr>
                <td style="text-align:center;margin-top:1px;" colspan="9"><h4 style="text-align: center; margin:0;padding:0;">BUKU BESAR</h4></td>
            </tr>
            <tr>
                <td style="text-align:center;margin-top:0px;" colspan="9"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
            </tr>
            <tr>
                <td style="text-align:center;border-bottom: 1px solid black;margin-top:0px;" colspan="9"><h4 style="text-align: center; margin:0;padding:0;">{{$data["txtPeriode"]}}</h4></td>
            </tr>
            <tr style="font-size: 11px;border-right: 1px solid black;border-left: 1px solid black;">
                <td>No. Akun</td>
                <td>:</td>
                <td>{{$data["dataAccount"]["account_number"]}}</td>
                <td></td>
                <td></td>
                <td></td>
                @if ($data["jenisPeriode"] == "bulanan")
                <td>Bulan</td>
                <td>:</td>
                <td>{{Carbon::parse($data["bulan"])->format('m')}}</td>
                @else
                <td>Tahun</td>
                <td>:</td>
                <td>{{Carbon::parse($data["tahun"])->format('Y')}}</td>
                @endif
            </tr>
            <tr style="font-size: 11px;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;margin-top:1px;">
                <td>Nama Akun</td>
                <td>:</td>
                <td>{{$data["dataAccount"]["account_name"]}}</td>
                <td></td>
                <td></td>
                <td></td>
                @if ($data["jenisPeriode"] == "bulanan")
                <td>Tahun</td>
                <td>:</td>
                <td>{{Carbon::parse($data["bulan"])->format('Y')}}</td>
                @else
                <td></td>
                <td></td>
                <td></td>
                @endif

            </tr>
            <tr style="font-size: 11px;border-right: 1px solid black;border-left: 1px solid black;">
                <td>Saldo Awal </td>
                <td>:</td>
                <td style="text-align:right; border-right: 1px solid black;">{{number_format($saldo, 2,",",".")}}</td>
                <td>Mutasi Debet</td>
                <td>:</td>
                <td style="text-align:right;border-right: 1px solid black;">{{number_format($data["mutasiDebet"], 2,",",".")}}</td>
                <td>Saldo Akhir</td>
                <td>:</td>
                <td style="text-align:right;">{{ number_format($saldoAkhir, 2,",",".") }}</td>
            </tr>
            <tr style="font-size: 11px;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;margin-top:1px;">
                <td></td>
                <td></td>
                <td style="border-right: 1px solid black;"></td>
                <td>Mutasi Kredit</td>
                <td>:</td>
                <td style="text-align:right;border-right: 1px solid black;">{{number_format($data["mutasiKredit"], 2,",",".")}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table style="margin-top:10px;width: 100%;border:1px solid black;border-collapse: collapse;">
            <thead>
                <tr>
                    <td style="font-size: 11px;text-align: center;border-right:1px solid black;">
                        Tanggal
                    </td>
                    <td style="font-size: 11px;text-align: center;border-right:1px solid black;">
                        Keterangan
                    </td>
                    <td style="font-size: 11px;text-align: center;border-right:1px solid black;">
                        No. Ref
                    </td>
                    <td style="font-size: 11px;text-align: center;border-right:1px solid black;">
                        Debet
                    </td>
                    <td style="font-size: 11px;text-align: center;border-right:1px solid black;">
                        Kredit
                    </td>
                    <td style="font-size: 11px;text-align: center;border-bottom:1px solid black;">
                        Saldo
                    </td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="5" class="TableItemTD" style="text-align: center;">Saldo Awal</td>
                <td class="TableItemTD" style="text-align: right;">{{number_format($saldo, 2,",",".") }}</td>
            </tr>
            @foreach($data["dataDetails"] as $item)
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
                    <td class="TableItemTD" style="text-align: right;">{{$item["side"] == "debet" ? number_format($item["nominal"], 2,",",".") : number_format(0, 2,",","."); }}</td>
                    <td class="TableItemTD" style="text-align: right;">{{$item["side"] == "credit" ? number_format($item["nominal"], 2,",",".") : number_format(0, 2,",","."); }}</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($saldo, 2,",",".") }}</td>
                </tr>
            @endforeach

                <tr>
                    <td colspan="3" class="TableItemTD" style="text-align: center;">Total</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($data["mutasiDebet"], 2,",",".")}}</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($data["mutasiKredit"], 2,",",".")}}</td>
                    <td class="TableItemTD" style="text-align: center;"></td>
                </tr>
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
