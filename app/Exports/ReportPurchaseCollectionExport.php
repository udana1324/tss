<?php

namespace App\Exports;

use App\Models\Accounting\AccountPayableDetail;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Purchasing\PurchaseInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportPurchaseCollectionExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->supplier = $request->input('supplier');
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
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;
        $supplier = $this->supplier;

        // if ($jenisPeriode != null) {
        //     $transaction = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                     ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
        //                                     ->leftJoin('supplier_detail', 'purchase_order.id_alamat', '=', 'supplier_detail.id')
        //                                     ->select(
        //                                         'purchase_invoice.*',
        //                                         'purchase_order.no_po',
        //                                         'supplier.nama_supplier'
        //                                     )
        //                                     ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
        //                                         $q->whereBetween('purchase_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
        //                                     })
        //                                     ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
        //                                         $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
        //                                         $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
        //                                     })
        //                                     ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
        //                                         $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
        //                                     })
        //                                     ->orderBy('purchase_invoice.tanggal_invoice', 'desc')
        //                                     ->get();

        //     if ($jenisPeriode == "harian") {
        //         $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
        //     }
        //     else if ($jenisPeriode == "bulanan") {
        //         $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
        //     }
        //     else {
        //         $txt = Carbon::parse($bulan)->isoFormat('Y');
        //     }
        // }

        $totalPembayaran = AccountPayableDetail::select(
                                                            'account_payable_detail.id_invoice',
                                                            DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumPembayaran")
                                                        )
                                                        ->groupBy('account_payable_detail.id_invoice');

        $transaction = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                        ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                        ->leftJoin('supplier_detail', 'purchase_order.id_alamat', '=', 'supplier_detail.id')
                                        ->leftJoinSub($totalPembayaran, 'totalPembayaran', function($totalPembayaran) {
                                            $totalPembayaran->on('purchase_invoice.id', '=', 'totalPembayaran.id_invoice');
                                        })
                                        ->leftJoin('purchase_invoice_collection_detail', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
                                        ->leftJoin('purchase_invoice_collection', 'purchase_invoice_collection_detail.id_tf', '=', 'purchase_invoice_collection.id')
                                        ->select(
                                            'purchase_invoice_collection.kode_tf',
                                            'purchase_invoice_collection.tanggal',
                                            'purchase_invoice.*',
                                            'purchase_order.no_po',
                                            'supplier.nama_supplier',
                                            DB::raw('COALESCE(totalPembayaran.sumPembayaran, 0) as sumPembayaran')
                                        )
                                        ->where([
                                            ['purchase_invoice.status_invoice', '=', 'posted'],
                                            ['purchase_invoice.flag_pembayaran', '!=', 1]
                                        ])
                                        ->when($supplier != "", function($q) use ($supplier) {
                                            $q->where('supplier.id', '=', $supplier);
                                        })
                                        // ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                        //     $q->whereBetween('purchase_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                        // })
                                        // ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                        //     $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                        //     $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                        // })
                                        // ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                        //     $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                        // })
                                        ->whereRaw("DATE_ADD(purchase_invoice_collection.tanggal, INTERVAL 15 DAY) <= CURDATE()")
                                        ->orderBy('purchase_invoice.tanggal_invoice', 'asc')
                                        ->get();

        // $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.report.reportPurchaseCollectionExport', $data);
    }
}
