<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Library\SupplierProduct;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use App\Models\Purchasing\ReceivingTerms;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\PurchaseOrderTerms;
use App\Models\Library\Purchase;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductUnit;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class RekapPembelianController extends Controller
{
    public function indexBarang()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/RekapPembelianBarang'],
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
                                                ['module.url', '=', '/RekapPembelianBarang'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::distinct()->get();
                $dataSatuan = ProductUnit::distinct()->get('nama_satuan');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');
                $parentMenu = Module::find($hakAkses->parent);

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataSatuan'] = $dataSatuan;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Ringkasan Pembelian',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ringkasan berdasarkan Barang',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.rekap.barang.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexSupplier()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/RekapPembelianSupplier'],
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
                                                ['module.url', '=', '/RekapPembelianSupplier'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::distinct()->get();
                $dataSupplier = Supplier::distinct()->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Ringkasan',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ringkasan berdasarkan Supplier',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.rekap.supplier.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDetailRekapPembelianBarang(Request $request)
    {
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $data = PurchaseOrderDetail::leftJoin('product', 'product.id', '=', 'purchase_order_detail.id_item')
                                    ->leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'purchase_order_detail.id_satuan',
                                        'product_unit.nama_satuan',
                                        DB::raw("SUM(purchase_order_detail.qty_order) AS qty_item"),
                                        DB::raw("SUM(purchase_order_detail.outstanding_qty) AS qty_outstanding"),
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['qty_order', '>', 0]
                                    ])
                                    ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                        $q->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd]);
                                    })
                                    ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                        $q->whereMonth('purchase_order.tanggal_po', Carbon::parse($bulan)->format('m'));
                                        $q->whereYear('purchase_order.tanggal_po', Carbon::parse($bulan)->format('Y'));
                                    })
                                    ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                        $q->whereYear('purchase_order.tanggal_po', Carbon::parse($tahun)->format('Y'));
                                    })
                                    ->groupBy('purchase_order_detail.id_item')
                                    ->get();
        return response()->json($data);
    }

    public function getDetailRekapBarang(Request $request)
    {
        $idProduct = $request->input('idProduct');

        $data = Product::leftJoin('purchase_order_detail', 'product.id', '=', 'purchase_order_detail.id_item')
                        ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                        ->leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                        ->leftJoin('supplier', 'supplier.id', '=', 'purchase_order.id_supplier')
                        ->select(
                            'supplier.id',
                            'supplier.kode_supplier',
                            'supplier.nama_supplier',
                            'product_unit.nama_satuan',
                            DB::raw("SUM(purchase_order_detail.qty_order) AS qty_item"),
                            DB::raw("SUM(purchase_order_detail.outstanding_qty) AS qty_outstanding"))
                        ->where([
                            ['product.id', '=', $idProduct]
                        ])
                        ->groupBy('supplier.id')
                        ->groupBy('purchase_order_detail.id_satuan')
                        ->get();


        return response()->json($data);
    }

    public function getDetailRekapPembelianSupplier(Request $request)
    {
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $transaction = "";

        if ($tglStart != "" && $tglEnd != "") {
            $jmlPO = PurchaseOrder::select('id_supplier', DB::raw("COUNT(*) AS JmlPO"))
                                ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                ->groupBy('id_supplier');

            $jmlPODiterima = PurchaseOrder::select('id_supplier', DB::raw("COUNT(*) AS JmlPODiterima"))
                                        ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                        ->where([
                                            ['outstanding_po', '>', '0']
                                        ])
                                        ->whereRaw('outstanding_po < jumlah_total_po')
                                        ->groupBy('id_supplier');

            $jmlPOFull = PurchaseOrder::select('id_supplier', DB::raw("COUNT(*) AS JmlPOFull"))
                                ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                ->whereIn('status_po', ['full', 'closed'])
                                ->groupBy('id_supplier');

            $jmlPenagihan = PurchaseOrder::select('id_supplier', DB::raw("COUNT(*) AS JmlInv"))
                                ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                ->where([
                                    ['purchase_invoice.flag_pembayaran', '=', '0']
                                ])
                                ->groupBy('purchase_order.id_supplier');

            $jmlPembayaran = PurchaseOrder::select('id_supplier', DB::raw("COUNT(*) AS JmlInvLunas"))
                                ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                ->where([
                                    ['purchase_invoice.flag_pembayaran', '=', '1'],
                                ])
                                ->groupBy('purchase_order.id_supplier');


            $transaction = Supplier::leftJoinSub($jmlPO, 'jmlPO', function($jmlPO) {
                                        $jmlPO->on('supplier.id', '=', 'jmlPO.id_supplier');
                                    })
                                    ->leftJoinSub($jmlPODiterima, 'jmlPODiterima', function($jmlPODiterima) {
                                        $jmlPODiterima->on('supplier.id', '=', 'jmlPODiterima.id_supplier');
                                    })
                                    ->leftJoinSub($jmlPOFull, 'jmlPOFull', function($jmlPOFull) {
                                        $jmlPOFull->on('supplier.id', '=', 'jmlPOFull.id_supplier');
                                    })
                                    ->leftJoinSub($jmlPenagihan, 'jmlPenagihan', function($jmlPenagihan) {
                                        $jmlPenagihan->on('supplier.id', '=', 'jmlPenagihan.id_supplier');
                                    })
                                    ->leftJoinSub($jmlPembayaran, 'jmlPembayaran', function($jmlPembayaran) {
                                        $jmlPembayaran->on('supplier.id', '=', 'jmlPembayaran.id_supplier');
                                    })
                                    ->select(
                                        'supplier.id',
                                        'supplier.kode_supplier',
                                        'supplier.nama_supplier',
                                        'jmlPO.JmlPO',
                                        'jmlPODiterima.JmlPODiterima',
                                        'jmlPOFull.JmlPOFull',
                                        'jmlPenagihan.JmlInv',
                                        'jmlPembayaran.JmlInvLunas',
                                    )
                                    ->where([
                                        ['jmlPO.JmlPO', '!=', '0']
                                    ])
                                    ->get();
        }

        return response()->json($transaction);
    }

    public function getDetailRekapSupplier(Request $request)
    {
        $idCust = $request->input('idSupplier');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $data = "";

        if ($tglStart != "" && $tglEnd != "") {
            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $data = PurchaseOrder::leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                ->leftJoin('purchase_order_detail', 'purchase_order.id', '=', 'purchase_order_detail.id_po')
                                ->leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', '=', 'product_unit.id')
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'purchase_order.id',
                                    'purchase_order.no_po',
                                    'purchase_order.tanggal_po',
                                    'product.nama_item',
                                    'product_unit.nama_satuan',
                                    'purchase_order_detail.qty_order',
                                    'purchase_order_detail.outstanding_qty',
                                    'dataSpek.value_spesifikasi'
                                )
                                ->where([
                                    ['purchase_order.id_supplier', '=', $idCust]
                                ])
                                ->whereBetween('purchase_order.tanggal_po', [$tglStart, $tglEnd])
                                ->get();
        }

        return response()->json($data);
    }
}
