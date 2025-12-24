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
    @endphp
    <style>
    @page {
        margin-top: 15px; /* Set top margin to 0 */
        /* You can also set other margins if needed */
        /* margin-right: 0px; */
        /* margin-bottom: 0px; */
        /* margin-left: 0px; */
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
    <div class="body">
        <table class="tabelHeader" style="width: 100%;border-collapse: collapse;margin-top:0;padding-top:0;">
            <thead>
                <tr>
                    <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{strtoupper($dataPreference["nama_pt"])}}</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;margin-top:1px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">LAPORAN POSISI KEUANGAN</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;margin-top:0px;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">PERIODE</h4></td>
                </tr>
                <tr>
                    <td style="text-align:center;" colspan="6"><h4 style="text-align: center; margin:0;padding:0;">{{$txtPeriode}}</h4></td>
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
                @foreach($aktivaLancar as $detailAktivaLancar)
                    @php
                        $totalAktivaLancar = $totalAktivaLancar + $detailAktivaLancar["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$detailAktivaLancar["account_number"]}} - {{$detailAktivaLancar["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{$detailAktivaLancar["saldo_akhir"]}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Aktiva Lancar</td>
                    <td class="TableItemTD" style="text-align: right;">{{$totalAktivaLancar}}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Aktiva Tetap</td>
                </tr>
                @foreach($aktivaTetap as $detailAktivaTetap)
                    @php
                        $totalAktivaTetap = $totalAktivaTetap + $detailAktivaTetap["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$detailAktivaTetap["account_number"]}} - {{$detailAktivaTetap["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{$detailAktivaTetap["saldo_akhir"]}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Aktiva Tetap</td>
                    <td class="TableItemTD" style="text-align: right;">{{$totalAktivaTetap}}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Akumulasi Penyusutan</td>
                </tr>
                @foreach($akumulasiPenyusutan as $detailAkumulasiPenyusutan)
                    @php
                        $totalAkumulasiPenyusutan = $totalAkumulasiPenyusutan + $detailAkumulasiPenyusutan["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$detailAkumulasiPenyusutan["account_number"]}} - {{$detailAkumulasiPenyusutan["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{$detailAkumulasiPenyusutan["saldo_akhir"]}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Akumulasi Penyusutan</td>
                    <td class="TableItemTD" style="text-align: right;">{{$totalAkumulasiPenyusutan}}</td>
                </tr>
            </table>
        </div>

        <div class="right-table">
            <table style="width: 100%;border-collapse: collapse;margin:0;padding-left:2px;">
                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Liabilitas</td>
                </tr>
                @foreach($liabilitas as $detailLiabilitas)
                    @php
                        $totalLiabilitas = $totalLiabilitas + $detailLiabilitas["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$detailLiabilitas["account_number"]}} - {{$detailLiabilitas["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{$detailLiabilitas["saldo_akhir"] * -1}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Liabilitas</td>
                    <td class="TableItemTD" style="text-align: right;">{{$totalLiabilitas  * -1}}</td>
                </tr>

                <tr style="text-align:center;">
                    <td colspan="2" class="TableItemTH">Ekuitas</td>
                </tr>
                @foreach($ekuitas as $detailEkuitas)
                    @php
                        $totalEkuitas = $totalEkuitas + $detailEkuitas["saldo_akhir"];
                    @endphp
                    <tr>
                        <td class="TableItemTD" style="text-align: left;">{{$detailEkuitas["account_number"]}} - {{$detailEkuitas["account_name"]}}</td>
                        <td class="TableItemTD" style="text-align: right;">{{$detailEkuitas["saldo_akhir"]}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="TableItemTD" style="text-align: center;font-weight:bold;">Total Ekuitas</td>
                    <td class="TableItemTD" style="text-align: right;">{{$totalEkuitas}}</td>
                </tr>
            </table>
        </div>
        <div class="center-table">
            <table style="width: 100%;border-collapse: collapse;margin-top:55px;">
                <tr>
                    <td style="margin-right: 300px;display:block;" ></td>
                    <td style="text-align: center;padding-bottom:100px;">{{ucwords($dataPreference["kota_pt"])}},  {{Carbon::now()->isoFormat('D MMMM Y')}}</td>
                </tr>
                <tr>
                    <td ></td>
                    <td></td>
                </tr>
                <tr>
                    <td ></td>
                    <td style="text-align: center;">{{ucwords($dataPreference["direktur"])}}</td>
                </tr>
                <tr>
                    <td ></td>
                    <td style="text-align: center;">(Direktur)</td>
                </tr>
            </table>

        </div>

    </div>
</body>
</html>
