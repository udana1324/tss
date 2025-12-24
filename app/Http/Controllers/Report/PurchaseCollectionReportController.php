<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Exports\ReportPurchaseCollectionExport;
use App\Models\Purchasing\PurchaseInvoice;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportPurchasingExport;
use App\Models\Accounting\AccountPayableDetail;
use App\Models\Library\Supplier;
use App\Models\Setting\Module;

class PurchaseCollectionReportController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportPurchaseCollection'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportPurchaseCollection'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataSupplier = Supplier::distinct()->orderBy('nama_supplier', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Purchasing Report Collection',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Purchasing Report Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportPurchaseCollection', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataCollectionReport(Request $request)
    {
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $supplier = $request->input('supplier');

        $transaction = "";

        // if ($jenisPeriode != null) {

        //     $totalPembayaran = AccountPayableDetail::select(
        //                                                     'account_payable_detail.id_invoice',
        //                                                     DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumPembayaran")
        //                                                 )
        //                                                 ->groupBy('account_payable_detail.id_invoice');

        //     $transaction = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                     ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
        //                                     ->leftJoin('supplier_detail', 'purchase_order.id_alamat', '=', 'supplier_detail.id')
        //                                     ->leftJoinSub($totalPembayaran, 'totalPembayaran', function($totalPembayaran) {
        //                                         $totalPembayaran->on('purchase_invoice.id', '=', 'totalPembayaran.id_invoice');
        //                                     })
        //                                     ->leftJoin('purchase_invoice_collection_detail', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
        //                                     ->leftJoin('purchase_invoice_collection', 'purchase_invoice_collection_detail.id_tf', '=', 'purchase_invoice_collection.id')
        //                                     ->select(
        //                                         'purchase_invoice_collection.kode_tf',
        //                                         'purchase_invoice_collection.tanggal',
        //                                         'purchase_invoice.*',
        //                                         'purchase_order.no_po',
        //                                         'supplier.nama_supplier',
        //                                         DB::raw('COALESCE(totalPembayaran.sumPembayaran, 0) as sumPembayaran')
        //                                     )
        //                                     // ->where([
        //                                     //     ['purchase_invoice.status_invoice', '=', 'posted']
        //                                     // ])
        //                                     ->when($supplier != "", function($q) use ($supplier) {
        //                                         $q->where('supplier.id', '=', $supplier);
        //                                     })
        //                                     // ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
        //                                     //     $q->whereBetween('purchase_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
        //                                     // })
        //                                     // ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
        //                                     //     $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
        //                                     //     $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
        //                                     // })
        //                                     // ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
        //                                     //     $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
        //                                     // })
        //                                     ->whereRaw("DATE_ADD(purchase_invoice_collection.tanggal, INTERVAL 15 DAY) <= CURDATE")
        //                                     ->orderBy('purchase_invoice.tanggal_invoice', 'asc')
        //                                     ->get();
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

        return response()->json($transaction);
    }

    public function exportDataCollectionReport(Request $request)
    {
        return Excel::download(new ReportPurchaseCollectionExport($request), 'ReportPurchaseCollection.xlsx');
    }
}
