<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportReceivingExport;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Setting\Module;

class ReceivingReportController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReceivingReport'],
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
                                                ['module.url', '=', '/ReceivingReport'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataProduct = Product::distinct()
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->orderBy('nama_item', 'asc')
                                        ->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Receiving Report',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Receiving Report',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportReceiving', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataReceivingReport(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $transaction = "";

        if ($jenisPeriode != null) {
            if ($idProduct == "All") {
                $idProduct = "";
            }


            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

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
                                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                            })
                                            ->select(
                                                'product.id',
                                                'receiving.kode_penerimaan',
                                                'purchase_order.no_po',
                                                'stock_transaction.tgl_transaksi',
                                                'stock_transaction.qty_item',
                                                'product_unit.nama_satuan',
                                                'product.nama_item',
                                                'purchase_order_detail.harga_beli',
                                                'supplier.nama_supplier',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'dataSpek.value_spesifikasi'
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
        }

        return response()->json($transaction);
    }

    public function exportDataReceivingReport(Request $request)
    {
        return Excel::download(new ReportReceivingExport($request), 'ReportReceiving.xlsx');
    }
}
