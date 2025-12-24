<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @php
        use Carbon\Carbon;
        use App\Classes\BusinessManagement\Helper;

        $rowCount = 0;
        $filler = 20;
        $totalAktivaLancar = 0;
        $totalAktivaTetap = 0;
        $totalLiabilitas = 0;
        $totalEkuitas = 0;
        $totalAkumulasiPenyusutan = 0;
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

    .left-table, .right-table {
        width: 50%; /* Adjust width as needed */
        float: left;
        padding-top: 10px;
        display: block;
    }
    .right-table {
        float: right;
        display: block;
    }

    .center-table {
        width: 100%;
        display: block;
        margin: auto;
        clear: both;
    }

    .TableItemTH {
        font-size: 12px;
        font-weight: bold;
        border: solid 1px black;
    }

    .TableItemTD {
        font-size: 10px;
        border: solid 1px black;
    }

    </style>
</head>
<body>
    <div class="body">
        <table class="tabelHeader" style="width: 100%;border-collapse: collapse;margin-top:0;padding-top:0;">
            <thead>
                <tr>
                    <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{strtoupper($data["dataPreference"]["nama_pt"])}}</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">LAPORAN POSISI KEUANGAN</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{$data["txtPeriode"]}}</h4></td>
                </tr>
            </thead>

            <tbody>

            </tbody>
        </table>
        <div class="left-table">
            <table style="width: 100%;border-collapse: collapse;margin:0;padding:0;">
                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Aktiva Lancar</td>
                </tr>
                @foreach($data["aktivaLancar"] as $aktivaLancar)
                    @php
                        $totalAktivaLancar = $totalAktivaLancar + $aktivaLancar["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$aktivaLancar["account_number"]}} - {{$aktivaLancar["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($aktivaLancar["saldo_akhir"], 2,",",".") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Aktiva Lancar</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($totalAktivaLancar, 2,",",".") }}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Aktiva Tetap</td>
                </tr>
                @foreach($data["aktivaTetap"] as $aktivaTetap)
                    @php
                        $totalAktivaTetap = $totalAktivaTetap + $aktivaTetap["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$aktivaTetap["account_number"]}} - {{$aktivaTetap["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($aktivaTetap["saldo_akhir"], 2,",",".") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Aktiva Tetap</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($totalAktivaTetap, 2,",",".") }}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Akumulasi Penyusutan</td>
                </tr>
                @foreach($data["akumulasiPenyusutan"] as $akumulasiPenyusutan)
                    @php
                        $totalAkumulasiPenyusutan = $totalAkumulasiPenyusutan + $akumulasiPenyusutan["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$akumulasiPenyusutan["account_number"]}} - {{$akumulasiPenyusutan["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($akumulasiPenyusutan["saldo_akhir"], 2,",",".") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Akumulasi Penyusutan</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($totalAkumulasiPenyusutan, 2,",",".") }}</td>
                </tr>
            </table>
        </div>

        <div class="right-table">
            <table style="width: 100%;border-collapse: collapse;margin:0;padding-left:2px;">
                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Liabilitas</td>
                </tr>
                @foreach($data["liabilitas"] as $liabilitas)
                    @php
                        $totalLiabilitas = $totalLiabilitas + $liabilitas["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$liabilitas["account_number"]}} - {{$liabilitas["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($liabilitas["saldo_akhir"] * -1, 2,",",".") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Liabilitas</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($totalLiabilitas  * -1, 2,",",".") }}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Ekuitas</td>
                </tr>
                @foreach($data["ekuitas"] as $ekuitas)
                    @php
                        $totalEkuitas = $totalEkuitas + $ekuitas["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$ekuitas["account_number"]}} - {{$ekuitas["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{number_format($ekuitas["saldo_akhir"], 2,",",".") }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Ekuitas</td>
                    <td class="TableItemTD" style="text-align: right;">{{number_format($totalEkuitas, 2,",",".") }}</td>
                </tr>
            </table>
        </div>
        <div class="center-table">
            <table style="width: 100%;border-collapse: collapse;margin-top:55px;">
                <tr>
                    <td style="margin-right: 300px;display:block;" ></td>
                    <td style="text-align: center;padding-bottom:100px;">{{ucwords($data["dataPreference"]["kota_pt"])}},  {{Carbon::now()->isoFormat('D MMMM Y')}}</td>
                </tr>
                <tr>
                    <td ></td>
                    <td></td>
                </tr>
                <tr>
                    <td ></td>
                    <td style="text-align: center;">{{ucwords($data["dataPreference"]["direktur"])}}</td>
                </tr>
                <tr>
                    <td ></td>
                    <td style="text-align: center;">(Direktur)</td>
                </tr>
            </table>

        </div>

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
