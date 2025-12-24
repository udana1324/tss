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
use App\Models\Production\ProductionOrder;
use App\Models\Production\ProductionOrderDetail;
use App\Models\Production\ProductionReceiving;
use App\Models\Production\ProductionReceivingDetail;
use App\Models\Production\ProductionReceivingTerms;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperProductionReceiving;
use App\Exports\ProductionReceivingExport;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Production\ProductionOrderTerms;
use App\Models\Production\ProductionReceivingAllocation;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ProductionReceivingController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductionReceiving'],
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
                                                ['module.url', '=', '/ProductionReceiving'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = ProductionReceiving::distinct()->get('status_penerimaan');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('production_receiving_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;


                $log = ActionLog::create([
                    'module' => 'ProductionReceiving',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan ProductionReceiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_receiving.index', $data);
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

        $receiving = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', 'production_order.id')
                            ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'production_order.no_production_order',
                                'production_receiving.id',
                                'production_receiving.kode_penerimaan',
                                'production_receiving.no_sj_supplier',
                                'production_receiving.jumlah_total_sj',
                                'production_receiving.tanggal_sj',
                                'production_receiving.tanggal_terima',
                                'production_receiving.flag_revisi',
                                'production_receiving.flag_invoiced',
                                'production_receiving.status_penerimaan')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('production_receiving.tanggal_sj', Carbon::parse($periode)->format('m'));
                                $q->whereYear('production_receiving.tanggal_sj', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('production_receiving.id', 'desc')
                            ->get();
        return response()->json($receiving);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionReceiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('production_order', 'production_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->where([
                                            ['production_order.status', '=', 'posted'],
                                            ['production_order.deleted_at', '=', null]
                                        ])
                                        ->orderBy('production_order.id', 'desc')
                                        ->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'ProductionReceiving',
                    'action' => 'Buat',
                    'desc' => 'Buat ProductionReceiving',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('production_receiving_detail')
                            ->where([
                                ['id_penerimaan', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.production.production_receiving.add', $data);
            }
            else {
                return redirect('/ProductionReceiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionReceiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('production_order', 'production_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->where([
                                            ['production_order.status', '=', 'posted']
                                        ])
                                        ->orderBy('production_order.id', 'desc')
                                        ->get();

                $dataRcv = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                                    ->leftJoin('supplier_detail', 'production_receiving.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_receiving.id',
                                        'production_receiving.kode_penerimaan',
                                        'production_receiving.id_po',
                                        'production_receiving.id_alamat',
                                        'production_receiving.no_sj_supplier',
                                        'production_receiving.tanggal_sj',
                                        'production_receiving.tanggal_terima',
                                        'production_receiving.status_penerimaan',
                                        'production_order.id_supplier',
                                        'production_order.no_production_order',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->status_penerimaan != "draft") {
                    return redirect('/ProductionReceiving')->with('warning', 'Penerimaan tidak dapat diubah karena status Penerimaan bukan DRAFT!');
                }

                // $draftPo = ProductionOrder::find($dataRcv->id_po);

                // if ($draftPo->status == "draft") {
                //     return redirect('/ProductionReceiving')->with('warning', 'Tidak dapat mengubah Penerimaan, PO '.strtoupper($draftPo->no_production_order).' Berstatus Draft');
                // }

                // $restore = ProductionReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_receiving'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = ProductionReceivingDetail::where([
                                                    ['id_penerimaan', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'production_receiving',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_penerimaan,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_item
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $dataTerms = ProductionReceivingTerms::where('id_receiving', $id)->get();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'ProductionReceiving',
                    'action' => 'Ubah',
                    'desc' => 'Ubah ProductionReceiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_receiving.edit', $data);
            }
            else {
                return redirect('/ProductionReceiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionReceiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRcv = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                                    ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('preference', 'production_receiving.id_alamat', '=', 'preference.id')
                                    ->select(
                                        'production_receiving.id',
                                        'production_receiving.kode_penerimaan',
                                        'production_receiving.id_po',
                                        'production_receiving.id_alamat',
                                        'production_receiving.tanggal_sj',
                                        'production_receiving.tanggal_terima',
                                        'production_receiving.status_penerimaan',
                                        'production_receiving.flag_terms_po',
                                        'production_receiving.flag_revisi',
                                        'production_receiving.no_sj_supplier',
                                        'production_receiving.jumlah_total_sj',
                                        'production_order.id_supplier',
                                        'production_order.no_production_order',
                                        'supplier.nama_supplier',
                                        'preference.alamat_pt',
                                    )
                                    ->where([
                                        ['production_receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ProductionReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = ProductionOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailRcv = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'production_receiving_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_receiving_detail.id',
                                            'production_receiving_detail.id_item',
                                            'production_receiving_detail.id_satuan',
                                            'production_receiving_detail.qty_item',
                                            'production_receiving_detail.id_penerimaan',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_receiving_detail.id_penerimaan', '=', $id],
                                            ['production_order_detail.id_po', '=', $dataRcv->id_po]
                                        ])
                                        ->get();

                $alokasiRcv = ProductionReceivingAllocation::leftJoin('product_unit', 'production_receiving_allocation.id_satuan', 'product_unit.id')
                ->where([
                    ['production_receiving_allocation.id_receiving', '=', $id]
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

                $dataAlokasiRcv = [];
                foreach($alokasiRcv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_receiving' => $dataAlokasi->id_receiving,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'id_satuan' => $dataAlokasi->id_satuan,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiRcv, $dataAlloc);
                }

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;
                $data['listIndex'] = $list;
                $data['detailRcv'] = $detailRcv;
                $data['dataAlokasiRcv'] = $dataAlokasiRcv;

                $log = ActionLog::create([
                    'module' => 'ProductionReceiving',
                    'action' => 'Staging',
                    'desc' => 'Staging ProductionReceiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_receiving.staging', $data);
            }
            else {
                return redirect('/ProductionReceiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionReceiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRcv = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                                    ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('preference', 'production_receiving.id_alamat', '=', 'preference.id')
                                    ->select(
                                        'production_receiving.id',
                                        'production_receiving.kode_penerimaan',
                                        'production_receiving.id_po',
                                        'production_receiving.id_alamat',
                                        'production_receiving.tanggal_sj',
                                        'production_receiving.tanggal_terima',
                                        'production_receiving.status_penerimaan',
                                        'production_receiving.flag_terms_po',
                                        'production_receiving.flag_revisi',
                                        'production_receiving.no_sj_supplier',
                                        'production_receiving.jumlah_total_sj',
                                        'production_order.id_supplier',
                                        'production_order.no_production_order',
                                        'supplier.nama_supplier',
                                        'preference.alamat_pt',
                                    )
                                    ->where([
                                        ['production_receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ProductionReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = ProductionOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailRcv = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'production_receiving_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_receiving_detail.id',
                                            'production_receiving_detail.id_item',
                                            'production_receiving_detail.qty_item',
                                            'production_receiving_detail.id_penerimaan',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_receiving_detail.id_penerimaan', '=', $id],
                                            ['production_order_detail.id_po', '=', $dataRcv->id_po]
                                        ])
                                        ->get();

                $alokasiRcv = ProductionReceivingAllocation::leftJoin('product', 'production_receiving_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'production_receiving_allocation.id_satuan', 'product_unit.id')
                                                ->where([
                                                    ['production_receiving_allocation.id_receiving', '=', $id]
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

                $dataAlokasiRcv = [];
                foreach($alokasiRcv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_receiving' => $dataAlokasi->id_receiving,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'id_satuan' => $dataAlokasi->id_satuan,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiRcv, $dataAlloc);
                }

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;
                $data['listIndex'] = $list;
                $data['detailRcv'] = $detailRcv;
                $data['dataAlokasiRcv'] = $dataAlokasiRcv;

                $log = ActionLog::create([
                    'module' => 'ProductionReceiving',
                    'action' => 'Detail',
                    'desc' => 'Detail ProductionReceiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_receiving.detail', $data);
            }
            else {
                return redirect('/ProductionReceiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function RestoreProductionReceivingDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idRcv');
            $restore = ProductionReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {

        }
        else {
            return response()->json($exception);
        }

    }

    public function getProductionOrder(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataPo = ProductionOrder::where([
                                            ['id_supplier', '=', $idSupplier],
                                            ['status', '=', 'posted']
                                        ])
                                        ->orderBy('id', 'asc')
                                        ->get();

        return response()->json($dataPo);
    }

    public function getProduct(Request $request)
    {
        $idProductionOrder = $request->input('idProductionOrder');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = ProductionOrderDetail::leftJoin('product', 'production_order_detail.id_item', 'product.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_order_detail.id_po', '=', $idProductionOrder],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->orderBy('product.nama_item', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDefaultAddress(Request $request)
    {
        $idProductionOrder = $request->input('idProductionOrder');

        $idAlamat = ProductionOrder::find($idProductionOrder);

        $defaultAddress = Preference::where([
                                            ['id', '=', $idAlamat->id_alamat]
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getTanggalPo(Request $request)
    {
        $idProductionOrder = $request->input('idProductionOrder');

        $dataPo = ProductionOrder::find($idProductionOrder);

        return response()->json($dataPo);
    }

    public function getSupplierAddress(Request $request)
    {
        $supplierAddress = Preference::get();

        return response()->json($supplierAddress);
    }

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idPo = $request->input('idProductionOrder');
        $idSatuan = $request->input('id_satuan');

        $dataProduct = Product::leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'product_unit.nama_satuan',
                                            'production_order_detail.outstanding_qty',
                                        )
                                        ->where([
                                            ['product.id', '=', $idProduct],
                                            ['production_order_detail.id_po', '=', $idPo],
                                            ['production_order_detail.id_satuan', '=', $idSatuan],
                                        ])
                                        ->get();

        return response()->json($dataProduct);
    }

    public function StoreProductionReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProductionReceiving');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = ProductionReceivingDetail::select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_penerimaan', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new ProductionReceivingDetail();
                    $listItem->id_penerimaan = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionReceiving Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan ProductionReceiving Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'production_receiving'],
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
                    $listItem->module = 'production_receiving';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionReceiving Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan ProductionReceiving Detail',
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

    public function UpdateProductionReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProductionReceiving');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = ProductionReceivingDetail::find($idDetail);
                $listItem->id_penerimaan = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_item = $qty;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->updated_by = $user;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();

            }


            $log = ActionLog::create([
                'module' => 'ProductionReceiving Detail',
                'action' => 'Update',
                'desc' => 'Update ProductionReceiving Detail',
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

    public function SetProductionReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProductionReceiving');
            $idPo = $request->input('idProductionOrder');
            $user = Auth::user()->user_name;
            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != "DRAFT") {
                if ($id != "DRAFT") {
                    $update = DB::table('temp_transaction')
                                ->where([
                                    ['value1', '=', $id],
                                    ['module', '=', "production_receiving"]
                                ])
                                ->update([
                                    'action' => "hapus",
                                    'deleted_by' => Auth::user()->user_name,
                                    'deleted_at' => now()
                                ]);

                    $detail = ProductionOrderDetail::select(
                                                'production_order_detail.id_item',
                                                'production_order_detail.id_satuan',
                                                'production_order_detail.outstanding_qty'
                                            )
                                            ->where([
                                                ['production_order_detail.id_po', '=', $idPo]
                                            ])
                                            ->get();
                    $data = $detail;
                    $listDetail = [];
                    foreach ($detail As $detailDlv) {
                        $dataDetail = [
                            'module' => "production_receiving",
                            'value1' => $id,
                            'value2' => $detailDlv->id_item,
                            'value3' => $detailDlv->id_satuan,
                            'value4' => $detailDlv->outstanding_qty,
                            'action' => "tambah",
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name,
                        ];
                        array_push($listDetail, $dataDetail);
                    }
                    TempTransaction::insert($listDetail);
                }
            }
            else {
                $delete = DB::table('production_receiving_detail')
                            ->where('id_penerimaan', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('production_receiving_detail.created_by', $user);
                            })
                            ->delete();

                $detail = ProductionOrderDetail::select(
                                                'production_order_detail.id_item',
                                                'production_order_detail.id_satuan',
                                                'production_order_detail.outstanding_qty'
                                            )
                                            ->where([
                                                ['production_order_detail.id_po', '=', $idPo],
                                                ['production_order_detail.outstanding_qty', '>', 0]
                                            ])
                                            ->get();

                $listDetail = [];
                foreach ($detail As $detailRcv) {

                    $dataDetail = [
                        'id_penerimaan' => $id,
                        'id_item' => $detailRcv->id_item,
                        'id_satuan' => $detailRcv->id_satuan,
                        'qty_item' => $detailRcv->outstanding_qty,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                ProductionReceivingDetail::insert($listDetail);
            }
        });

        $log = ActionLog::create([
            'module' => 'ProductionReceiving',
            'action' => 'Set Detail',
            'desc' => 'Set ProductionReceiving Detail',
            'username' => Auth::user()->user_name
        ]);

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetProductionReceivingDetail(Request $request)
    {
        $id = $request->input('idProductionReceiving');
        $idPo = $request->input('idProductionOrder');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('production_order_detail', function($join) {
                                            $join->on('production_order_detail.id_item', '=', 'production_receiving_detail.id_item');
                                            $join->on('production_order_detail.id_satuan', '=', 'production_receiving_detail.id_satuan');
                                        })
                                        ->select(
                                            'production_receiving_detail.id',
                                            'production_receiving_detail.id_item',
                                            'production_receiving_detail.qty_item',
                                            'production_receiving_detail.id_satuan',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['production_receiving_detail.id_penerimaan', '=', $id],
                                            ['production_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('production_receiving_detail.created_by', $user);
                                        })
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('production_order_detail', function($join) {
                                        $join->on('production_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('production_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'production_receiving'],
                                            ['production_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditProductionReceivingDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $idPo = $request->input('idProductionOrder');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = ProductionReceivingDetail::leftJoin('production_order_detail', 'production_receiving_detail.id_item', '=', 'production_order_detail.id_item')
                                        ->leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'production_receiving_detail.id',
                                            'production_receiving_detail.id_item',
                                            'production_receiving_detail.id_satuan',
                                            'production_receiving_detail.qty_item',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['production_receiving_detail.id', '=', $id],
                                            ['production_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('production_order_detail', function($join) {
                                        $join->on('production_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('production_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'production_order_detail.qty_order',
                                            'production_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'production_receiving'],
                                            ['production_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteProductionReceivingDetail(Request $request)
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
                $delete = DB::table('production_receiving_detail')->where('id', '=', $id)->delete();
            }

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetProductionReceivingFooter(Request $request)
    {
        $id = $request->input('idProductionReceiving');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {

            $detail = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                            ->select(
                                                DB::raw('COALESCE(SUM(production_receiving_detail.qty_item),0) AS qtyItem'),
                                            )
                                            ->where([
                                                ['production_receiving_detail.id_penerimaan', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('production_receiving_detail.created_by', $user);
                                            })
                                            ->groupBy('production_receiving_detail.id_penerimaan')
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
                                            ['temp_transaction.module', '=', 'production_receiving']
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
            'production_order'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect('/ProductionReceiving')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idProductionOrder = $request->input('production_order');
            $tglSj = $request->input('tanggal_sj');
            $tglTerima = $request->input('tanggal_terima');
            $noSjSupplier = $request->input('no_sj_supplier');
            $qtyTerima = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idProductionOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyTerima = str_replace(",", ".", $qtyTerima);

            $blnPeriode = date("m", strtotime($tglSj));
            $thnPeriode = date("Y", strtotime($tglSj));
            $tahunPeriode = date("y", strtotime($tglSj));

            $countKode = DB::table('production_receiving')
                            ->select(DB::raw("MAX(RIGHT(kode_penerimaan,2)) AS angka"))
                            // ->whereMonth('tanggal_sj', $blnPeriode)
                            // ->whereYear('tanggal_sj', $thnPeriode)
                            ->whereDate('tanggal_sj', $tglSj)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSj)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSj))));

            if ($counter < 10) {
                $nmrRcv = "pbp-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrRcv = "pbp-cv-".$kodeTgl.$counter;
            }

            $receiving = new ProductionReceiving();
            $receiving->kode_penerimaan = $nmrRcv;
            $receiving->id_po = $idProductionOrder;
            $receiving->id_alamat = $idAlamat;
            $receiving->jumlah_total_sj = $qtyTerima;
            $receiving->tanggal_sj = $tglSj;
            $receiving->no_sj_supplier = $noSjSupplier;
            $receiving->tanggal_terima = $tglTerima;
            $receiving->status_penerimaan = 'draft';
            $receiving->flag_terms_po = $flagTermsUsage;
            $receiving->flag_invoiced = '0';
            $receiving->created_by = $user;
            $receiving->save();

            $data = $receiving;

            $setDetail = DB::table('production_receiving_detail')
                            ->where([
                                        ['id_penerimaan', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_penerimaan' => $receiving->id,
                                'updated_by' => $user
                            ]);

            if ($flagTerms != "termsPo") {
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_receiving' => $receiving->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    ProductionReceivingTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'ProductionReceiving',
                'action' => 'Simpan',
                'desc' => 'Simpan ProductionReceiving',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('ProductionReceiving.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_penerimaan).' Telah Disimpan!');
        }
        else {
            return redirect('/ProductionReceiving')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'production_order'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect()->route('ProductionReceiving.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idProductionOrder = $request->input('production_order');
            $tglSj = $request->input('tanggal_sj');
            $tglTerima = $request->input('tanggal_terima');
            $noSjSupplier = $request->input('no_sj_supplier');
            $qtyTerima = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idProductionOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyTerima = str_replace(",", ".", $qtyTerima);

            $receiving = ProductionReceiving::find($id);
            $receiving->id_po = $idProductionOrder;
            $receiving->id_alamat = $idAlamat;
            $receiving->jumlah_total_sj = $qtyTerima;
            $receiving->no_sj_supplier = $noSjSupplier;
            $receiving->tanggal_sj = $tglSj;
            $receiving->tanggal_terima = $tglTerima;
            $receiving->flag_terms_po = $flagTermsUsage;
            $receiving->updated_by = $user;
            $receiving->save();

            $data = $receiving;

            // $deletedDetail = ProductionReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'production_receiving'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = ProductionReceivingDetail::find($detail->id_detail);
                        $listItem->id_penerimaan = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new ProductionReceivingDetail();
                        $listItem->id_penerimaan = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('production_receiving_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_receiving'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($flagTerms != "termsPo") {
                $delete = DB::table('production_receiving_terms')->where('id_receiving', '=', $id)->delete();
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_receiving' => $id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    ProductionReceivingTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'ProductionReceiving',
                'action' => 'Update',
                'desc' => 'Update ProductionReceiving',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ProductionReceiving.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_penerimaan).' Telah Diubah!');
        }
        else {
            return redirect('/ProductionReceiving')->with('error', $exception);
        }
    }

    public function postAllocation(Request $request, $id)
    {
        $data = new stdClass();
        $btnAction = $request->input('submit_action');
        if ($btnAction == "ubah") {
            return redirect()->route('ProductionReceiving.edit', [$id]);
        }
        $dlv = ProductionReceiving::find($id);
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $receiving = ProductionReceiving::find($id);
            $arrayDetail = $request->input('isi');
            $listAlokasi = [];
            $listDetail = [];
            $ttlAlokasi = 0;
            if ($arrayDetail != "") {
                $delete = DB::table('production_receiving_allocation')
                            ->where([
                                ['production_receiving_allocation.id_receiving', '=', $id]
                            ])
                            ->delete();
                foreach ($arrayDetail as $detilAlokasi) {
                    $dataAlokasi = [
                        'id_receiving' => $id,
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

                ProductionReceivingAllocation::insert($listAlokasi);
            }

            $log = ActionLog::create([
                'module' => 'ProductionReceiving',
                'action' => 'Alokasi',
                'desc' => 'Alokasi ProductionReceiving',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ProductionReceiving.Detail', [$dlv->id])->with('success', 'Data Alokasi '.strtoupper($dlv->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/ProductionReceiving')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $receiving = ProductionReceiving::find($id);

            if ($btnAction == "posting") {
                $detailRcv = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('production_order_detail', function($join) {
                                                $join->on('production_order_detail.id_item', '=', 'production_receiving_detail.id_item');
                                                $join->on('production_order_detail.id_satuan', '=', 'production_receiving_detail.id_satuan');
                                            })
                                            ->select(
                                                'production_receiving_detail.id',
                                                'production_receiving_detail.id_item',
                                                'production_receiving_detail.qty_item',
                                                'production_receiving_detail.id_satuan',
                                                'production_order_detail.qty_order',
                                                'production_order_detail.outstanding_qty',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_receiving_detail.id_penerimaan', '=', $id],
                                                ['production_order_detail.id_po', '=', $receiving->id_po]
                                            ])
                                            ->get();
                $transaksi = [];
                $failedItem = [];
                foreach ($detailRcv As $detail) {
                    $detailOuts = ProductionOrderDetail::where([
                                                        ['id_po', '=', $receiving->id_po],
                                                        ['id_item', '=', $detail->id_item],
                                                        ['id_satuan', '=', $detail->id_satuan]
                                                    ])
                                                    ->first();

                    $cekOuts = $detailOuts->outstanding_qty - $detail->qty_item;

                    if ($cekOuts < 0) {
                        $dataProduct = Product::find($detail->id_item);
                        array_push($failedItem, $dataProduct->nama_item);
                    }
                }

                $alokasiRcv = ProductionReceivingAllocation::where([
                                                        ['production_receiving_allocation.id_receiving', '=', $id]
                                                    ])
                                                    ->get();
                $listAlokasi = [];
                $listDetail = [];
                $ttlAlokasi = 0;
                if ($alokasiRcv != "") {
                    foreach ($alokasiRcv as $detilAlokasi) {

                        $dataStok = [
                            'kode_transaksi' => $receiving->kode_penerimaan,
                            'id_item' => $detilAlokasi->id_item,
                            'id_satuan' => $detilAlokasi->id_satuan,
                            'qty_item' => $detilAlokasi->qty_item,
                            'id_index' => $detilAlokasi->id_index,
                            'tgl_transaksi' => $receiving->tanggal_sj,
                            'jenis_transaksi' => "penerimaan_produksi",
                            'transaksi' => "in",
                            'jenis_sumber' => 2,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name,
                        ];
                        array_push($listDetail, $dataStok);

                        $ttlAlokasi = $ttlAlokasi + $detilAlokasi["qty"];
                    }
                }

                if (count($failedItem) > 0) {
                    $msg = 'Penerimaan Produksi '.strtoupper($receiving->kode_penerimaan).' Gagal Diposting! Item ('.strtoupper(implode(', ', $failedItem)).')';
                    $status = 'warning';
                }
                elseif (count($alokasiRcv) < 1) {
                    $msg = 'Pengiriman Produksi '.strtoupper($receiving->kode_penerimaan).' Gagal Diposting! Lakukan Alokasi Penerimaan Terlebih dahulu!';
                    $status = 'warning';
                }
                else {
                    foreach ($detailRcv As $detail) {
                        $detailOuts = ProductionOrderDetail::where([
                                                            ['id_po', '=', $receiving->id_po],
                                                            ['id_item', '=', $detail->id_item]
                                                        ])
                                                        ->first();

                        $cekOuts = $detailOuts->outstanding_qty - $detail->qty_item;

                        if ($cekOuts >= 0) {
                            $detailOuts->outstanding_qty = $detailOuts->outstanding_qty - $detail->qty_item;
                            $detailOuts->save();
                        }
                    }
                    StockTransaction::insert($listDetail);

                    $totalOuts = ProductionOrder::where([
                                                    ['id', '=', $receiving->id_po],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_qty = $totalOuts->outstanding_qty - $receiving->jumlah_total_sj;
                    if ($totalOuts->outstanding_qty == 0) {
                        $totalOuts->status = 'full';
                    }

                    $totalOuts->save();

                    $receiving->status_penerimaan = "posted";
                    $receiving->save();

                    $log = ActionLog::create([
                        'module' => 'ProductionReceiving',
                        'action' => 'Posting',
                        'desc' => 'Posting ProductionReceiving',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Penerimaan Barang Produksi '.strtoupper($receiving->kode_penerimaan).' Telah Diposting!';
                    $status = 'success';
                }
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "revisi") {
                $receiving->status_penerimaan = "draft";
                $receiving->flag_revisi = '1';
                $receiving->updated_by = Auth::user()->user_name;
                $receiving->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $receiving->kode_penerimaan)->delete();

                $detailRcv = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('production_order_detail', function($join) {
                                                $join->on('production_order_detail.id_item', '=', 'production_receiving_detail.id_item');
                                                $join->on('production_order_detail.id_satuan', '=', 'production_receiving_detail.id_satuan');
                                            })
                                            ->select(
                                                'production_receiving_detail.id',
                                                'production_receiving_detail.id_item',
                                                'production_receiving_detail.id_satuan',
                                                'production_receiving_detail.qty_item',
                                                'production_order_detail.qty_order',
                                                'production_order_detail.outstanding_qty',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_receiving_detail.id_penerimaan', '=', $id],
                                                ['production_order_detail.id_po', '=', $receiving->id_po]
                                            ])
                                            ->get();

                foreach ($detailRcv As $detail) {

                    $detailOuts = ProductionOrderDetail::where([
                                                        ['id_po', '=', $receiving->id_po],
                                                        ['id_item', '=', $detail->id_item],
                                                        ['id_satuan', '=', $detail->id_satuan],
                                                    ])
                                                    ->first();

                    $detailOuts->outstanding_qty = $detailOuts->outstanding_qty + $detail->qty_item;
                    $detailOuts->save();

                }

                $totalOuts = ProductionOrder::where([
                                                ['id', '=', $receiving->id_po],
                                            ])
                                            ->first();

                $totalOuts->outstanding_qty = $totalOuts->outstanding_qty + $receiving->jumlah_total_sj;
                if ($totalOuts->outstanding_qty == 0) {
                    $totalOuts->status = 'full';
                }
                else {
                    $totalOuts->status = 'posted';
                }
                $totalOuts->save();

                $log = ActionLog::create([
                    'module' => 'Penerimaan Barang',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Penerimaan Barang',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $receiving->status_penerimaan = "batal";
                $receiving->updated_by = Auth::user()->user_name;
                $receiving->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $receiving->kode_penerimaan)->delete();

                $detailRcv = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('production_order_detail', function($join) {
                                                $join->on('production_order_detail.id_item', '=', 'production_receiving_detail.id_item');
                                                $join->on('production_order_detail.id_satuan', '=', 'production_receiving_detail.id_satuan');
                                            })
                                            ->select(
                                                'production_receiving_detail.id',
                                                'production_receiving_detail.id_item',
                                                'production_receiving_detail.id_satuan',
                                                'production_receiving_detail.qty_item',
                                                'production_order_detail.qty_order',
                                                'production_order_detail.outstanding_qty',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_receiving_detail.id_penerimaan', '=', $id],
                                                ['production_order_detail.id_po', '=', $receiving->id_po]
                                            ])
                                            ->get();

                foreach ($detailRcv As $detail) {

                    $detailOuts = ProductionOrderDetail::where([
                                                        ['id_po', '=', $receiving->id_po],
                                                        ['id_item', '=', $detail->id_item],
                                                        ['id_satuan', '=', $detail->id_satuan]
                                                    ])
                                                    ->first();

                    $detailOuts->outstanding_qty = $detailOuts->outstanding_qty + $detail->qty_item;
                    $detailOuts->save();

                }

                $totalOuts = ProductionOrder::where([
                                                ['id', '=', $receiving->id_po],
                                            ])
                                            ->first();

                $totalOuts->outstanding_qty = $totalOuts->outstanding_qty + $receiving->jumlah_total_sj;
                if ($totalOuts->outstanding_qty == 0) {
                    $totalOuts->status = 'full';
                }
                else {
                    $totalOuts->status = 'posted';
                }
                $totalOuts->save();

                $log = ActionLog::create([
                    'module' => 'Penerimaan',
                    'action' => 'Batal',
                    'desc' => 'Batal Penerimaan Barang',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Penerimaan '.strtoupper($receiving->kode_penerimaan).' Telah Dibatalkan!';
                $status = 'success';
            }
            else {
                $status = "ubahStaging";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('ProductionReceiving.edit', [$id]);
            }
            elseif ($status == "ubahStaging") {
                return redirect()->route('ProductionReceiving.Staging', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetProductionReceivingDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idRcv');


            if ($id != "DRAFT") {
                // $detail = ProductionReceivingDetail::where([
                //                             ['id_penerimaan', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'production_receiving'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('production_receiving_detail')->where('id_penerimaan', '=', $id)->delete();
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
                                            ['module.url', '=', '/ProductionReceiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataRcv = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                                    ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_detail', 'production_receiving.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'production_receiving.id',
                                        'production_receiving.kode_penerimaan',
                                        'production_receiving.id_po',
                                        'production_receiving.id_alamat',
                                        'production_receiving.tanggal_sj',
                                        'production_receiving.no_sj_supplier',
                                        'production_receiving.status_penerimaan',
                                        'production_receiving.flag_invoiced',
                                        'production_receiving.flag_revisi',
                                        'production_receiving.tanggal_terima',
                                        'production_order.id_supplier',
                                        'production_order.no_production_order',
                                        'supplier.nama_supplier',
                                        'supplier.telp_supplier',
                                        'supplier.fax_supplier',
                                        'supplier.email_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['production_receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ProductionReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = ProductionOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataAlamat = SupplierDetail::where([
                    ['id_supplier', '=', $dataRcv->id_supplier],
                    ['default', '=', 'Y']
                ])
                ->first();
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
                                            ->where('flag_rcv', 'Y')
                                            ->first();
                $detailProductionReceiving = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'production_receiving_detail.id',
                                                                'production_receiving_detail.id_item',
                                                                'production_receiving_detail.qty_item',
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan',
                                                                'product.keterangan_item'
                                                            )
                                                            ->where([
                                                                ['production_receiving_detail.id_penerimaan', '=', $id]
                                                            ])
                                                            ->get();


                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailProductionReceiving'] = $detailProductionReceiving;

                $log = ActionLog::create([
                    'module' => 'Penerimaan',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Penerimaan',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperProductionReceiving::cetakPdfDlv($data);

                $fpdf->Output('I', strtoupper($dataRcv->kode_penerimaan).".pdf");
                exit;
            }
            else {
                return redirect('/ProductionReceiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getProductDetail(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idPo = $request->input('idPo');

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.id',
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                )
                                ->where([
                                    ['product_detail.id_product', '=', $idProduct]
                                ])
                                ->whereIn('product_detail.id_satuan', function($subQuery) use ($idProduct, $idPo) {
                                    $subQuery->select('id_satuan')->from('production_order_detail')
                                    ->where([
                                        ['id_po', '=', $idPo],
                                        ['id_item', '=', $idProduct]
                                    ]);
                                })
                                ->get();

        return response()->json($detail);
    }

    public function StoreProductionReceivingAllocation(Request $request)
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
                                ['module', '=', 'production_receiving_allocation'],
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
                $listItem->module = 'production_receiving_allocation';
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idIndex;
                $listItem->value4 = $qty;
                $listItem->action = 'tambah';
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'ProductionReceiving Allocation',
                    'action' => 'Simpan',
                    'desc' => 'Simpan ProductionReceiving Allocation',
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
                                                ['temp_transaction.module', '=', 'production_receiving_allocation']
                                            ])
                                            ->groupBy('temp_transaction.value1')
                                            ->groupBy('temp_transaction.value2');
        }
        else {
            $dataAlokasi = ProductionReceivingAllocation::select(
                                                    'production_receiving_allocation.id_detail',
                                                    'production_receiving_allocation.id_item',
                                                    'production_receiving_allocation.qty_item',
                                                    DB::raw('SUM(production_receiving_allocation.qty_item) as sumAllocation')
                                                )
                                                ->where([
                                                    ['production_receiving_allocation.id_detail', '=', $idDetail],
                                                    ['production_receiving_allocation.id_item', '=', $idItem]
                                                ])
                                                ->groupBy('production_receiving_allocation.id_detail')
                                                ->groupBy('production_receiving_allocation.id_item');
        }

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);


        $dataDetail = ProductionReceivingDetail::leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                    ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoinSub($dataAlokasi, 'dataAlokasi', function($dataAlokasi) {
                                        $dataAlokasi->on('production_receiving_detail.id_item', '=', 'dataAlokasi.id_item');
                                        $dataAlokasi->on('production_receiving_detail.id', '=', 'dataAlokasi.id_detail');
                                    })
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'production_receiving_detail.id',
                                        'production_receiving_detail.qty_item',
                                        'production_receiving_detail.id_item',
                                        'product_category.nama_kategori',
                                        'product_unit.nama_satuan',
                                        'product.nama_item',
                                        DB::raw('COALESCE(dataAlokasi.sumAllocation, 0) as sumAllocation'),
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['production_receiving_detail.id', '=', $idDetail],
                                        ['production_receiving_detail.id_item', '=', $idItem]
                                    ])
                                    ->first();

        return response()->json($dataDetail);
    }

    public function GetProductionReceivingAllocation(Request $request)
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

            $detail = ProductionReceivingAllocation::leftJoin('product', 'production_receiving_allocation.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_receiving_allocation.id_satuan', 'product_unit.id')
                                        ->leftJoin('production_order_detail', 'production_order_detail.id_item', '=', 'production_receiving_allocation.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'production_receiving_allocation.id',
                                            'production_receiving_allocation.id_detail',
                                            'production_receiving_allocation.id_item',
                                            'production_receiving_allocation.id_satuan',
                                            'production_receiving_allocation.qty_item',
                                            'production_receiving_allocation.id_index',
                                            DB::raw('production_receiving_allocation.id_index as id_index'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['production_receiving_allocation.id_detail', '=', $id],
                                            ['production_receiving_allocation.id_item', '=', $idItem]
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
                                            ['temp_transaction.module', '=', 'production_receiving_allocation']
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

    public function DeleteProductionReceivingAllocation(Request $request)
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

    public function exportDataProductionReceiving(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new ProductionReceivingExport($request), 'RekapPenerimaanProduksi_'.$kodeTgl.'.xlsx');
    }
}
