<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerProduct;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\DeliveryTerms;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperDelivery;
use App\Exports\RekapBarangExport;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use App\Models\Sales\SalesOrderTerms;
use App\Models\Library\Sales;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductUnit;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class RekapPenjualanController extends Controller
{
    public function indexBarang()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/RekapPenjualanBarang'],
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
                                                ['module.url', '=', '/RekapPenjualanBarang'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::distinct()->get();
                $dataSatuan = ProductUnit::distinct()->get('nama_satuan');
                $dataCustomer = Customer::distinct()->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataSatuan'] = $dataSatuan;
                $data['dataCust'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Ringkasan Penjualan',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ringkasan berdasarkan Barang',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.rekap.barang.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexCustomer()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/RekapPenjualanCustomer'],
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
                                                ['module.url', '=', '/RekapPenjualanCustomer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::distinct()->get();
                $dataCustomer = Customer::distinct()->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Ringkasan Penjualan',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ringkasan berdasarkan Customer',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.rekap.customer.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDetailRekapPenjualanBarang(Request $request)
    {
        $jenisPeriode = $request->input('jenisPeriode');
        $idCust = $request->input('idCust');
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

        $data = Product::leftJoin('sales_order_detail', 'product.id', '=', 'sales_order_detail.id_item')
                        ->leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                        ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                            })
                        ->select(
                            'product.id',
                            'product.kode_item',
                            'product.nama_item',
                            'sales_order_detail.id_satuan',
                            'product_unit.nama_satuan',
                            DB::raw("SUM(sales_order_detail.qty_item) AS qty_item"),
                            DB::raw("SUM(sales_order_detail.qty_outstanding) AS qty_outstanding"),
                            'dataSpek.value_spesifikasi'
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

        return response()->json($data);
    }

    public function getDetailRekapBarang(Request $request)
    {
        $idProduct = $request->input('idProduct');

        $data = Product::leftJoin('sales_order_detail', 'product.id', '=', 'sales_order_detail.id_item')
                        ->leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                        ->leftJoin('customer', 'customer.id', '=', 'sales_order.id_customer')
                        ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                        ->select(
                            'customer.id',
                            'customer.kode_customer',
                            'customer.nama_customer',
                            'sales_order_detail.id_satuan',
                            'product_unit.nama_satuan',
                            DB::raw("SUM(sales_order_detail.qty_item) AS qty_item"),
                            DB::raw("SUM(sales_order_detail.qty_outstanding) AS qty_outstanding"))
                        ->where([
                            ['product.id', '=', $idProduct]
                        ])
                        ->groupBy('customer.id')
                        ->groupBy('sales_order_detail.id_satuan')
                        ->get();


        return response()->json($data);
    }

    public function getDetailRekapLokasi(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $jenisPeriode = $request->input('jenisPeriode');
        $idCust = $request->input('idCust');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $list = [];
        $i = 0;
        foreach ($dataIndex as $index) {
            $txt = "";
            foreach ($index->ancestors as $ancestors) {
                $txt = $txt.$ancestors->nama_index.".";
            }

            $txt = $txt.$index->nama_index;
            $dataTxt = [
                'id' => $index->id,
                'nama_index' => $txt
            ];

            array_push($list, $dataTxt);
        }

        $data = Product::leftJoin('delivery_allocation', 'product.id', 'delivery_allocation.id_item')
                        ->leftJoin('delivery', 'delivery_allocation.id_delivery', '=', 'delivery.id')
                        ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                        ->select(
                            'delivery_allocation.id_index',
                            DB::raw("SUM(delivery_allocation.qty_item) AS qty_item")
                        )
                        ->where([
                            ['product.id', '=', $idProduct]
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
                        ->groupBy('delivery_allocation.id_index')
                        ->get();

        $detail = [];
        foreach($data as $dataAllocation) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataAllocation->id_index) {
                    $idIndex = $txt["id"];
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id_index' => $dataAllocation->id_index,
                'txt_index' => $txtIndex,
                'qty_item' => $dataAllocation->qty_item,
            ];
            array_push($detail, $dataAlloc);
        }


        return response()->json($detail);
    }

    public function getDetailRekapPenjualanCustomer(Request $request)
    {
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $transaction = "";

        if ($tglStart != "" && $tglEnd != "") {
            $jmlSO = SalesOrder::select('id_customer', DB::raw("COUNT(*) AS JmlSO"))
                                ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                ->groupBy('id_customer');

            $jmlSOTerkirim = SalesOrder::select('id_customer', DB::raw("COUNT(*) AS JmlSOTerkirim"))
                                        ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                        ->where([
                                            ['outstanding_so', '>', '0']
                                        ])
                                        ->whereRaw('outstanding_so < jumlah_total_so')
                                        ->groupBy('id_customer');

            $jmlSOFull = SalesOrder::select('id_customer', DB::raw("COUNT(*) AS JmlSOFull"))
                                ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                ->whereIn('status_so', ['full', 'closed'])
                                ->groupBy('id_customer');

            $jmlPenagihan = SalesOrder::select('id_customer', DB::raw("COUNT(*) AS JmlInv"))
                                ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                ->where([
                                    ['sales_invoice.flag_tf', '=', '1'],
                                    ['sales_invoice.flag_pembayaran', '=', '0'],
                                ])
                                ->groupBy('sales_order.id_customer');

            $jmlPembayaran = SalesOrder::select('id_customer', DB::raw("COUNT(*) AS JmlInvLunas"))
                                ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                ->where([
                                    ['sales_invoice.flag_pembayaran', '=', '1'],
                                ])
                                ->groupBy('sales_order.id_customer');


            $transaction = Customer::leftJoinSub($jmlSO, 'jmlSO', function($jmlSO) {
                                        $jmlSO->on('customer.id', '=', 'jmlSO.id_customer');
                                    })
                                    ->leftJoinSub($jmlSOTerkirim, 'jmlSOTerkirim', function($jmlSOTerkirim) {
                                        $jmlSOTerkirim->on('customer.id', '=', 'jmlSOTerkirim.id_customer');
                                    })
                                    ->leftJoinSub($jmlSOFull, 'jmlSOFull', function($jmlSOFull) {
                                        $jmlSOFull->on('customer.id', '=', 'jmlSOFull.id_customer');
                                    })
                                    ->leftJoinSub($jmlPenagihan, 'jmlPenagihan', function($jmlPenagihan) {
                                        $jmlPenagihan->on('customer.id', '=', 'jmlPenagihan.id_customer');
                                    })
                                    ->leftJoinSub($jmlPembayaran, 'jmlPembayaran', function($jmlPembayaran) {
                                        $jmlPembayaran->on('customer.id', '=', 'jmlPembayaran.id_customer');
                                    })
                                    ->leftJoin('sales', 'customer.sales', '=', 'sales.id')
                                    ->select(
                                        'customer.id',
                                        'customer.kode_customer',
                                        'customer.nama_customer',
                                        'sales.nama_sales',
                                        'jmlSO.JmlSO',
                                        'jmlSOTerkirim.JmlSOTerkirim',
                                        'jmlSOFull.JmlSOFull',
                                        'jmlPenagihan.JmlInv',
                                        'jmlPembayaran.JmlInvLunas',
                                    )
                                    ->where([
                                        ['jmlSO.JmlSO', '>', 0]
                                    ])
                                    ->get();
        }

        return response()->json($transaction);
    }

    public function getDetailRekapCustomer(Request $request)
    {
        $idCust = $request->input('idCustomer');
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

            $data = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                ->leftJoin('sales_order_detail', 'sales_order.id', '=', 'sales_order_detail.id_so')
                                ->leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'sales_order.id',
                                    'sales_order.no_so',
                                    'customer_detail.nama_outlet',
                                    'sales_order.tanggal_so',
                                    'product.nama_item',
                                    'product_unit.nama_satuan',
                                    'sales_order_detail.qty_item',
                                    'sales_order_detail.qty_outstanding',
                                    'dataSpek.value_spesifikasi'
                                )
                                ->where([
                                    ['sales_order.id_customer', '=', $idCust]
                                ])
                                ->whereBetween('sales_order.tanggal_so', [$tglStart, $tglEnd])
                                ->get();
        }

        return response()->json($data);
    }

    public function exportDataRekap(Request $request)
    {
        return Excel::download(new RekapBarangExport($request), 'RingkasanPenjualan.xlsx');
    }
}
