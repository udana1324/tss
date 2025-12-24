<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Exports\OutstandingPOExport;
use App\Exports\OutstandingSOExport;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Stock\StockAdjustment;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Exports\StockCardExport;
use App\Models\Library\Customer;
use App\Models\Library\Supplier;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductSpecification;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductUnit;
use App\Models\Production\ProductionDelivery;
use App\Models\Production\ProductionReceiving;
use App\Models\Purchasing\Receiving;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesOrder;
use App\Models\Stock\StockIndex;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Stock'],
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
                                                ['module.url', '=', '/Stock'],
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
                                        ->get('nama_item');

                $dataCategory = ProductCategory::distinct()->get('nama_kategori');
                $dataBrand = ProductBrand::distinct()->get('nama_merk');
                $kodeSP = ProductDetailSpecification::distinct()
                                                    ->leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                                    ->where([
                                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                                    ])
                                                    ->get('value_spesifikasi');

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

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $data['kodeSP'] = $kodeSP;
                $data['listIndex'] = $list;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Stok',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Stok',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexOutstandingSO()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/OutstandingSO'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $userGroup = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/OutstandingSO'],
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
                                        ->get('nama_item');

                $dataCustomer = Customer::all();

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Outstanding SO',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Outstanding SO',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.outstanding_so.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexOutstandingPO()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/OutstandingPO'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $userGroup = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/OutstandingPO'],
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
                                        ->get('nama_item');

                $dataSupplier = Supplier::all();

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Outstanding PO',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Outstanding PO',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.outstanding_po.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Stock/Adjustment'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $dataProduct = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();

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

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['listIndex'] = $list;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Adjustment',
                    'action' => 'Buat',
                    'desc' => 'Buat Adjustment',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('stock_adjustment')->where('kode_transaksi', '=', 'DRAFT')->delete();

                return view('pages.stock.stock.addAdjustment', $data);
            }
            else {
                return redirect('/Stock/Adjustment')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexAdjustment()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Stock/Adjustment'],
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
                                                ['module.url', '=', '/Stock/Adjustment'],
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
                                        ->get('nama_item');
                $dataCategory = ProductCategory::distinct()->get('nama_kategori');
                $dataBrand = ProductBrand::distinct()->get('nama_merk');


                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Adjustment Stok',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Adjustment Stok',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock.indexAdjustment', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexMutasi()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Stock/Mutasi'],
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
                                                ['module.url', '=', '/Stock/Mutasi'],
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
                                        ->get('nama_item');

                $dataSpek1 = ProductSpecification::distinct()
                                    ->leftJoin('product_detail_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ])
                                    ->get();
                $dataCategory = ProductCategory::distinct()->get('nama_kategori');
                $dataBrand = ProductBrand::distinct()->get('nama_merk');

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $data['dataSpek'] = $dataSpek1;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Mutasi',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Mutasi',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock.indexMutasi', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex(Request $request)
    {
        $idIndex = $request->input('idIndex');

        // $stokIn = StockTransaction::select('id_item', 'id_index', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
        //                             ->where([
        //                                         ['transaksi', '=', 'in']
        //                                     ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan');
        //                             // ->groupBy('id_index');

        // $stokOut = StockTransaction::select('id_item', 'id_index', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
        //                             ->where([
        //                                 ['transaksi', '=', 'out']
        //                             ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan');
        //                             // ->groupBy('id_index');

        $dataStocks = StockTransaction::select(
                                        'stock_transaction.id_item',
                                        'stock_transaction.id_index',
                                        'stock_transaction.id_satuan',
                                        DB::raw("SUM(
                                            CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                                                    Else -stock_transaction.qty_item
                                            End
                                        ) AS qty")
                                    )
                                    ->groupBy('stock_transaction.id_item')
                                    ->groupBy('stock_transaction.id_satuan');
                                    // ->groupBy('stock_transaction.id_index')

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
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

        $dataStoks = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                            ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                            ->leftJoin('product_detail', 'product.id', '=', 'product_detail.id_product')
                            ->leftJoin('product_unit', 'product_unit.id', '=', 'product_detail.id_satuan')
                            // ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                            //     $join_in->on('product_detail.id_product', '=', 'stokIn.id_item');
                            //     $join_in->on('product_detail.id_satuan', '=', 'stokIn.id_satuan');
                            // })
                            // ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                            //     $join_out->on('product_detail.id_product', '=', 'stokOut.id_item');
                            //     $join_out->on('product_detail.id_satuan', '=', 'stokOut.id_satuan');
                            //     // $join_out->on('stokIn.id_index', '=', 'stokOut.id_index');
                            // })
                            ->leftJoinSub($dataStocks, 'dataStocks', function($dataStocks) {
                                $dataStocks->on('product.id', '=', 'dataStocks.id_item');
                                $dataStocks->on('product_detail.id_satuan', '=', 'dataStocks.id_satuan');
                            })
                            ->select('product.id',
                                'product.kode_item',
                                'product.nama_item',
                                'product.jenis_item',
                                'product_brand.nama_merk',
                                'product_category.nama_kategori',
                                'product_detail.stok_minimum',
                                'product_detail.stok_maksimum',
                                'product_unit.nama_satuan',
                                DB::raw('product_unit.id as id_satuan'),
                                // 'dataStocks.qty'
                                DB::raw('COALESCE(dataStocks.qty,0) AS stok_item')
                            )
                            ->when($idIndex != null, function($q) use ($idIndex) {
                                $q->where('dataStocks.id_index', $idIndex);
                            })
                            ->whereIn('product.id', function($query) {
                                $query->select('id_item')->from('stock_transaction');
                            })
                            ->where([
                                ['product_detail.deleted_at', '=', null]
                            ])
                            ->orderBy('product.id', 'desc')
                            ->get();

        $stok = [];
        foreach($dataStoks as $dataStock) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataStock->id_index) {
                    $idIndex = $txt["id"];
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $dataStock->id,
                'nama_merk' => $dataStock->nama_merk,
                'nama_kategori' => $dataStock->nama_kategori,
                'nama_item' => $dataStock->nama_item,
                'kode_item' => $dataStock->kode_item,
                // 'value_spesifikasi' => $dataStock->value_spesifikasi,
                'stok_item' => $dataStock->stok_item,
                'id_satuan' => $dataStock->id_satuan,
                'nama_satuan' => $dataStock->nama_satuan,
                'stok_minimum' => $dataStock->stok_minimum,
                'stok_maksimum' => $dataStock->stok_maksimum,
                'txt_index' => $txtIndex,
            ];
            array_push($stok, $dataAlloc);
        }

        return response()->json($stok);
    }

    public function getDataPerIndex(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idSatuan = $request->input('idSatuan');

        $stokIn = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in']
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan')
                                    ->groupBy('id_index');

        $stokOut = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out']
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan')
                                    ->groupBy('id_index');

        $dataStocks = StockTransaction::select(
                                        'stock_transaction.id_item',
                                        'stock_transaction.id_index',
                                        'stock_transaction.id_satuan',
                                        'stock_transaction.jenis_sumber',
                                        DB::raw("SUM(
                                            CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                                                    Else -stock_transaction.qty_item
                                            End
                                        ) AS qty")
                                    )
                                    ->groupBy('stock_transaction.id_item')
                                    ->groupBy('stock_transaction.id_satuan')
                                    ->groupBy('stock_transaction.jenis_sumber')
                                    ->groupBy('stock_transaction.id_index');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
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

        $dataStoks = Product::leftJoinSub($dataStocks, 'dataStocks', function($join_in) use ($idSatuan) {
                                $join_in->on('product.id', '=', 'dataStocks.id_item');
                                $join_in->where('dataStocks.id_satuan', '=', $idSatuan);
                            })
                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                            })
                            ->select('product.id',
                                'dataStocks.id_index',
                                'dataStocks.jenis_sumber',
                                DB::raw('COALESCE(dataStocks.qty,0) AS stok_item')
                            )
                            ->where([
                                ['product.id', '=', $idProduct],

                            ])
                            // ->whereRaw("COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) <> 0")
                            ->get();

        $stok = [];
        foreach($dataStoks as $dataStock) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataStock->id_index) {
                    $idIndex = $txt["id"];
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $dataStock->id,
                'stok_item' => $dataStock->stok_item,
                'jenis_sumber' => $dataStock->jenis_sumber,
                'txt_index' => $txtIndex,
            ];
            array_push($stok, $dataAlloc);
        }

        return response()->json($stok);
    }

    public function getDataIndexAdjustment()
    {
        $detailItem = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                    ->select('product_detail.id_product', 'product_detail.id_satuan', 'product_unit.nama_satuan', 'product_detail.stok_minimum', 'product_detail.stok_maksimum');


        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataAdjustment = StockAdjustment::leftJoin('product', 'stock_adjustment.id_item', '=', 'product.id')
                                        ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                        ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                        ->leftJoinSub($detailItem, 'detailItem', function($join_out) {
                                            $join_out->on('stock_adjustment.id_item', '=', 'detailItem.id_product');
                                            $join_out->on('stock_adjustment.id_satuan', '=', 'detailItem.id_satuan');
                                        })
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'stock_adjustment.id',
                                            'stock_adjustment.kode_transaksi',
                                            'stock_adjustment.tgl_transaksi',
                                            'stock_adjustment.jenis_adjustment',
                                            'stock_adjustment.qty_item',
                                            'stock_adjustment.keterangan',
                                            'stock_adjustment.id_index',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product.jenis_item',
                                            'product_brand.nama_merk',
                                            'product_category.nama_kategori',
                                            'detailItem.stok_minimum',
                                            'detailItem.stok_maksimum',
                                            'detailItem.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->orderBy('stock_adjustment.tgl_transaksi', 'desc')
                                        ->get();

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

        $dataAdj = [];
        foreach($dataAdjustment as $data) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $data->id_index) {
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $data->id,
                'kode_transaksi' => $data->kode_transaksi,
                'tgl_transaksi' => $data->tgl_transaksi,
                'jenis_adjustment' => $data->jenis_adjustment,
                'qty_item' => $data->qty_item,
                'id_index' => $data->id_index,
                'kode_item' => $data->kode_item,
                'nama_item' => $data->nama_item,
                'jenis_item' => $data->jenis_item,
                'nama_merk' => $data->nama_merk,
                'nama_kategori' => $data->nama_kategori,
                'stok_minimum' => $data->stok_minimum,
                'stok_maksimum' => $data->stok_maksimum,
                'nama_satuan' => $data->nama_satuan,
                'txt_index' => $txtIndex,
                'value_spesifikasi' => $data->value_spesifikasi,
                'keterangan' => $data->keterangan,
            ];
            array_push($dataAdj, $dataAlloc);
        }


        return response()->json($dataAdj);
    }

    public function getDataIndexOutstandingSO(Request $request)
    {
        $periode = $request->input('periode');
         $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataOutstanding = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->leftJoin('sales_order', 'sales_order_detail.id_so', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                            })
                                            ->select(
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'sales_order.id',
                                                'sales_order_detail.harga_jual',
                                                'sales_order_detail.qty_outstanding',
                                                'sales_order.tanggal_so',
                                                'sales_order.tanggal_request',
                                                'sales_order.no_so',
                                                'sales_order.status_so',
                                                'customer.nama_customer',
                                                'dataSpek.value_spesifikasi'
                                            )
                                            ->when($periode != "", function($q) use ($periode) {
                                                $q->whereMonth('sales_order.tanggal_so', Carbon::parse($periode)->format('m'));
                                                $q->whereYear('sales_order.tanggal_so', Carbon::parse($periode)->format('Y'));
                                            })
                                            ->whereNotIn('sales_order.status_so', ['draft', 'batal', 'close'])
                                            ->where([
                                                ['sales_order_detail.qty_outstanding', '>', 0],
                                                ['sales_order.outstanding_so', '>', 0],
                                            ])
                                            ->get();


        return response()->json($dataOutstanding);
    }

    public function getDataIndexOutstandingPO(Request $request)
    {
        $periode = $request->input('periode');

         $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataOutstanding = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->leftJoin('purchase_order', 'purchase_order_detail.id_po', 'purchase_order.id')
                                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                            })
                                            ->select(
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'purchase_order_detail.harga_beli',
                                                'purchase_order_detail.outstanding_qty',
                                                'purchase_order.tanggal_po',
                                                'purchase_order.tanggal_request',
                                                'purchase_order.tanggal_deadline',
                                                'purchase_order.id',
                                                'purchase_order.no_po',
                                                'purchase_order.status_po',
                                                'purchase_order.id_supplier',
                                                'supplier.nama_supplier',
                                                'dataSpek.value_spesifikasi'
                                            )
                                            ->when($periode != "", function($q) use ($periode) {
                                                $q->whereMonth('purchase_order.tanggal_po', Carbon::parse($periode)->format('m'));
                                                $q->whereYear('purchase_order.tanggal_po', Carbon::parse($periode)->format('Y'));
                                            })
                                            ->whereNotIn('purchase_order.status_po', ['draft', 'batal', 'close'])
                                            ->where([
                                                ['purchase_order_detail.outstanding_qty', '>', 0],
                                                ['purchase_order.outstanding_po', '>', 0],
                                            ])
                                            ->get();


        return response()->json($dataOutstanding);
    }

    public function getStockTransaction(Request $request)
    {

        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $transaction = "";


        if ($jenisPeriode != null) {

            $stokInAwal = StockTransaction::select('id_item', 'jenis_sumber', 'id_satuan', DB::raw("SUM(qty_item) AS stok_in"))
                                        ->where([
                                            ['transaksi', '=', 'in']
                                        ])
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart) {
                                            $q->whereDate('tgl_transaksi', '<=', Carbon::parse($tglStart)->subDays(1)->format('Y-m-d'));
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereDate('tgl_transaksi', '<=', Carbon::parse($bulan)->subMonth()->endOfMonth()->format('Y-m-d'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('tgl_transaksi', '<=',  Carbon::parse($tahun)->subYears(1)->format('Y'));
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('jenis_sumber');

            $stokOutAwal = StockTransaction::select('id_item', 'jenis_sumber', 'id_satuan', DB::raw("SUM(qty_item) AS stok_out"))
                                        ->where([
                                            ['transaksi', '=', 'out']
                                        ])
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart) {
                                            $q->whereDate('tgl_transaksi', '<=', Carbon::parse($tglStart)->subDays(1)->format('Y-m-d'));
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereDate('tgl_transaksi', '<=', Carbon::parse($bulan)->subMonth()->endOfMonth()->format('Y-m-d'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('tgl_transaksi', '<=',  Carbon::parse($tahun)->subYears(1)->format('Y'));
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('jenis_sumber');

            $stokIn = StockTransaction::select('id_item', 'jenis_sumber', 'id_satuan', DB::raw("SUM(qty_item) AS stok_in"))
                                        ->where([
                                            ['transaksi', '=', 'in']
                                        ])
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('tgl_transaksi', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('tgl_transaksi', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('tgl_transaksi', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('tgl_transaksi', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('jenis_sumber');

            $stokOut = StockTransaction::select('id_item', 'jenis_sumber', 'id_satuan', DB::raw("SUM(qty_item) AS stok_out"))
                                        ->where([
                                            ['transaksi', '=', 'out']
                                        ])
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('tgl_transaksi', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('tgl_transaksi', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('tgl_transaksi', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('tgl_transaksi', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('jenis_sumber');

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);


            $transaction = Product::leftJoinSub($stokInAwal, 'stokInAwal', function($join_in) {
                                        $join_in->on('product.id', '=', 'stokInAwal.id_item');
                                    })
                                    ->leftJoinSub($stokOutAwal, 'stokOutAwal', function($join_out) {
                                        $join_out->on('stokInAwal.id_item', '=', 'stokOutAwal.id_item');
                                        $join_out->on('stokInAwal.jenis_sumber', '=', 'stokOutAwal.jenis_sumber');
                                    })
                                    ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                        $join_in->on('stokInAwal.id_item', '=', 'stokIn.id_item');
                                        $join_in->on('stokInAwal.jenis_sumber', '=', 'stokIn.jenis_sumber');
                                    })
                                    ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                        $join_out->on('product.id', '=', 'stokOut.id_item');
                                        $join_out->on('stokInAwal.id_item', '=', 'stokOut.id_item');
                                        $join_out->on('stokInAwal.jenis_sumber', '=', 'stokOut.jenis_sumber');
                                    })
                                    ->leftJoin('product_unit', function($join) {
                                        $join->on('product_unit.id', '=', 'stokInAwal.id_satuan');
                                        $join->orOn('product_unit.id', '=', 'stokOut.id_satuan');
                                    })
                                    ->leftJoin('product_detail', function($join) {
                                        $join->on('product_detail.id_satuan', '=', 'stokInAwal.id_satuan')->where('product_detail.id_product', '=', 'product.id');
                                        $join->orOn('product_detail.id_satuan', '=', 'stokOut.id_satuan')->where('product_detail.id_product', '=', 'product.id');
                                    })
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                    ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                    ->select('product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product.jenis_item',
                                        'product_detail.id_satuan',
                                        'product_detail.stok_minimum',
                                        'product_detail.stok_maksimum',
                                        'product_unit.nama_satuan',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
                                        'dataSpek.value_spesifikasi',
                                        'stokInAwal.jenis_sumber',
                                        DB::raw('COALESCE(stokInAwal.stok_in,0) - COALESCE(stokOutAwal.stok_out,0) AS stok_awal'),
                                        DB::raw('COALESCE(stokIn.stok_in,0) AS stok_in'),
                                        DB::raw('COALESCE(stokOut.stok_out,0) AS stok_out'),
                                        DB::raw('(COALESCE(stokInAwal.stok_in,0) - COALESCE(stokOutAwal.stok_out,0)) + (COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0)) AS stok_akhir'))
                                    ->whereIn('product.id', function($subQuery) use ($jenisPeriode, $tglStart, $tglEnd, $bulan, $tahun) {
                                        $subQuery->select('id_item')
                                                ->from('stock_transaction')
                                                ->where([
                                                    ['qty_item', '>', 0]
                                                ])
                                                ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                    $q->whereBetween('tgl_transaksi', [$tglStart, $tglEnd]);
                                                })
                                                ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                    $q->whereDate('tgl_transaksi', '<=', Carbon::parse($bulan)->subMonth()->endOfMonth()->format('Y-m-d'));
                                                })
                                                ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                    $q->whereYear('tgl_transaksi', '=',  Carbon::parse($tahun)->format('Y'));
                                                });
                                    })
                                    ->where([
                                        // ['product.id', '=', 4],
                                        ['stokInAwal.stok_in', '>', 0]
                                    ])
                                    ->orderBy('product.nama_item', 'asc')
                                    ->get();
        }

        return response()->json($transaction);
    }

    public function getStockDetailPerItem(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idSatuan = $request->input('idSatuan');
        $jenisPeriode = $request->input('jenisPeriode');
        $idIndex = $request->input('idIndex');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $transaction = "";
        $dataTransaction = [];

        if ($jenisPeriode != null) {

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

            $customerName = Delivery::select('delivery.kode_pengiriman', 'customer.nama_customer')
                                ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                ->where([
                                    ['delivery.status_pengiriman', '=', 'posted'],
                                ]);

            $supplierName = Receiving::select('receiving.kode_penerimaan', 'supplier.nama_supplier')
                                ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                ->where([
                                    ['receiving.status_penerimaan', '=', 'posted'],
                                ]);

            $supplierProductionName = ProductionReceiving::select('production_receiving.kode_penerimaan', 'supplier.nama_supplier')
                                ->leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                                ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                                ->where([
                                    ['production_receiving.status_penerimaan', '=', 'posted'],
                                ]);

            $deliveryProductionName = ProductionDelivery::select('production_delivery.kode_pengiriman', 'supplier.nama_supplier')
                                ->leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                                ->where([
                                    ['production_delivery.status_pengiriman', '=', 'posted'],
                                ]);

            $transaction = StockTransaction::leftJoinSub($customerName, 'customerName', function($customerName) {
                                                $customerName->on('stock_transaction.kode_transaksi', '=', 'customerName.kode_pengiriman');
                                            })
                                            ->leftJoinSub($supplierName, 'supplierName', function($supplierName) {
                                                $supplierName->on('stock_transaction.kode_transaksi', '=', 'supplierName.kode_penerimaan');
                                            })
                                            ->leftJoinSub($supplierProductionName, 'supplierProductionName', function($supplierProductionName) {
                                                $supplierProductionName->on('stock_transaction.kode_transaksi', '=', 'supplierProductionName.kode_penerimaan');
                                            })
                                            ->leftJoinSub($deliveryProductionName, 'deliveryProductionName', function($deliveryProductionName) {
                                                $deliveryProductionName->on('stock_transaction.kode_transaksi', '=', 'deliveryProductionName.kode_pengiriman');
                                            })
                                            ->select(
                                                'stock_transaction.*',
                                                DB::raw("CASE WHEN stock_transaction.jenis_transaksi = 'pengiriman' THEN customerName.nama_customer
                                                              WHEN stock_transaction.jenis_transaksi = 'pengiriman_produksi' THEN deliveryProductionName.nama_supplier
                                                              WHEN stock_transaction.jenis_transaksi = 'penerimaan_produksi' THEN supplierProductionName.nama_supplier
                                                              ELSE supplierName.nama_supplier
                                                         END AS customer_vendor")
                                            )
                                            ->where([
                                                ['id_item', '=', $idProduct],
                                                ['id_satuan', '=', $idSatuan]
                                            ])
                                            ->when($idIndex != "all", function($q) use ($idIndex) {
                                                $q->where('id_index', $idIndex);
                                            })
                                            // ->where(function($query) {
                                            //     $query->where('customerName.nama_customer', '!=', null);
                                            //     $query->orWhere('supplierName.nama_supplier', '!=', null);
                                            // })
                                            ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                $q->whereBetween('tgl_transaksi', [$tglStart, $tglEnd]);
                                            })
                                            ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                $q->whereMonth('tgl_transaksi', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('tgl_transaksi', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                $q->whereYear('tgl_transaksi', Carbon::parse($tahun)->format('Y'));
                                            })
                                            ->orderBy('tgl_transaksi', 'desc')
                                            ->get();


            foreach($transaction as $dataStock) {
                $txtIndex = "-";
                foreach ($list as $txt) {
                    if ($txt["id"] == $dataStock->id_index) {
                        $idIndex = $txt["id"];
                        $txtIndex = $txt["nama_index"];
                    }
                }
                $dataAlloc = [
                    'id' => $dataStock->id,
                    'transaksi' => $dataStock->transaksi,
                    'tgl_transaksi' => $dataStock->tgl_transaksi,
                    'kode_transaksi' => $dataStock->kode_transaksi,
                    'jenis_transaksi' => $dataStock->jenis_transaksi,
                    'qty_item' => $dataStock->qty_item,
                    'customer_vendor' => $dataStock->customer_vendor,
                    'status_stok' => $dataStock->status_stok,
                    'txt_index' => $txtIndex,
                ];
                array_push($dataTransaction, $dataAlloc);
            }
        }

        return response()->json($dataTransaction);
    }

    public function detail($id, $idSatuan)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Stock'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataProduct = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                        ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select('product.id',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product.jenis_item',
                                            'product_brand.nama_merk',
                                            'product_category.nama_kategori',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['product.id', '=', $id]
                                        ])
                                        ->first();

                $dataSatuan = ProductUnit::find($idSatuan);

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

                $data['hakAkses'] = $hakAkses;
                $data['listIndex'] = $list;
                $data['dataProduct'] = $dataProduct;
                $data['dataSatuan'] = $dataSatuan;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Stok Barang',
                    'action' => 'Detail',
                    'desc' => 'Detail Stok Barnag',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock.detailPerItem', $data);
            }
            else {
                return redirect('/Stock')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getAdjustment(Request $request)
    {
        $kode = $request->input('kodeAdjustment');

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

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataAdjustment = StockAdjustment::leftJoin('product', 'stock_adjustment.id_item', '=', 'product.id')
                                        ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                        ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                        ->leftJoin('product_unit', 'stock_adjustment.id_satuan', '=', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'stock_adjustment.id',
                                            'stock_adjustment.kode_transaksi',
                                            'stock_adjustment.tgl_transaksi',
                                            'stock_adjustment.jenis_adjustment',
                                            'stock_adjustment.qty_item',
                                            'stock_adjustment.id_index',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product.jenis_item',
                                            'product_brand.nama_merk',
                                            'product_category.nama_kategori',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['kode_transaksi', '=', $kode]
                                        ])
                                        ->get();

        $dataAdj = [];
        foreach($dataAdjustment as $data) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $data->id_index) {
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $data->id,
                'kode_transaksi' => $data->kode_transaksi,
                'tgl_transaksi' => $data->tgl_transaksi,
                'jenis_adjustment' => $data->jenis_adjustment,
                'qty_item' => $data->qty_item,
                'id_index' => $data->id_index,
                'kode_item' => $data->kode_item,
                'value_spesifikasi' => $data->value_spesifikasi,
                'nama_item' => $data->nama_item,
                'jenis_item' => $data->jenis_item,
                'nama_merk' => $data->nama_merk,
                'nama_kategori' => $data->nama_kategori,
                'stok_minimum' => $data->stok_minimum,
                'stok_maksimum' => $data->stok_maksimum,
                'nama_satuan' => $data->nama_satuan,
                'txt_index' => $txtIndex,
            ];
            array_push($dataAdj, $dataAlloc);
        }


        return response()->json($dataAdj);
    }

    public function GetDataProduct(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idSatuan = $request->input('idSatuan');
        $idIndex = $request->input('idIndex');

        $stokIn = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in'],
                                                ['id_index', '=', $idIndex]
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        $stokOut = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out'],
                                        ['id_index', '=', $idIndex]
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');
        $detail = ProductDetail::select('id_item', 'id_satuan', 'stok_minimum', 'stok_maksimum')
                                    ->where([
                                        ['id_item', '=', $idProduct],
                                        ['id_satuan', '=', $idSatuan]
                                    ]);

        $dataStocks = StockTransaction::select(
                                        'stock_transaction.id_item',
                                        'stock_transaction.id_index',
                                        'stock_transaction.id_satuan',
                                        DB::raw("SUM(
                                            CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                                                    Else -stock_transaction.qty_item
                                            End
                                        ) AS qty")
                                    )
                                    ->where([
                                        ['stock_transaction.id_index', '=', $idIndex]
                                    ])
                                    ->groupBy('stock_transaction.id_item')
                                    ->groupBy('stock_transaction.id_satuan')
                                    ->groupBy('stock_transaction.id_index');


        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataAdjustment = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                ->leftJoin('product_detail', function($join) use ($idSatuan) {
                                    $join->on('product_detail.id_product', '=', 'product.id')->where('product_detail.id_satuan', '=', $idSatuan);
                                })
                                ->leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->leftJoinSub($dataStocks, 'dataStocks', function($join_in) {
                                    $join_in->on('product_detail.id_product', '=', 'dataStocks.id_item');
                                    $join_in->on('product_detail.id_satuan', '=', 'dataStocks.id_satuan');
                                })
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'product.id',
                                    'product.kode_item',
                                    'product.nama_item',
                                    'product.jenis_item',
                                    'product_brand.nama_merk',
                                    'product_category.nama_kategori',
                                    'product_detail.stok_minimum',
                                    'product_detail.stok_maksimum',
                                    'product_unit.nama_satuan',
                                    DB::raw('product_unit.id as id_satuan'),
                                    DB::raw('COALESCE(dataStocks.qty,0) AS stok_item'),
                                    'dataSpek.value_spesifikasi'
                                )
                                ->where([
                                    ['product.id', '=', $idProduct],
                                    ['product_unit.id', '=', $idSatuan]
                                ])
                                ->get();


        return response()->json($dataAdjustment);
    }

    public function StoreAdjustment(Request $request)
    {
        $idItem = $request->input('idItem');
        $idSatuan = $request->input('idSatuan');
        $idIndex = $request->input('idIndex');
        $qty = $request->input('qtyItem');
        $jenisAdjustment = $request->input('jenisAdjustment');
        $tgl = $request->input('tgl');
        $keterangan = $request->input('keterangan');
        $user = Auth::user()->user_name;
        $qty = str_replace(",", ".", $qty);

        if ($jenisAdjustment == "retur_purc") {
            $jenisTransaksi = "out";
        }
        else if ($jenisAdjustment == "retur_sale") {
            $jenisTransaksi = "in";
        }
        else if ($jenisAdjustment == "penambahan") {
            $jenisTransaksi = "in";
        }
        else if ($jenisAdjustment == "pengurangan") {
            $jenisTransaksi = "out";
        }

        // if ($jenisTransaksi == "in") {
        //     $jenisSumber = 5;
        // }
        // else {
        //     $jenisSumber = 0;
        // }

        $adj = new StockAdjustment();
        $adj->kode_transaksi = 'DRAFT';
        $adj->id_index = $idIndex;
        $adj->id_item = $idItem;
        $adj->id_satuan = $idSatuan;
        $adj->qty_item = $qty;
        $adj->jenis_transaksi = $jenisTransaksi;
        $adj->jenis_adjustment = $jenisAdjustment;
        $adj->tgl_transaksi = $tgl;
        $adj->keterangan = $keterangan;
        $adj->jenis_sumber = 5;
        $adj->created_by = $user;
        $adj->save();

        $log = ActionLog::create([
            'module' => 'Adjustment',
            'action' => 'Simpan',
            'desc' => 'Simpan Adjustment',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function DeleteAdjustment(Request $request)
    {
        $id = $request->input('idDetail');

        $dataAdjustment = StockAdjustment::find($id);

        if ($dataAdjustment->kode_transaksi == "DRAFT") {
            $delete = DB::table('stock_adjustment')->where('id', '=', $id)->delete();
        }
        else {
            $delete = DB::table('stock_adjustment')->where('id', '=', $id)->delete();
            $deleteTransaction = DB::table('stock_transaction')->where('kode_transaksi', '=', $dataAdjustment->kode_transaksi)->delete();
        }

        return response()->json("success");
    }

    public function store(Request $request)
    {
        $user = Auth::user()->user_name;



        $dataAdjustment = StockAdjustment::where([
                                            ['kode_transaksi', '=', 'draft'],
                                            ['created_by', '=', $user]
                                        ])
                                        ->get();
        if (count($dataAdjustment) > 0) {
            foreach ($dataAdjustment as $adj) {
                $blnPeriode = date("m", strtotime($adj->tgl_transaksi));
                $thnPeriode = date("Y", strtotime($adj->tgl_transaksi));

                $countKode = DB::table('stock_adjustment')
                                ->select(DB::raw("MAX(RIGHT(kode_transaksi,2)) AS angka"))
                                //->whereMonth('tanggal_so', $blnPeriode)
                                // ->whereYear('tgl_transaksi', $thnPeriode)
                                ->whereDate('tgl_transaksi', $adj->tgl_transaksi)
                                ->where([
                                    ['kode_transaksi', '!=', 'DRAFT']
                                ])
                                ->first();
                $count = $countKode->angka;
                $counter = $count + 1;

                $kodeTgl = Carbon::parse($adj->tgl_transaksi)->format('ymd');

                if ($counter < 10) {
                    $nmrAdj = "adj-cv-".$kodeTgl."0".$counter;
                }
                else {
                    $nmrAdj = "adj-cv-".$kodeTgl.$counter;
                }

                $adjustment = StockAdjustment::find($adj->id);
                $adjustment->kode_transaksi = $nmrAdj;
                $adjustment->save();

                $stockTransaction = new StockTransaction();
                $stockTransaction->kode_transaksi = $nmrAdj;
                $stockTransaction->id_item = $adj->id_item;
                $stockTransaction->id_satuan = $adj->id_satuan;
                $stockTransaction->id_index = $adj->id_index;
                $stockTransaction->qty_item = $adj->qty_item;
                $stockTransaction->tgl_transaksi = Carbon::parse($adj->tgl_transaksi)->format('Y-m-d');
                $stockTransaction->jenis_transaksi = $adj->jenis_transaksi;
                $stockTransaction->transaksi = $adj->jenis_transaksi;
                $stockTransaction->jenis_sumber = $adj->jenis_sumber;
                $stockTransaction->created_by = $user;
                $stockTransaction->save();

            }


            $log = ActionLog::create([
                'module' => 'Adjustment',
                'action' => 'Simpan',
                'desc' => 'Simpan Adjustment',
                'username' => Auth::user()->user_name
            ]);

            if ($adj) {
                return redirect('/Stock/Adjustment')->with('success', 'Data Adjustment Telah Disimpan!');
            }
            else {
                return redirect('/Stock/Adjustment')->with('danger', 'Simpan Adjustment Gagal!');
            }
        }
        else {
            return redirect()->back()->with('danger', 'Tidak Terdapat Data Adjustment untuk Disimpan!');
        }

    }

    public function exportStockCard(Request $request)
    {
        return Excel::download(new StockCardExport($request), 'KartuStok.xlsx');
    }

    public function exportOutstandingSO(Request $request)
    {
        return Excel::download(new OutstandingSOExport($request), 'OutstandingSO.xlsx');
    }

    public function exportOutstandingPO(Request $request)
    {
        return Excel::download(new OutstandingPOExport($request), 'OutstandingPO.xlsx');
    }
}
