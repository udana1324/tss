<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
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
use App\Classes\BusinessManagement\HelperReceiving;
use App\Exports\ReceivingExport;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\PurchaseOrderTerms;
use App\Models\Purchasing\ReceivingAllocation;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ReceivingController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Receiving'],
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
                                                ['module.url', '=', '/Receiving'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = Receiving::distinct()->get('status_penerimaan');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('receiving_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;


                $log = ActionLog::create([
                    'module' => 'Receiving',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Receiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.receiving.index', $data);
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

        $receiving = Receiving::leftJoin('purchase_order', 'receiving.id_po', 'purchase_order.id')
                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'purchase_order.no_po',
                                'receiving.id',
                                'receiving.kode_penerimaan',
                                'receiving.no_sj_supplier',
                                'receiving.jumlah_total_sj',
                                'receiving.tanggal_sj',
                                'receiving.tanggal_terima',
                                'receiving.flag_revisi',
                                'receiving.flag_invoiced',
                                'receiving.status_penerimaan')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('receiving.tanggal_sj', Carbon::parse($periode)->format('m'));
                                $q->whereYear('receiving.tanggal_sj', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('receiving.id', 'desc')
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
                                            ['module.url', '=', '/Receiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->where([
                                            ['purchase_order.status_po', '=', 'posted'],
                                            ['purchase_order.deleted_at', '=', null]
                                        ])
                                        ->orderBy('purchase_order.id', 'desc')
                                        ->get();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Receiving',
                    'action' => 'Buat',
                    'desc' => 'Buat Receiving',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('receiving_detail')
                            ->where([
                                ['id_penerimaan', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.purchasing.receiving.add', $data);
            }
            else {
                return redirect('/Receiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Receiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->where([
                                            ['purchase_order.status_po', '=', 'posted']
                                        ])
                                        ->orderBy('purchase_order.id', 'desc')
                                        ->get();

                $dataRcv = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('supplier_detail', 'receiving.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'receiving.id',
                                        'receiving.kode_penerimaan',
                                        'receiving.id_po',
                                        'receiving.id_alamat',
                                        'receiving.no_sj_supplier',
                                        'receiving.tanggal_sj',
                                        'receiving.tanggal_terima',
                                        'receiving.status_penerimaan',
                                        'purchase_order.id_supplier',
                                        'purchase_order.no_po',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->status_penerimaan != "draft") {
                    return redirect('/Receiving')->with('warning', 'Penerimaan tidak dapat diubah karena status Penerimaan bukan DRAFT!');
                }

                // $draftPo = PurchaseOrder::find($dataRcv->id_po);

                // if ($draftPo->status_po == "draft") {
                //     return redirect('/Receiving')->with('warning', 'Tidak dapat mengubah Penerimaan, PO '.strtoupper($draftPo->no_po).' Berstatus Draft');
                // }

                // $restore = ReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'receiving'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = ReceivingDetail::where([
                                                    ['id_penerimaan', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'receiving',
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

                $dataTerms = ReceivingTerms::where('id_receiving', $id)->get();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'Receiving',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Receiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.receiving.edit', $data);
            }
            else {
                return redirect('/Receiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Receiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRcv = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('preference', 'receiving.id_alamat', '=', 'preference.id')
                                    ->select(
                                        'receiving.id',
                                        'receiving.kode_penerimaan',
                                        'receiving.id_po',
                                        'receiving.id_alamat',
                                        'receiving.tanggal_sj',
                                        'receiving.tanggal_terima',
                                        'receiving.status_penerimaan',
                                        'receiving.flag_terms_po',
                                        'receiving.flag_revisi',
                                        'receiving.no_sj_supplier',
                                        'receiving.jumlah_total_sj',
                                        'purchase_order.id_supplier',
                                        'purchase_order.no_po',
                                        'supplier.nama_supplier',
                                        'preference.alamat_pt',
                                    )
                                    ->where([
                                        ['receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = PurchaseOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailRcv = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'receiving_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'receiving_detail.id',
                                            'receiving_detail.id_item',
                                            'receiving_detail.id_satuan',
                                            'receiving_detail.qty_item',
                                            'receiving_detail.id_penerimaan',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['receiving_detail.id_penerimaan', '=', $id],
                                            ['purchase_order_detail.id_po', '=', $dataRcv->id_po]
                                        ])
                                        ->get();

                $alokasiRcv = ReceivingAllocation::leftJoin('product_unit', 'receiving_allocation.id_satuan', 'product_unit.id')
                ->where([
                    ['receiving_allocation.id_receiving', '=', $id]
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
                    'module' => 'Receiving',
                    'action' => 'Staging',
                    'desc' => 'Staging Receiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.receiving.staging', $data);
            }
            else {
                return redirect('/Receiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Receiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRcv = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('preference', 'receiving.id_alamat', '=', 'preference.id')
                                    ->select(
                                        'receiving.id',
                                        'receiving.kode_penerimaan',
                                        'receiving.id_po',
                                        'receiving.id_alamat',
                                        'receiving.tanggal_sj',
                                        'receiving.tanggal_terima',
                                        'receiving.status_penerimaan',
                                        'receiving.flag_terms_po',
                                        'receiving.flag_revisi',
                                        'receiving.no_sj_supplier',
                                        'receiving.jumlah_total_sj',
                                        'purchase_order.id_supplier',
                                        'purchase_order.no_po',
                                        'supplier.nama_supplier',
                                        'preference.alamat_pt',
                                    )
                                    ->where([
                                        ['receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = PurchaseOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailRcv = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'receiving_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'receiving_detail.id',
                                            'receiving_detail.id_item',
                                            'receiving_detail.qty_item',
                                            'receiving_detail.id_penerimaan',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['receiving_detail.id_penerimaan', '=', $id],
                                            ['purchase_order_detail.id_po', '=', $dataRcv->id_po]
                                        ])
                                        ->get();

                $alokasiRcv = ReceivingAllocation::leftJoin('product', 'receiving_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'receiving_allocation.id_satuan', 'product_unit.id')
                                                ->where([
                                                    ['receiving_allocation.id_receiving', '=', $id]
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
                    'module' => 'Receiving',
                    'action' => 'Detail',
                    'desc' => 'Detail Receiving',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.receiving.detail', $data);
            }
            else {
                return redirect('/Receiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function RestoreReceivingDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idRcv');
            $restore = ReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {

        }
        else {
            return response()->json($exception);
        }

    }

    public function getPurchaseOrder(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataPo = PurchaseOrder::where([
                                            ['id_supplier', '=', $idSupplier],
                                            ['status_po', '=', 'posted']
                                        ])
                                        ->orderBy('id', 'asc')
                                        ->get();

        return response()->json($dataPo);
    }

    public function getProduct(Request $request)
    {
        $idPurchaseOrder = $request->input('idPurchaseOrder');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', 'product.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['purchase_order_detail.id_po', '=', $idPurchaseOrder],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->orderBy('product.nama_item', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDefaultAddress(Request $request)
    {
        $idPurchaseOrder = $request->input('idPurchaseOrder');

        $idAlamat = PurchaseOrder::find($idPurchaseOrder);

        $defaultAddress = Preference::where([
                                            ['id', '=', $idAlamat->id_alamat]
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getTanggalPo(Request $request)
    {
        $idPurchaseOrder = $request->input('idPurchaseOrder');

        $dataPo = PurchaseOrder::find($idPurchaseOrder);

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
        $idPo = $request->input('idPurchaseOrder');
        $idSatuan = $request->input('id_satuan');

        $dataProduct = Product::leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'product_unit.nama_satuan',
                                            'purchase_order_detail.outstanding_qty',
                                        )
                                        ->where([
                                            ['product.id', '=', $idProduct],
                                            ['purchase_order_detail.id_po', '=', $idPo],
                                            ['purchase_order_detail.id_satuan', '=', $idSatuan],
                                        ])
                                        ->get();

        return response()->json($dataProduct);
    }

    public function StoreReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReceiving');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = ReceivingDetail::select(DB::raw("COUNT(*) AS angka"))
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

                    $listItem = new ReceivingDetail();
                    $listItem->id_penerimaan = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Receiving Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Receiving Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'receiving'],
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
                    $listItem->module = 'receiving';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Receiving Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Receiving Detail',
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

    public function UpdateReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReceiving');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = ReceivingDetail::find($idDetail);
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
                'module' => 'Receiving Detail',
                'action' => 'Update',
                'desc' => 'Update Receiving Detail',
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

    public function SetReceivingDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReceiving');
            $idPo = $request->input('idPurchaseOrder');
            $user = Auth::user()->user_name;
            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != "DRAFT") {
                if ($id != "DRAFT") {
                    $update = DB::table('temp_transaction')
                                ->where([
                                    ['value1', '=', $id],
                                    ['module', '=', "receiving"]
                                ])
                                ->update([
                                    'action' => "hapus",
                                    'deleted_by' => Auth::user()->user_name,
                                    'deleted_at' => now()
                                ]);

                    $detail = PurchaseOrderDetail::select(
                                                'purchase_order_detail.id_item',
                                                'purchase_order_detail.id_satuan',
                                                'purchase_order_detail.outstanding_qty'
                                            )
                                            ->where([
                                                ['purchase_order_detail.id_po', '=', $idPo]
                                            ])
                                            ->get();
                    $data = $detail;
                    $listDetail = [];
                    foreach ($detail As $detailDlv) {
                        $dataDetail = [
                            'module' => "receiving",
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
                $delete = DB::table('receiving_detail')
                            ->where('id_penerimaan', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('receiving_detail.created_by', $user);
                            })
                            ->delete();

                $detail = PurchaseOrderDetail::select(
                                                'purchase_order_detail.id_item',
                                                'purchase_order_detail.id_satuan',
                                                'purchase_order_detail.outstanding_qty'
                                            )
                                            ->where([
                                                ['purchase_order_detail.id_po', '=', $idPo],
                                                ['purchase_order_detail.outstanding_qty', '>', 0]
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
                ReceivingDetail::insert($listDetail);
            }
        });

        $log = ActionLog::create([
            'module' => 'Receiving',
            'action' => 'Set Detail',
            'desc' => 'Set Receiving Detail',
            'username' => Auth::user()->user_name
        ]);

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetReceivingDetail(Request $request)
    {
        $id = $request->input('idReceiving');
        $idPo = $request->input('idPurchaseOrder');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('purchase_order_detail', function($join) {
                                            $join->on('purchase_order_detail.id_item', '=', 'receiving_detail.id_item');
                                            $join->on('purchase_order_detail.id_satuan', '=', 'receiving_detail.id_satuan');
                                        })
                                        ->select(
                                            'receiving_detail.id',
                                            'receiving_detail.id_item',
                                            'receiving_detail.qty_item',
                                            'receiving_detail.id_satuan',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['receiving_detail.id_penerimaan', '=', $id],
                                            ['purchase_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('receiving_detail.created_by', $user);
                                        })
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('purchase_order_detail', function($join) {
                                        $join->on('purchase_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('purchase_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'receiving'],
                                            ['purchase_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditReceivingDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $idPo = $request->input('idPurchaseOrder');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = ReceivingDetail::leftJoin('purchase_order_detail', 'receiving_detail.id_item', '=', 'purchase_order_detail.id_item')
                                        ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'receiving_detail.id',
                                            'receiving_detail.id_item',
                                            'receiving_detail.id_satuan',
                                            'receiving_detail.qty_item',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['receiving_detail.id', '=', $id],
                                            ['purchase_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('purchase_order_detail', function($join) {
                                        $join->on('purchase_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('purchase_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'purchase_order_detail.qty_order',
                                            'purchase_order_detail.outstanding_qty',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'receiving'],
                                            ['purchase_order_detail.id_po', '=', $idPo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteReceivingDetail(Request $request)
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
                $delete = DB::table('receiving_detail')->where('id', '=', $id)->delete();
            }

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetReceivingFooter(Request $request)
    {
        $id = $request->input('idReceiving');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {

            $detail = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                            ->select(
                                                DB::raw('COALESCE(SUM(receiving_detail.qty_item),0) AS qtyItem'),
                                            )
                                            ->where([
                                                ['receiving_detail.id_penerimaan', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('receiving_detail.created_by', $user);
                                            })
                                            ->groupBy('receiving_detail.id_penerimaan')
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
                                            ['temp_transaction.module', '=', 'receiving']
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
            'purchaseOrder'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect('/Receiving')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idPurchaseOrder = $request->input('purchaseOrder');
            $tglSj = $request->input('tanggal_sj');
            $tglTerima = $request->input('tanggal_terima');
            $noSjSupplier = $request->input('no_sj_supplier');
            $qtyTerima = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idPurchaseOrder;
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

            $countKode = DB::table('receiving')
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
                $nmrRcv = "pb-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrRcv = "pb-cv-".$kodeTgl.$counter;
            }

            $receiving = new Receiving();
            $receiving->kode_penerimaan = $nmrRcv;
            $receiving->id_po = $idPurchaseOrder;
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

            $setDetail = DB::table('receiving_detail')
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
                    ReceivingTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Receiving',
                'action' => 'Simpan',
                'desc' => 'Simpan Receiving',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('Receiving.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_penerimaan).' Telah Disimpan!');
        }
        else {
            return redirect('/Receiving')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'purchaseOrder'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect()->route('Receiving.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idPurchaseOrder = $request->input('purchaseOrder');
            $tglSj = $request->input('tanggal_sj');
            $tglTerima = $request->input('tanggal_terima');
            $noSjSupplier = $request->input('no_sj_supplier');
            $qtyTerima = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idPurchaseOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyTerima = str_replace(",", ".", $qtyTerima);

            $receiving = Receiving::find($id);
            $receiving->id_po = $idPurchaseOrder;
            $receiving->id_alamat = $idAlamat;
            $receiving->jumlah_total_sj = $qtyTerima;
            $receiving->no_sj_supplier = $noSjSupplier;
            $receiving->tanggal_sj = $tglSj;
            $receiving->tanggal_terima = $tglTerima;
            $receiving->flag_terms_po = $flagTermsUsage;
            $receiving->updated_by = $user;
            $receiving->save();

            $data = $receiving;

            // $deletedDetail = ReceivingDetail::onlyTrashed()->where([['id_penerimaan', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'receiving'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = ReceivingDetail::find($detail->id_detail);
                        $listItem->id_penerimaan = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new ReceivingDetail();
                        $listItem->id_penerimaan = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('receiving_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'receiving'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($flagTerms != "termsPo") {
                $delete = DB::table('receiving_terms')->where('id_receiving', '=', $id)->delete();
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
                    ReceivingTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Receiving',
                'action' => 'Update',
                'desc' => 'Update Receiving',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('Receiving.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_penerimaan).' Telah Diubah!');
        }
        else {
            return redirect('/Receiving')->with('error', $exception);
        }
    }

    public function postAllocation(Request $request, $id)
    {
        $data = new stdClass();
        $btnAction = $request->input('submit_action');
        if ($btnAction == "ubah") {
            return redirect()->route('Receiving.edit', [$id]);
        }
        $dlv = Receiving::find($id);
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $receiving = Receiving::find($id);
            $arrayDetail = $request->input('isi');
            $listAlokasi = [];
            $listDetail = [];
            $ttlAlokasi = 0;
            if ($arrayDetail != "") {
                $delete = DB::table('receiving_allocation')
                            ->where([
                                ['receiving_allocation.id_receiving', '=', $id]
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

                ReceivingAllocation::insert($listAlokasi);
            }

            $log = ActionLog::create([
                'module' => 'Receiving',
                'action' => 'Alokasi',
                'desc' => 'Alokasi Receiving',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('Receiving.Detail', [$dlv->id])->with('success', 'Data Alokasi '.strtoupper($dlv->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/Receiving')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $receiving = Receiving::find($id);

            $cekSjInvoiced = PurchaseInvoiceDetail::leftJoin('purchase_invoice', 'purchase_invoice_detail.id_invoice', 'purchase_invoice.id')
                                                    ->where([
                                                        ['purchase_invoice_detail.id_sj', '=', $id],
                                                        ['purchase_invoice.status_invoice', '!=', 'draft']
                                                    ])
                                                    ->count();
            if ($btnAction == "posting") {
                $detailRcv = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('purchase_order_detail', function($join) {
                                                $join->on('purchase_order_detail.id_item', '=', 'receiving_detail.id_item');
                                                $join->on('purchase_order_detail.id_satuan', '=', 'receiving_detail.id_satuan');
                                            })
                                            ->select(
                                                'receiving_detail.id',
                                                'receiving_detail.id_item',
                                                'receiving_detail.qty_item',
                                                'receiving_detail.id_satuan',
                                                'purchase_order_detail.qty_order',
                                                'purchase_order_detail.outstanding_qty',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['receiving_detail.id_penerimaan', '=', $id],
                                                ['purchase_order_detail.id_po', '=', $receiving->id_po]
                                            ])
                                            ->get();
                $transaksi = [];
                $failedItem = [];
                foreach ($detailRcv As $detail) {
                    $detailOuts = PurchaseOrderDetail::where([
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

                $alokasiRcv = ReceivingAllocation::where([
                                                        ['receiving_allocation.id_receiving', '=', $id]
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
                            'jenis_transaksi' => "penerimaan",
                            'transaksi' => "in",
                            'jenis_sumber' => 1,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name,
                        ];
                        array_push($listDetail, $dataStok);

                        $ttlAlokasi = $ttlAlokasi + $detilAlokasi["qty"];
                    }
                }

                if (count($failedItem) > 0) {
                    $msg = 'Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' Gagal Diposting! Item ('.strtoupper(implode(', ', $failedItem)).')';
                    $status = 'warning';
                }
                elseif (count($alokasiRcv) < 1) {
                    $msg = 'Pengiriman Barang '.strtoupper($receiving->kode_penerimaan).' Gagal Diposting! Lakukan Alokasi Penerimaan Terlebih dahulu!';
                    $status = 'warning';
                }
                else {
                    foreach ($detailRcv As $detail) {
                        $detailOuts = PurchaseOrderDetail::where([
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

                    $totalOuts = PurchaseOrder::where([
                                                    ['id', '=', $receiving->id_po],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_po = $totalOuts->outstanding_po - $receiving->jumlah_total_sj;
                    if ($totalOuts->outstanding_po == 0) {
                        $totalOuts->status_po = 'full';
                    }

                    $totalOuts->save();

                    $receiving->status_penerimaan = "posted";
                    $receiving->save();

                    $log = ActionLog::create([
                        'module' => 'Receiving',
                        'action' => 'Posting',
                        'desc' => 'Posting Receiving',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' Telah Diposting!';
                    $status = 'success';
                }
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "revisi") {
                if ($cekSjInvoiced == 0) {
                    $receiving->status_penerimaan = "draft";
                    $receiving->flag_revisi = '1';
                    $receiving->updated_by = Auth::user()->user_name;
                    $receiving->save();

                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $receiving->kode_penerimaan)->delete();

                    $detailRcv = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                                ->leftJoin('purchase_order_detail', function($join) {
                                                    $join->on('purchase_order_detail.id_item', '=', 'receiving_detail.id_item');
                                                    $join->on('purchase_order_detail.id_satuan', '=', 'receiving_detail.id_satuan');
                                                })
                                                ->select(
                                                    'receiving_detail.id',
                                                    'receiving_detail.id_item',
                                                    'receiving_detail.id_satuan',
                                                    'receiving_detail.qty_item',
                                                    'purchase_order_detail.qty_order',
                                                    'purchase_order_detail.outstanding_qty',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan'
                                                )
                                                ->where([
                                                    ['receiving_detail.id_penerimaan', '=', $id],
                                                    ['purchase_order_detail.id_po', '=', $receiving->id_po]
                                                ])
                                                ->get();

                    foreach ($detailRcv As $detail) {

                        $detailOuts = PurchaseOrderDetail::where([
                                                            ['id_po', '=', $receiving->id_po],
                                                            ['id_item', '=', $detail->id_item],
                                                            ['id_satuan', '=', $detail->id_satuan],
                                                        ])
                                                        ->first();

                        $detailOuts->outstanding_qty = $detailOuts->outstanding_qty + $detail->qty_item;
                        $detailOuts->save();

                    }

                    $totalOuts = PurchaseOrder::where([
                                                    ['id', '=', $receiving->id_po],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_po = $totalOuts->outstanding_po + $receiving->jumlah_total_sj;
                    if ($totalOuts->outstanding_po == 0) {
                        $totalOuts->status_po = 'full';
                    }
                    else {
                        $totalOuts->status_po = 'posted';
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
                else {
                    $msg = 'Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' Tidak dapat Direvisi karena terdapat Invoice Pembelian atas Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' !';
                    $status = 'warning';
                }
            }
            elseif ($btnAction == "batal") {
                if ($cekSjInvoiced == 0) {
                    $receiving->status_penerimaan = "batal";
                    $receiving->updated_by = Auth::user()->user_name;
                    $receiving->save();

                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $receiving->kode_penerimaan)->delete();

                    $detailRcv = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                                ->leftJoin('purchase_order_detail', function($join) {
                                                    $join->on('purchase_order_detail.id_item', '=', 'receiving_detail.id_item');
                                                    $join->on('purchase_order_detail.id_satuan', '=', 'receiving_detail.id_satuan');
                                                })
                                                ->select(
                                                    'receiving_detail.id',
                                                    'receiving_detail.id_item',
                                                    'receiving_detail.id_satuan',
                                                    'receiving_detail.qty_item',
                                                    'purchase_order_detail.qty_order',
                                                    'purchase_order_detail.outstanding_qty',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan'
                                                )
                                                ->where([
                                                    ['receiving_detail.id_penerimaan', '=', $id],
                                                    ['purchase_order_detail.id_po', '=', $receiving->id_po]
                                                ])
                                                ->get();

                    foreach ($detailRcv As $detail) {

                        $detailOuts = PurchaseOrderDetail::where([
                                                            ['id_po', '=', $receiving->id_po],
                                                            ['id_item', '=', $detail->id_item],
                                                            ['id_satuan', '=', $detail->id_satuan]
                                                        ])
                                                        ->first();

                        $detailOuts->outstanding_qty = $detailOuts->outstanding_qty + $detail->qty_item;
                        $detailOuts->save();

                    }

                    $totalOuts = PurchaseOrder::where([
                                                    ['id', '=', $receiving->id_po],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_po = $totalOuts->outstanding_po + $receiving->jumlah_total_sj;
                    if ($totalOuts->outstanding_po == 0) {
                        $totalOuts->status_po = 'full';
                    }
                    else {
                        $totalOuts->status_po = 'posted';
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
                    $msg = 'Penerimaan '.strtoupper($receiving->kode_penerimaan).' Tidak dapat Dibatalkan karena terdapat Invoice Pembelian atas Penerimaan Barang '.strtoupper($receiving->kode_penerimaan).' !';
                    $status = 'warning';
                }
            }
            else {
                $status = "ubahStaging";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('Receiving.edit', [$id]);
            }
            elseif ($status == "ubahStaging") {
                return redirect()->route('Receiving.Staging', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetReceivingDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idRcv');


            if ($id != "DRAFT") {
                // $detail = ReceivingDetail::where([
                //                             ['id_penerimaan', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'receiving'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('receiving_detail')->where('id_penerimaan', '=', $id)->delete();
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
                                            ['module.url', '=', '/Receiving'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataRcv = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_detail', 'receiving.id_alamat', '=', 'supplier_detail.id')
                                    ->select(
                                        'receiving.id',
                                        'receiving.kode_penerimaan',
                                        'receiving.id_po',
                                        'receiving.id_alamat',
                                        'receiving.tanggal_sj',
                                        'receiving.no_sj_supplier',
                                        'receiving.status_penerimaan',
                                        'receiving.flag_invoiced',
                                        'receiving.flag_revisi',
                                        'receiving.tanggal_terima',
                                        'purchase_order.id_supplier',
                                        'purchase_order.no_po',
                                        'supplier.nama_supplier',
                                        'supplier.telp_supplier',
                                        'supplier.fax_supplier',
                                        'supplier.email_supplier',
                                        'supplier_detail.alamat_supplier',
                                    )
                                    ->where([
                                        ['receiving.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataRcv->flag_terms_po == "0") {
                    $dataTerms = ReceivingTerms::where('id_receiving', $id)->get();
                }
                else {
                    $dataTerms = PurchaseOrderTerms::where('id_po', $dataRcv->id_po)->get();
                }

                $dataAlamat = SupplierDetail::find($dataRcv->id_alamat);
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
                $detailReceiving = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'receiving_detail.id',
                                                                'receiving_detail.id_item',
                                                                'receiving_detail.qty_item',
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan',
                                                                'product.keterangan_item'
                                                            )
                                                            ->where([
                                                                ['receiving_detail.id_penerimaan', '=', $id]
                                                            ])
                                                            ->get();

                $dataAlamat = SupplierDetail::find($dataRcv->id_alamat);

                $data['dataRcv'] = $dataRcv;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailReceiving'] = $detailReceiving;

                $log = ActionLog::create([
                    'module' => 'Penerimaan',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Penerimaan',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperReceiving::cetakPdfDlv($data);

                $fpdf->Output('I', strtoupper($dataRcv->kode_penerimaan).".pdf");
                exit;
            }
            else {
                return redirect('/Receiving')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                    $subQuery->select('id_satuan')->from('purchase_order_detail')
                                    ->where([
                                        ['id_po', '=', $idPo],
                                        ['id_item', '=', $idProduct]
                                    ]);
                                })
                                ->get();

        return response()->json($detail);
    }

    public function StoreReceivingAllocation(Request $request)
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
                                ['module', '=', 'receiving_allocation'],
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
                $listItem->module = 'receiving_allocation';
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idIndex;
                $listItem->value4 = $qty;
                $listItem->action = 'tambah';
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'Receiving Allocation',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Receiving Allocation',
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
                                                ['temp_transaction.module', '=', 'receiving_allocation']
                                            ])
                                            ->groupBy('temp_transaction.value1')
                                            ->groupBy('temp_transaction.value2');
        }
        else {
            $dataAlokasi = ReceivingAllocation::select(
                                                    'receiving_allocation.id_detail',
                                                    'receiving_allocation.id_item',
                                                    'receiving_allocation.qty_item',
                                                    DB::raw('SUM(receiving_allocation.qty_item) as sumAllocation')
                                                )
                                                ->where([
                                                    ['receiving_allocation.id_detail', '=', $idDetail],
                                                    ['receiving_allocation.id_item', '=', $idItem]
                                                ])
                                                ->groupBy('receiving_allocation.id_detail')
                                                ->groupBy('receiving_allocation.id_item');
        }

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);


        $dataDetail = ReceivingDetail::leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                    ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoinSub($dataAlokasi, 'dataAlokasi', function($dataAlokasi) {
                                        $dataAlokasi->on('receiving_detail.id_item', '=', 'dataAlokasi.id_item');
                                        $dataAlokasi->on('receiving_detail.id', '=', 'dataAlokasi.id_detail');
                                    })
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'receiving_detail.id',
                                        'receiving_detail.qty_item',
                                        'receiving_detail.id_item',
                                        'product_category.nama_kategori',
                                        'product_unit.nama_satuan',
                                        'product.nama_item',
                                        DB::raw('COALESCE(dataAlokasi.sumAllocation, 0) as sumAllocation'),
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['receiving_detail.id', '=', $idDetail],
                                        ['receiving_detail.id_item', '=', $idItem]
                                    ])
                                    ->first();

        return response()->json($dataDetail);
    }

    public function GetReceivingAllocation(Request $request)
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

            $detail = ReceivingAllocation::leftJoin('product', 'receiving_allocation.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_allocation.id_satuan', 'product_unit.id')
                                        ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'receiving_allocation.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'receiving_allocation.id',
                                            'receiving_allocation.id_detail',
                                            'receiving_allocation.id_item',
                                            'receiving_allocation.id_satuan',
                                            'receiving_allocation.qty_item',
                                            'receiving_allocation.id_index',
                                            DB::raw('receiving_allocation.id_index as id_index'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['receiving_allocation.id_detail', '=', $id],
                                            ['receiving_allocation.id_item', '=', $idItem]
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
                                        ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'temp_transaction.value2')
                                        ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
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
                                            ['temp_transaction.module', '=', 'receiving_allocation']
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

    public function DeleteReceivingAllocation(Request $request)
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

    public function exportDataReceiving(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new ReceivingExport($request), 'Receiving_'.$kodeTgl.'.xlsx');
    }
}
