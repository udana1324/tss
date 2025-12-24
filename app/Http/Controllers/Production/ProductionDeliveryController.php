<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Production\ProductionDelivery;
use App\Models\Production\ProductionDeliveryDetail;
use App\Models\Production\ProductionDeliveryTerms;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperProductionDelivery;
use App\Exports\ProductionDeliveryExport;
use App\Models\Library\SupplierProduct;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Production\ProductionOrderTerms;
use App\Models\Production\ProductionDeliveryAllocation;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ProductionDeliveryController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductionDelivery'],
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
                                                ['module.url', '=', '/ProductionDelivery'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = ProductionDelivery::distinct()->get('status_pengiriman');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('production_delivery_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;


                $log = ActionLog::create([
                    'module' => 'ProductionDelivery',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan ProductionDelivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_delivery.index', $data);
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
        $periode = $request->input('periode');

        $delivery = ProductionDelivery::leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                                        ->select(
                                            'supplier.nama_supplier',
                                            'production_delivery.id',
                                            'production_delivery.kode_pengiriman',
                                            'production_delivery.jumlah_total_sj',
                                            'production_delivery.tanggal_sj',
                                            'production_delivery.flag_revisi',
                                            'production_delivery.status_pengiriman')
                                        ->when($periode != "", function($q) use ($periode) {
                                            $q->whereMonth('production_delivery.tanggal_sj', Carbon::parse($periode)->format('m'));
                                            $q->whereYear('production_delivery.tanggal_sj', Carbon::parse($periode)->format('Y'));
                                        })
                                        ->orderBy('production_delivery.id', 'desc')
                                        ->get();
        return response()->json($delivery);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionDelivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::orderBy('nama_supplier')->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'ProductionDelivery',
                    'action' => 'Buat',
                    'desc' => 'Buat ProductionDelivery',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('production_delivery_detail')
                            ->where([
                                ['id_delivery', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.production.production_delivery.add', $data);
            }
            else {
                return redirect('/ProductionDelivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionDelivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::orderBy('nama_supplier')->get();

                $dataDlv = ProductionDelivery::leftJoin('supplier_detail', 'production_delivery.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_delivery.id',
                                        'production_delivery.kode_pengiriman',
                                        'production_delivery.id_alamat',
                                        'production_delivery.tanggal_sj',
                                        'production_delivery.status_pengiriman',
                                        'production_delivery.id_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_delivery.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataDlv->status_pengiriman != "draft") {
                    return redirect('/ProductionDelivery')->with('warning', 'Pengiriman tidak dapat diubah karena status Pengiriman bukan DRAFT!');
                }

                // $draftPo = ProductionOrder::find($dataDlv->id_supplier);

                // if ($draftPo->status == "draft") {
                //     return redirect('/ProductionDelivery')->with('warning', 'Tidak dapat mengubah Pengiriman, PO '.strtoupper($draftPo->no_production_order).' Berstatus Draft');
                // }

                // $restore = ProductionDeliveryDetail::onlyTrashed()->where([['id_delivery', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_delivery'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = ProductionDeliveryDetail::where([
                                                    ['id_delivery', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'production_delivery',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_delivery,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_item,
                            'value5' => $detail->keterangan
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $dataTerms = ProductionDeliveryTerms::where('id_delivery', $id)->get();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'ProductionDelivery',
                    'action' => 'Ubah',
                    'desc' => 'Ubah ProductionDelivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_delivery.edit', $data);
            }
            else {
                return redirect('/ProductionDelivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function staging($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionDelivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataDlv = ProductionDelivery::leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_detail', 'production_delivery.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_delivery.id',
                                        'production_delivery.kode_pengiriman',
                                        'production_delivery.id_supplier',
                                        'production_delivery.id_alamat',
                                        'production_delivery.tanggal_sj',
                                        'production_delivery.status_pengiriman',
                                        'production_delivery.flag_revisi',
                                        'production_delivery.jumlah_total_sj',
                                        'supplier.nama_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_delivery.id', '=', $id],
                                    ])
                                    ->first();

                $dataTerms = ProductionDeliveryTerms::where('id_delivery', $id)->get();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailDlv = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_delivery_detail.id',
                                            'production_delivery_detail.id_item',
                                            'production_delivery_detail.id_satuan',
                                            'production_delivery_detail.qty_item',
                                            'production_delivery_detail.id_delivery',
                                            'production_delivery_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_delivery_detail.id_delivery', '=', $id]
                                        ])
                                        ->get();

                $alokasiDlv = ProductionDeliveryAllocation::leftJoin('product_unit', 'production_delivery_allocation.id_satuan', 'product_unit.id')
                ->where([
                    ['production_delivery_allocation.id_delivery', '=', $id]
                ])
                ->get();

                $parentMenu = Module::find($hakAkses->parent);

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

                $dataAlokasiDlv = [];
                foreach($alokasiDlv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_delivery' => $dataAlokasi->id_delivery,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'id_satuan' => $dataAlokasi->id_satuan,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiDlv, $dataAlloc);
                }

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['listIndex'] = $list;
                $data['detailDlv'] = $detailDlv;
                $data['dataAlokasiDlv'] = $dataAlokasiDlv;

                $log = ActionLog::create([
                    'module' => 'ProductionDelivery',
                    'action' => 'Staging',
                    'desc' => 'Staging ProductionDelivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_delivery.staging', $data);
            }
            else {
                return redirect('/ProductionDelivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionDelivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataDlv = ProductionDelivery::leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_detail', 'production_delivery.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_delivery.id',
                                        'production_delivery.kode_pengiriman',
                                        'production_delivery.id_supplier',
                                        'production_delivery.id_alamat',
                                        'production_delivery.tanggal_sj',
                                        'production_delivery.status_pengiriman',
                                        'production_delivery.flag_revisi',
                                        'production_delivery.jumlah_total_sj',
                                        'production_delivery.id_supplier',
                                        'supplier.nama_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_delivery.id', '=', $id],
                                    ])
                                    ->first();

                $dataTerms = ProductionDeliveryTerms::where('id_delivery', $id)->get();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailDlv = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_delivery_detail.id',
                                            'production_delivery_detail.id_item',
                                            'production_delivery_detail.qty_item',
                                            'production_delivery_detail.id_delivery',
                                            'production_delivery_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_delivery_detail.id_delivery', '=', $id]
                                        ])
                                        ->get();

                $alokasiDlv = ProductionDeliveryAllocation::leftJoin('product', 'production_delivery_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'production_delivery_allocation.id_satuan', 'product_unit.id')
                                                ->where([
                                                    ['production_delivery_allocation.id_delivery', '=', $id]
                                                ])
                                                ->get();

                $parentMenu = Module::find($hakAkses->parent);

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

                $dataAlokasiDlv = [];
                foreach($alokasiDlv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_delivery' => $dataAlokasi->id_delivery,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'id_satuan' => $dataAlokasi->id_satuan,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiDlv, $dataAlloc);
                }

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['listIndex'] = $list;
                $data['detailDlv'] = $detailDlv;
                $data['dataAlokasiDlv'] = $dataAlokasiDlv;

                $log = ActionLog::create([
                    'module' => 'ProductionDelivery',
                    'action' => 'Detail',
                    'desc' => 'Detail ProductionDelivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_delivery.detail', $data);
            }
            else {
                return redirect('/ProductionDelivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function RestoreProductionDeliveryDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDlv');
            $restore = ProductionDeliveryDetail::onlyTrashed()->where([['id_delivery', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {

        }
        else {
            return response()->json($exception);
        }

    }

    public function getProductSupplier(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = SupplierProduct::leftJoin('product', 'supplier_product.id_item', 'product.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['supplier_product.id_supplier', '=', $idSupplier],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->orderBy('product.nama_item', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDefaultAddress(Request $request)
    {
        $idSupplier = $request->input('idSupplier');

        $defaultAddress = SupplierDetail::where([
                                            ['id_supplier', '=', $idSupplier],
                                            ['default', '=', 'Y']
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getSupplierAddress(Request $request)
    {
        $idSupplier = $request->input('idSupplier');

        $supplierAddress = SupplierDetail::where([
            ['id_supplier', '=', $idSupplier]
        ])
        ->get();

        return response()->json($supplierAddress);
    }

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');

        $stokIn = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in']
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        $stokOut = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out']
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        $dataProduct = Product::leftJoin('product_detail', 'product.id', 'product_detail.id_product')
                                ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                    $join_in->on('product.id', '=', 'stokIn.id_item');
                                    $join_in->on('product_detail.id_satuan', '=', 'stokIn.id_satuan');

                                })
                                ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                    $join_out->on('product.id', '=', 'stokOut.id_item');
                                    $join_out->on('product_detail.id_satuan', '=', 'stokOut.id_satuan');
                                })
                                ->leftJoin('product_unit', 'product_detail.id_satuan', 'product_unit.id')
                                ->select(
                                    'product_unit.nama_satuan',
                                    DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                )
                                ->where([
                                    ['product.id', '=', $idProduct],
                                    ['product_detail.id_satuan', '=', $idSatuan],
                                ])
                                ->get();

        return response()->json($dataProduct);
    }

    public function StoreProductionDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProductionDelivery');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = ProductionDeliveryDetail::select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_delivery', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new ProductionDeliveryDetail();
                    $listItem->id_delivery = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_item = $qty;
                    $listItem->keterangan = $keterangan;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionDelivery Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan ProductionDelivery Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'production_delivery'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idItem],
                                    ['value3', '=', $idSatuan],
                                    ['deleted_at', '=', null]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'production_delivery';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->value5 = $keterangan;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionDelivery Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan ProductionDelivery Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }

        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function UpdateProductionDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProductionDelivery');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyItem');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = ProductionDeliveryDetail::find($idDetail);
                $listItem->id_delivery = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_item = $qty;
                $listItem->keterangan = $keterangan;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->value5 = $keterangan;
                $listItem->updated_by = $user;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();

            }


            $log = ActionLog::create([
                'module' => 'ProductionDelivery Detail',
                'action' => 'Update',
                'desc' => 'Update ProductionDelivery Detail',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetProductionDeliveryDetail(Request $request)
    {
        $id = $request->input('idProductionDelivery');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'production_delivery_detail.id',
                                            'production_delivery_detail.id_item',
                                            'production_delivery_detail.qty_item',
                                            'production_delivery_detail.id_satuan',
                                            'production_delivery_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['production_delivery_detail.id_delivery', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('production_delivery_detail.created_by', $user);
                                        })
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->select(
                                        'temp_transaction.id',
                                        'temp_transaction.value2',
                                        'temp_transaction.value3',
                                        'temp_transaction.value4',
                                        'temp_transaction.value5',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_unit.nama_satuan'
                                    )
                                    ->where([
                                        ['temp_transaction.value1', '=', $id],
                                        ['temp_transaction.module', '=', 'production_delivery']
                                    ])
                                    ->get();
        }

        return response()->json($detail);
    }

    public function EditProductionDeliveryDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        $stokIn = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in']
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        $stokOut = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out']
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        if ($mode == "") {

            $detail = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                            $join_in->on('production_delivery_detail.id_item', '=', 'stokIn.id_item');
                                            $join_in->on('production_delivery_detail.id_satuan', '=', 'stokIn.id_satuan');

                                        })
                                        ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                            $join_out->on('production_delivery_detail.id_item', '=', 'stokOut.id_item');
                                            $join_out->on('production_delivery_detail.id_satuan', '=', 'stokOut.id_satuan');
                                        })
                                        ->select(
                                            'production_delivery_detail.id',
                                            'production_delivery_detail.id_item',
                                            'production_delivery_detail.id_satuan',
                                            'production_delivery_detail.qty_item',
                                            'production_delivery_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                        )
                                        ->where([
                                            ['production_delivery_detail.id', '=', $id]
                                        ])
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                        $join_in->on('temp_transaction.value2', '=', 'stokIn.id_item');
                                        $join_in->on('temp_transaction.value3', '=', 'stokIn.id_satuan');

                                    })
                                    ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                        $join_out->on('temp_transaction.value2', '=', 'stokOut.id_item');
                                        $join_out->on('temp_transaction.value3', '=', 'stokOut.id_satuan');
                                    })
                                    ->select(
                                        'temp_transaction.id',
                                        'temp_transaction.value2',
                                        'temp_transaction.value3',
                                        'temp_transaction.value4',
                                        'temp_transaction.value5',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_unit.nama_satuan',
                                        DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                    )
                                    ->where([
                                        ['temp_transaction.id', '=', $id],
                                        ['temp_transaction.module', '=', 'production_delivery']
                                    ])
                                    ->get();
        }

        return response()->json($detail);
    }

    public function DeleteProductionDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            if ($mode != "") {
                $detail = TempTransaction::find($id);
                $detail->deleted_by = Auth::user()->user_name;
                $detail->action = "hapus";
                $detail->save();

                $detail->delete();
            }
            else {
                $delete = DB::table('production_delivery_detail')->where('id', '=', $id)->delete();
            }

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetProductionDeliveryFooter(Request $request)
    {
        $id = $request->input('idProductionDelivery');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {

            $detail = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                            ->select(
                                                DB::raw('COALESCE(SUM(production_delivery_detail.qty_item),0) AS qtyItem'),
                                            )
                                            ->where([
                                                ['production_delivery_detail.id_delivery', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('production_delivery_detail.created_by', $user);
                                            })
                                            ->groupBy('production_delivery_detail.id_delivery')
                                            ->first();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('COALESCE(SUM(temp_transaction.value4),0) AS qtyItem')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'production_delivery']
                                        ])
                                        ->groupBy('temp_transaction.value1')
                                        ->first();
        }

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function getListTerms(Request $request)
    {
        $target = $request->input('target');

        $listTemplate = TermsAndConditionTemplate::where([
                                            ['target_template', '=', $target]
                                        ])
                                        ->get();

        return response()->json($listTemplate);
    }

    public function getTerms(Request $request)
    {
        $id = $request->input('idTemplate');

        $template = TermsAndConditionTemplateDetail::where([
                                            ['id_template', '=', $id]
                                        ])
                                        ->get();

        return response()->json($template);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect('/ProductionDelivery')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tglSj = $request->input('tanggal_sj');
            $qtyTerima = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyTerima = str_replace(",", ".", $qtyTerima);

            $blnPeriode = date("m", strtotime($tglSj));
            $thnPeriode = date("Y", strtotime($tglSj));
            $tahunPeriode = date("y", strtotime($tglSj));

            $countKode = DB::table('production_delivery')
                            ->select(DB::raw("MAX(RIGHT(kode_pengiriman,2)) AS angka"))
                            // ->whereMonth('tanggal_sj', $blnPeriode)
                            // ->whereYear('tanggal_sj', $thnPeriode)
                            ->whereDate('tanggal_sj', $tglSj)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSj)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSj))));

            if ($counter < 10) {
                $nmrRcv = "sjp-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrRcv = "sjp-cv-".$kodeTgl.$counter;
            }

            $delivery = new ProductionDelivery();
            $delivery->kode_pengiriman = $nmrRcv;
            $delivery->id_supplier = $idSupplier;
            $delivery->id_alamat = $idAlamat;
            $delivery->jumlah_total_sj = $qtyTerima;
            $delivery->tanggal_sj = $tglSj;
            $delivery->status_pengiriman = 'draft';
            $delivery->created_by = $user;
            $delivery->save();

            $data = $delivery;

            $setDetail = DB::table('production_delivery_detail')
                            ->where([
                                        ['id_delivery', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_delivery' => $delivery->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_delivery' => $delivery->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                ProductionDeliveryTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'ProductionDelivery',
                'action' => 'Simpan',
                'desc' => 'Simpan ProductionDelivery',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('ProductionDelivery.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/ProductionDelivery')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect()->route('ProductionDelivery.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tglSj = $request->input('tanggal_sj');
            $qtyTerima = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyTerima = str_replace(",", ".", $qtyTerima);

            $delivery = ProductionDelivery::find($id);
            $delivery->id_supplier = $idSupplier;
            $delivery->id_alamat = $idAlamat;
            $delivery->jumlah_total_sj = $qtyTerima;
            $delivery->tanggal_sj = $tglSj;
            $delivery->updated_by = $user;
            $delivery->save();

            $data = $delivery;

            // $deletedDetail = ProductionDeliveryDetail::onlyTrashed()->where([['id_delivery', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'production_delivery'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = ProductionDeliveryDetail::find($detail->id_detail);
                        $listItem->id_delivery = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->keterangan = $detail->value5;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new ProductionDeliveryDetail();
                        $listItem->id_delivery = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->keterangan = $detail->value5;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('production_delivery_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_delivery'],
                                    ['value1', '=', $id]
                                ])->delete();

            $delete = DB::table('production_delivery_terms')->where('id_delivery', '=', $id)->delete();
            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_delivery' => $id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                ProductionDeliveryTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'ProductionDelivery',
                'action' => 'Update',
                'desc' => 'Update ProductionDelivery',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ProductionDelivery.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_pengiriman).' Telah Diubah!');
        }
        else {
            return redirect('/ProductionDelivery')->with('error', $exception);
        }
    }

    public function postAllocation(Request $request, $id)
    {
        $data = new stdClass();
        $btnAction = $request->input('submit_action');
        if ($btnAction == "ubah") {
            return redirect()->route('ProductionDelivery.edit', [$id]);
        }
        $dlv = ProductionDelivery::find($id);
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $delivery = ProductionDelivery::find($id);
            $arrayDetail = $request->input('isi');
            $listAlokasi = [];
            $listDetail = [];
            $ttlAlokasi = 0;
            if ($arrayDetail != "") {
                $delete = DB::table('production_delivery_allocation')
                            ->where([
                                ['production_delivery_allocation.id_delivery', '=', $id]
                            ])
                            ->delete();
                foreach ($arrayDetail as $detilAlokasi) {
                    $dataAlokasi = [
                        'id_delivery' => $id,
                        'id_detail' => $detilAlokasi["idDetail"],
                        'id_item' => $detilAlokasi["idItem"],
                        'id_satuan' => $detilAlokasi["idSatuan"],
                        'qty_item' => $detilAlokasi["qty"],
                        'id_index' => $detilAlokasi["idIndex"],
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name
                    ];
                    array_push($listAlokasi, $dataAlokasi);

                    $ttlAlokasi = $ttlAlokasi + $detilAlokasi["qty"];
                }

                ProductionDeliveryAllocation::insert($listAlokasi);
            }

            $log = ActionLog::create([
                'module' => 'ProductionDelivery',
                'action' => 'Alokasi',
                'desc' => 'Alokasi ProductionDelivery',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ProductionDelivery.Detail', [$dlv->id])->with('success', 'Data Alokasi '.strtoupper($dlv->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/ProductionDelivery')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $delivery = ProductionDelivery::find($id);

            if ($btnAction == "posting") {
                $detailDlv = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'production_delivery_detail.id',
                                                'production_delivery_detail.id_item',
                                                'production_delivery_detail.qty_item',
                                                'production_delivery_detail.id_satuan',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_delivery_detail.id_delivery', '=', $id]
                                            ])
                                            ->get();
                $transaksi = [];
                $failedItem = [];

                $alokasiDlv = ProductionDeliveryAllocation::where([
                                                        ['production_delivery_allocation.id_delivery', '=', $id]
                                                    ])
                                                    ->get();
                $listAlokasi = [];
                $listDetail = [];
                $ttlAlokasi = 0;
                if ($alokasiDlv != "") {
                    foreach ($alokasiDlv as $detilAlokasi) {

                        $dataStok = [
                            'kode_transaksi' => $delivery->kode_pengiriman,
                            'id_item' => $detilAlokasi->id_item,
                            'id_satuan' => $detilAlokasi->id_satuan,
                            'qty_item' => $detilAlokasi->qty_item,
                            'id_index' => $detilAlokasi->id_index,
                            'tgl_transaksi' => $delivery->tanggal_sj,
                            'jenis_transaksi' => "pengiriman_produksi",
                            'transaksi' => "out",
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name,
                        ];
                        array_push($listDetail, $dataStok);

                        $ttlAlokasi = $ttlAlokasi + $detilAlokasi["qty"];
                    }
                }

                if (count($failedItem) > 0) {
                    $msg = 'Pengiriman Produksi '.strtoupper($delivery->kode_pengiriman).' Gagal Diposting! Item ('.strtoupper(implode(', ', $failedItem)).')';
                    $status = 'warning';
                }
                elseif (count($alokasiDlv) < 1) {
                    $msg = 'Pengiriman Produksi '.strtoupper($delivery->kode_pengiriman).' Gagal Diposting! Lakukan Alokasi Pengiriman Terlebih dahulu!';
                    $status = 'warning';
                }
                else {

                    StockTransaction::insert($listDetail);

                    $delivery->status_pengiriman = "posted";
                    $delivery->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionDelivery',
                        'action' => 'Posting',
                        'desc' => 'Posting ProductionDelivery',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Pengiriman Barang Produksi '.strtoupper($delivery->kode_pengiriman).' Telah Diposting!';
                    $status = 'success';
                }
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "revisi") {
                $delivery->status_pengiriman = "draft";
                $delivery->flag_revisi = '1';
                $delivery->updated_by = Auth::user()->user_name;
                $delivery->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $delivery->kode_pengiriman)->delete();

                $detailDlv = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'production_delivery_detail.id',
                                                'production_delivery_detail.id_item',
                                                'production_delivery_detail.id_satuan',
                                                'production_delivery_detail.qty_item',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_delivery_detail.id_delivery', '=', $id],
                                            ])
                                            ->get();

                $log = ActionLog::create([
                    'module' => 'Pengiriman Barang',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Pengiriman Barang',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $delivery->status_pengiriman = "batal";
                $delivery->updated_by = Auth::user()->user_name;
                $delivery->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $delivery->kode_pengiriman)->delete();

                $detailDlv = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'production_delivery_detail.id',
                                                'production_delivery_detail.id_item',
                                                'production_delivery_detail.id_satuan',
                                                'production_delivery_detail.qty_item',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_delivery_detail.id_delivery', '=', $id]
                                            ])
                                            ->get();


                $log = ActionLog::create([
                    'module' => 'Pengiriman',
                    'action' => 'Batal',
                    'desc' => 'Batal Pengiriman Barang',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Pengiriman '.strtoupper($delivery->kode_pengiriman).' Telah Dibatalkan!';
                $status = 'success';
            }
            else {
                $status = "ubahStaging";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('ProductionDelivery.edit', [$id]);
            }
            elseif ($status == "ubahStaging") {
                return redirect()->route('ProductionDelivery.Staging', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetProductionDeliveryDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDlv');


            if ($id != "DRAFT") {
                // $detail = ProductionDeliveryDetail::where([
                //                             ['id_delivery', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'production_delivery'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('production_delivery_detail')->where('id_delivery', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function cetak($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionDelivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataDlv = ProductionDelivery::leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_detail', 'production_delivery.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_delivery.id',
                                        'production_delivery.kode_pengiriman',
                                        'production_delivery.id_supplier',
                                        'production_delivery.id_alamat',
                                        'production_delivery.tanggal_sj',
                                        'production_delivery.status_pengiriman',
                                        'production_delivery.flag_revisi',
                                        'production_delivery.id_supplier',
                                        'supplier.nama_supplier',
                                        'supplier.telp_supplier',
                                        'supplier.fax_supplier',
                                        'supplier.email_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_delivery.id', '=', $id],
                                    ])
                                    ->first();
                $dataTerms = ProductionDeliveryTerms::where('id_delivery', $id)->get();

                $dataAlamat = SupplierDetail::find($dataDlv->id_alamat);

                $dataPreference = Preference::leftJoin('company_account', 'preference.rekening', '=', 'company_account.id')
                                            ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                            ->select(
                                                'bank.kode_bank',
                                                'bank.nama_bank',
                                                'company_account.nomor_rekening',
                                                'company_account.cabang',
                                                'company_account.atas_nama',
                                                'preference.*'
                                            )
                                            ->where('flag_do', 'Y')
                                            ->first();
                $detailProductionDelivery = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'production_delivery_detail.id',
                                                                'production_delivery_detail.id_item',
                                                                'production_delivery_detail.qty_item',
                                                                'production_delivery_detail.keterangan',
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan',
                                                                'product.keterangan_item'
                                                            )
                                                            ->where([
                                                                ['production_delivery_detail.id_delivery', '=', $id]
                                                            ])
                                                            ->get();


                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailProductionDelivery'] = $detailProductionDelivery;

                $log = ActionLog::create([
                    'module' => 'Pengiriman',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Pengiriman',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperProductionDelivery::cetakPdfDlv($data);

                $fpdf->Output('I', strtoupper($dataDlv->kode_pengiriman).".pdf");
                exit;
            }
            else {
                return redirect('/ProductionDelivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getProductDetail(Request $request)
    {
        $idProduct = $request->input('idProduct');

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.id',
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                )
                                ->where([
                                    ['product_detail.id_product', '=', $idProduct]
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function StoreProductionDeliveryAllocation(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $idItem = $request->input('idItem');
            $qty = $request->input('qtyItem');
            $idIndex = $request->input('index');
            $user = Auth::user()->user_name;

            $countItem = DB::table('temp_transaction')
                            ->select(DB::raw("COUNT(*) AS angka"))
                            ->where([
                                ['module', '=', 'production_delivery_allocation'],
                                ['value1', '=' , $id],
                                ['value2', '=', $idItem],
                                ['value3', '=', $idIndex],
                                ['deleted_at', '=', null]
                            ])
                            ->first();
            $count = $countItem->angka;

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listItem = new TempTransaction();
                $listItem->module = 'production_delivery_allocation';
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idIndex;
                $listItem->value4 = $qty;
                $listItem->action = 'tambah';
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'ProductionDelivery Allocation',
                    'action' => 'Simpan',
                    'desc' => 'Simpan ProductionDelivery Allocation',
                    'username' => Auth::user()->user_name
                ]);

                $data = "success";
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function getDataDetail(Request $request)
    {
        $idItem = $request->input('id_product');
        $idDetail = $request->input('id_detail');
        $status = $request->input('status');

        if ($status == "draft") {
            $dataAlokasi = TempTransaction::select(
                                                DB::raw('temp_transaction.value1 as id_detail'),
                                                DB::raw('temp_transaction.value2 as id_item'),
                                                DB::raw('SUM(temp_transaction.value4) as sumAllocation')
                                            )
                                            ->where([
                                                ['temp_transaction.value1', '=', $idDetail],
                                                ['temp_transaction.value2', '=', $idItem],
                                                ['temp_transaction.module', '=', 'production_delivery_allocation']
                                            ])
                                            ->groupBy('temp_transaction.value1')
                                            ->groupBy('temp_transaction.value2');
        }
        else {
            $dataAlokasi = ProductionDeliveryAllocation::select(
                                                    'production_delivery_allocation.id_detail',
                                                    'production_delivery_allocation.id_item',
                                                    'production_delivery_allocation.qty_item',
                                                    DB::raw('SUM(production_delivery_allocation.qty_item) as sumAllocation')
                                                )
                                                ->where([
                                                    ['production_delivery_allocation.id_detail', '=', $idDetail],
                                                    ['production_delivery_allocation.id_item', '=', $idItem]
                                                ])
                                                ->groupBy('production_delivery_allocation.id_detail')
                                                ->groupBy('production_delivery_allocation.id_item');
        }

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);


        $dataDetail = ProductionDeliveryDetail::leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                    ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoinSub($dataAlokasi, 'dataAlokasi', function($dataAlokasi) {
                                        $dataAlokasi->on('production_delivery_detail.id_item', '=', 'dataAlokasi.id_item');
                                        $dataAlokasi->on('production_delivery_detail.id', '=', 'dataAlokasi.id_detail');
                                    })
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'production_delivery_detail.id',
                                        'production_delivery_detail.qty_item',
                                        'production_delivery_detail.id_item',
                                        'production_delivery_detail.keterangan',
                                        'product_category.nama_kategori',
                                        'product_unit.nama_satuan',
                                        'product.nama_item',
                                        DB::raw('COALESCE(dataAlokasi.sumAllocation, 0) as sumAllocation'),
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['production_delivery_detail.id', '=', $idDetail],
                                        ['production_delivery_detail.id_item', '=', $idItem]
                                    ])
                                    ->first();

        return response()->json($dataDetail);
    }

    public function GetProductionDeliveryAllocation(Request $request)
    {
        $id = $request->input('idDetail');
        $idItem = $request->input('idItem');
        $status = $request->input('status');

        if ($status != "draft") {

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $detail = ProductionDeliveryAllocation::leftJoin('product', 'production_delivery_allocation.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_delivery_allocation.id_satuan', 'product_unit.id')
                                        ->leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'production_delivery_allocation.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_delivery_allocation.id',
                                            'production_delivery_allocation.id_detail',
                                            'production_delivery_allocation.id_item',
                                            'production_delivery_allocation.id_satuan',
                                            'production_delivery_allocation.qty_item',
                                            'production_delivery_allocation.id_index',
                                            DB::raw('production_delivery_allocation.id_index as id_index'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_delivery_allocation.id_detail', '=', $id],
                                            ['production_delivery_allocation.id_item', '=', $idItem]
                                        ])
                                        ->get();

        }
        else {
            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'temp_transaction.value2')
                                        ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'temp_transaction.id',
                                            DB::raw('temp_transaction.value1 as id_detail'),
                                            DB::raw('temp_transaction.value2 as id_item'),
                                            DB::raw('temp_transaction.value3 as id_index'),
                                            DB::raw('temp_transaction.value4 as qty_item'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.value2', '=', $idItem],
                                            ['temp_transaction.module', '=', 'production_delivery_allocation']
                                        ])
                                        ->get();
        }

        if (count($detail) > 0) {
            $detailAllocation = [];
            foreach($detail as $dataDetail) {
                $dataIndex = StockIndex::defaultOrder()->ancestorsAndSelf($dataDetail->id_index);

                $i = 0;
                $x = $dataIndex->keys()->last();
                $txt = "";
                foreach ($dataIndex as $index) {
                    $txt = $txt.$index->nama_index;
                    if ($i < $x) {
                        $txt = $txt.".";
                    }
                    $i = $i + 1;
                }


                $dataTemps = [
                    'id' => $dataDetail->id,
                    'id_detail' => $dataDetail->id_detail,
                    'id_item' => $dataDetail->id_item,
                    'id_index' => $dataDetail->id_index,
                    'qty_item' => $dataDetail->qty_item,
                    'txt' => $txt,
                ];
                array_push($detailAllocation, $dataTemps);
            }
        }
        else {
            $detailAllocation = null;
        }

        return response()->json($detailAllocation);
    }

    public function DeleteProductionDeliveryAllocation(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');

            $detail = TempTransaction::find($id);
            $detail->deleted_by = Auth::user()->user_name;
            $detail->action = "hapus";
            $detail->save();

            $detail->delete();

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function exportDataProductionDelivery(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new ProductionDeliveryExport($request), 'RekapPengirimanProduksi_'.$kodeTgl.'.xlsx');
    }
}
