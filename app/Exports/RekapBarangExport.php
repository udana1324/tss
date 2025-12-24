<?php

namespace App\Exports;

use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RekapBarangExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->kodeProduct = $request->input('product');
        $this->idCust = $request->input('id_customer');
        $this->jenisPeriode = $request->input('jenis_periode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $kodeProduct = $this->kodeProduct;
        $jenisPeriode = $this->jenisPeriode;
        $idCust = $this->idCust;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        $transaction = Product::leftJoin('sales_order_detail', 'product.id', '=', 'sales_order_detail.id_item')
                        ->leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                        ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                        ->select(
                            'product.id',
                            'product.kode_item',
                            'product.nama_item',
                            'sales_order_detail.id_satuan',
                            'product_unit.nama_satuan',
                            DB::raw("SUM(sales_order_detail.qty_item) AS qty_item"),
                            DB::raw("SUM(sales_order_detail.qty_outstanding) AS qty_outstanding")
                        )
                        ->where([
                            ['qty_item', '>', 0]
                        ])
                        ->when($idCust != "", function($q) use ($idCust) {
                            $q->where('sales_order.id_customer', '=', $idCust);
                        })
                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                            $q->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd]);
                        })
                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                            $q->whereMonth('sales_order.tanggal_so', Carbon::parse($bulan)->format('m'));
                            $q->whereYear('sales_order.tanggal_so', Carbon::parse($bulan)->format('Y'));
                        })
                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                            $q->whereYear('sales_order.tanggal_so', Carbon::parse($tahun)->format('Y'));
                        })
                        ->groupBy('product.id')
                        ->get();

        if ($jenisPeriode == "harian") {
            $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
        }
        else if ($jenisPeriode == "bulanan") {
            $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
        }
        else if ($jenisPeriode == "tahunan") {
            $txt = Carbon::parse($tahun)->isoFormat('Y');
        }
        else {
            $txt = Carbon::now()->isoFormat('D MMM Y');
        }

        $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.sales.rekap.barang.rekapExport', $data);
    }
}
