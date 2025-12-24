<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Purchasing\PurchaseInvoice;
use Illuminate\Support\Carbon;

class ReportPurchasingDetailExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
        $this->idSupplier = $request->input('supplier');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;
        $idSupplier = $this->idSupplier;

        if ($jenisPeriode != null) {
            if ($idSupplier == "All") {
                $idSupplier = "";
            }

            $transaction = PurchaseInvoice::leftJoin('purchase_invoice_detail', 'purchase_invoice_detail.id_invoice', '=', 'purchase_invoice.id')
                                            ->leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                                            ->leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', '=', 'receiving.id')
                                            ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                            ->leftJoin('purchase_order_detail', function($join) {
                                                $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                                $join->on('receiving_detail.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                                $join->on('purchase_order.id', '=', 'purchase_order_detail.id_po');
                                            })
                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->select(
                                                'receiving.kode_penerimaan',
                                                'purchase_order.no_po',
                                                'purchase_invoice.kode_invoice',
                                                'purchase_invoice.tanggal_invoice',
                                                'receiving_detail.qty_item',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'product_category.nama_kategori',
                                                'purchase_order_detail.harga_beli',
                                                'supplier.nama_supplier'
                                            )
                                            // ->where([
                                            //     ['supplier.id', '=', $idSupplier],
                                            // ])
                                            ->when($idSupplier != "", function($q) use ($idSupplier) {
                                                $q->where('supplier.id', '=', $idSupplier);
                                            })
                                            ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                $q->whereBetween('purchase_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                            })
                                            ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                            })
                                            ->orderBy('purchase_invoice.tanggal_invoice', 'desc')
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

        return View('pages.report.reportPurchasingDetailExport', $data);
    }
}
