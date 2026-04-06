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
        margin-top: 10px; /* Set top margin to 0 */
        /* You can also set other margins if needed */
        /* margin-right: 0px; */
        /* margin-bottom: 0px; */
        margin-left: 0px;
    }

    /* @media print {
        body { width: 58mm; }
        .no-print { display: none; }
    } */

    .center-table {
        width: 90%;
        display: block;
        margin: auto;
        clear: both;
    }

    /* .double-border-row {
        border-style: double dashed; /* Applies a double border to cells in this specific row
        border-width: 3px; /* Adjust the width as needed for a visible double line
        border-color: black; /* Customize the color
    } */

    </style>
</head>
<body>
    <div style="width: 58mm;">
        <div class="center-table">
            <table style="margin-left:0;width: 90%;border-collapse: collapse;font-size:8px;">
                <tr>
                    <td style="text-align: center;">{{ucwords($data["dataPreference"]["nama_pt"])}}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">{{ucwords($data["dataPreference"]["alamat_pt"])}}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">{{ucwords($data["dataPreference"]["kota_pt"])}}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">Telp : {{ucwords($data["dataPreference"]["telp_pt"])}}</td>
                </tr>
            </table>
            <table style="margin-left:0;width: 90%;border-collapse: collapse;font-size:7px;">
                <tr>
                    <td style="text-align: left;border-top:1px dashed black;border-bottom:1px dashed black;"></td>
                </tr>
            </table>
            <table style="margin-left:0;width: 90%;border-collapse: collapse;font-size:7px;">
                <tr>
                    <td style="text-align: left;width:25%;">No. Nota</td>
                    <td style="width:5%;">:</td>
                    <td style="text-align: left;width:70%;">{{strtoupper($data["dataTransaction"]["no_ref"])}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Waktu</td>
                    <td>:</td>
                    <td style="text-align: left;">{{Carbon::parse($data["dataTransaction"]["tanggal_penjualan"])->locale('id')->isoFormat('DD MMMM Y HH:mm:ss')}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Kasir</td>
                    <td>:</td>
                    <td style="text-align: left;">{{ucwords($data["dataUser"]["user_name"])}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Customer</td>
                    <td>:</td>
                    <td style="text-align: left;">{{ucwords($data["dataCustomer"]["nama_customer"])}}</td>
                </tr>
                </table>
                <table style="width: 90%;padding-top:5px;border-collapse: collapse;font-size:7px;">
                @foreach($data["details"] as $detail)
                    @if ($loop->first)
                    <tr>
                        <td style="border-top:1px dashed black;" colspan="3">{{ucwords($detail["nama_item"])}} ({{ucwords($detail["nama_satuan"])}})</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;width:60%;border-bottom:1px dashed black;">{{number_format($detail["qty_item"], 0,",",".")}} x  @ {{number_format($detail["harga_jual"], 0,",",".")}}</td>
                        <td  style="text-align: right;width:40%;border-bottom:1px dashed black;">{{number_format($detail["subtotal"], 0,",",".")}}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="3" style="text-align: left;">{{ucwords($detail["nama_item"])}} ({{ucwords($detail["nama_satuan"])}})</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;width:60%;border-bottom:1px dashed black;">{{number_format($detail["qty_item"], 0,",",".")}} x  @ {{number_format($detail["harga_jual"], 0,",",".")}}</td>
                        <td  style="text-align: right;width:40%;border-bottom:1px dashed black;">{{number_format($detail["subtotal"], 0,",",".")}}</td>
                    </tr>
                    @endif
                @endforeach
                @if ($data["dataTransaction"]["id_hutang"] != null)
                    <tr>
                        <td colspan="2" style="text-align: left;">Subtotal {{number_format($data["dataTransaction"]["jumlah_total_qty"], 0,",",".")}} Produk</td>
                        <td style="text-align: right;">{{number_format($data["dataTransaction"]["nominal_total"], 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;border-bottom:1px dashed black;">Hutang Transaksi {{strtoupper($data["prevTransaction"]["no_ref"])}}</td>
                        <td style="text-align: right;border-bottom:1px dashed black;">{{number_format($data["prevTransaction"]["nominal_outstanding"], 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;border-bottom:1px dashed black;">Total</td>
                        <td style="text-align: right;border-bottom:1px dashed black;">{{number_format(($data["dataTransaction"]["nominal_total"] + $data["prevTransaction"]["nominal_outstanding"]), 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;">Pembayaran : {{ucwords($data["dataTransaction"]["metode_pembayaran"])}}</td>
                        <td style="text-align: right;">{{number_format(($data["dataTransaction"]["nominal_pembayaran"] + $data["dataTransaction"]["nominal_pembayaran_hutang"]), 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;">Total Bayar</td>
                        <td style="text-align: right;">{{number_format(($data["dataTransaction"]["nominal_pembayaran"] + $data["dataTransaction"]["nominal_pembayaran_hutang"]), 0,",",".")}}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2" style="text-align: left;">Subtotal {{number_format($data["dataTransaction"]["jumlah_total_qty"], 0,",",".")}} Produk</td>
                        <td style="text-align: right;">{{number_format($data["dataTransaction"]["nominal_total"], 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;border-bottom:1px dashed black;">Total</td>
                        <td style="text-align: right;border-bottom:1px dashed black;">{{number_format($data["dataTransaction"]["nominal_total"], 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;">Pembayaran : {{ucwords($data["dataTransaction"]["metode_pembayaran"])}}</td>
                        <td style="text-align: right;">{{number_format($data["dataTransaction"]["nominal_pembayaran"], 0,",",".")}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;">Total Bayar</td>
                        <td style="text-align: right;">{{number_format($data["dataTransaction"]["nominal_pembayaran"], 0,",",".")}}</td>
                    </tr>
                @endif

                @if ($data["dataTransaction"]["metode_pembayaran"] == "cash")
                <tr>
                    <td colspan="2" style="text-align: left;">Kembalian</td>
                    <td style="text-align: right;">{{number_format($data["dataTransaction"]["nominal_change"], 0,",",".")}}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="border-top:1px dashed black;border-bottom:1px dashed black;"></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">Tokopedia : Test Tokopedia</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">Shopee : Test Shopee</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">Teks Promosi</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">Tgl. Cetak : {{Carbon::now()->locale('id')->isoFormat('DD MMMM Y HH:mm:ss')}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">Dicetak : {{ucwords($data["current_user"])}}</td>
                </tr>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        try {
            this.print();
        } catch (e) {
            window.onload = window.print;
        }
    </script>
</body>
</html>
