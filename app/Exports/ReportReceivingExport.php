<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;

class ReportReceivingExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idProduct = $request->input('product');
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $idProduct = $this->idProduct;
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        if ($jenisPeriode != null) {
            if ($idProduct == "All") {
                $idProduct = "";
            }

            $transaction = StockTransaction::leftJoin('receiving', 'receiving.kode_penerimaan', '=', 'stock_transaction.kode_transaksi')
                                            ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->leftJoin('product', 'stock_transaction.id_item', '=', 'product.id')
                                            ->leftJoin('purchase_order_detail', function($join) {
                                                $join->on('stock_transaction.id_item', '=', 'purchase_order_detail.id_item');
                                                $join->on('stock_transaction.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                                $join->on('purchase_order.id', '=', 'purchase_order_detail.id_po');
                                            })
                                            ->leftJoin('product_unit', 'stock_transaction.id_satuan', '=', 'product_unit.id')
                                            ->select(
                                                'product.id',
                                                'receiving.kode_penerimaan',
                                                'purchase_order.no_po',
                                                'stock_transaction.tgl_transaksi',
                                                'stock_transaction.qty_item',
                                                'product_unit.nama_satuan',
                                                'purchase_order_detail.harga_beli',
                                                'supplier.nama_supplier',
                                                'product.kode_item',
                                                'product.nama_item'
                                            )
                                            ->where([
                                                ['stock_transaction.jenis_transaksi', '=', 'penerimaan']
                                            ])
                                            ->whereNotIn('purchase_order.status_po', ['draft', 'cancel'])
                                            ->whereNotIn('receiving.status_penerimaan', ['draft', 'cancel'])
                                            ->when($idProduct != "", function($q) use ($idProduct) {
                                                $q->where('stock_transaction.id_item', '=', $idProduct);
                                            })
                                            ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                $q->whereBetween('stock_transaction.tgl_transaksi', [$tglStart, $tglEnd]);
                                            })
                                            ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                $q->whereMonth('stock_transaction.tgl_transaksi', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('stock_transaction.tgl_transaksi', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                $q->whereYear('stock_transaction.tgl_transaksi', Carbon::parse($tahun)->format('Y'));
                                            })
                                            ->orderBy('stock_transaction.tgl_transaksi', 'desc')
                                            ->get();
            if ($jenisPeriode == "harian") {
                $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
            }
            else if ($jenisPeriode == "bulanan") {
                $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
            }
            else {
                $txt = Carbon::parse($bulan)->isoFormat('Y');
            }
        }

        $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.report.reportReceivingExport', $data);
    }
}
