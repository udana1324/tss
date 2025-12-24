<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Library\Supplier;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Purchasing\PurchaseInvoice;
use Illuminate\Support\Carbon;
use App\Exports\ReportPurchasingDetailExport;
use App\Models\Product\ProductDetailSpecification;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Setting\Module;

class PurchaseReportDetailController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportPurchasingDetail'],
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
                                                ['module.url', '=', '/ReportPurchasingDetail'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataSupplier = Supplier::distinct()->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Supplier Report Detail',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Supplier Report Detail',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportPurchasingDetail', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataPurchasingReportDetail(Request $request)
    {
        $idSupplier = $request->input('idSupplier');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $transaction = "";

        if ($jenisPeriode != null) {
            if ($idSupplier == "All") {
                $idSupplier = "";
            }

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $transaction = PurchaseInvoice::leftJoin('purchase_invoice_detail', 'purchase_invoice_detail.id_invoice', '=', 'purchase_invoice.id')
                                            ->leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                                            ->leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', '=', 'receiving.id')
                                            ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
                                            ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                            ->leftJoin('purchase_order_detail', function($join) {
                                                $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                                $join->on('receiving_detail.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                                $join->on('purchase_order.id', '=', 'purchase_order_detail.id_po');
                                            })
                                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                            })
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
                                                'supplier.nama_supplier',
                                                'dataSpek.value_spesifikasi'
                                            )
                                            ->where([
                                                ['purchase_invoice.status_invoice', '=', 'posted']
                                            ])
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
        }

        return response()->json($transaction);
    }

    public function exportDataPurchasingDetailReport(Request $request)
    {
        return Excel::download(new ReportPurchasingDetailExport($request), 'ReportPurchasingDetail.xlsx');
    }
}
