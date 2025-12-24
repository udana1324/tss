<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Library\SupplierProduct;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Purchasing\PurchaseOrderTerms;
use App\Models\Purchasing\Receiving;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperPurchaseOrder;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\Purchase;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductUnit;
use App\Models\Purchasing\ReceivingDetail;
use App\Models\Purchasing\PurchaseReturn;
use App\Models\Purchasing\PurchaseReturnDetail;
use App\Models\Purchasing\PurchaseReturnItem;
use App\Models\Purchasing\PurchaseReturnItemDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseReturn'],
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
                                                ['module.url', '=', '/PurchaseReturn'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = PurchaseReturn::distinct()->get('status_retur');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('purchase_return_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Purchase Return',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Purchase Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_return.index', $data);
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

        $salesOrder = PurchaseReturn::leftJoin('supplier', 'purchase_return.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'purchase_return.id',
                                'purchase_return.kode_retur',
                                'purchase_return.nota_retur',
                                'purchase_return.jumlah_total_retur',
                                'purchase_return.nominal_retur',
                                'purchase_return.tanggal_retur',
                                'purchase_return.flag_revisi',
                                'purchase_return.status_retur')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('purchase_return.tanggal_retur', Carbon::parse($periode)->format('m'));
                                $q->whereYear('purchase_return.tanggal_retur', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('purchase_return.id', 'desc')
                            ->get();
        return response()->json($salesOrder);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();

                $idRetur = Session::get('id_retur');
                $idSupp = Session::get('id_supp');
                if ($idSupp == "" && $idRetur == "") {
                    $mode = "tambah";
                }
                else {
                    $mode = "nota";
                }
                Session::forget('id_po');
                Session::forget('id_supp');

                $parentMenu = Module::find($hakAkses->parent);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['idRetur'] = $idRetur;
                $data['idSupp'] = $idSupp;
                $data['mode'] = $mode;

                $log = ActionLog::create([
                    'module' => 'Purchase Retur',
                    'action' => 'Buat',
                    'desc' => 'Buat Purchase Retur',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('purchase_return_detail')
                            ->where([
                                ['id_retur', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.purchasing.purchase_return.add', $data);
            }
            else {
                return redirect('/PurchaseReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRetur = PurchaseReturn::find($id);

                if ($dataRetur->status_retur != "draft") {
                    return redirect('/PurchaseReturn')->with('warning', 'Retur tidak dapat diubah karena status Retur bukan DRAFT!');
                }

                // $restore = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_return'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = PurchaseReturnDetail::where([
                                                    ['id_retur', '=', $id]
                                                ])
                                                ->get();
                                                //dd($dataDetail);

                //Legend
                // 'value1' => $detail->id_retur,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,
                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'purchase_return',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_retur,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->id_index,
                            'value5' => $detail->qty_item,
                            'value6' => $detail->harga_retur,
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Purchase Return',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Purchase Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_return.edit', $data);
            }
            else {
                return redirect('/PurchaseReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataRetur = PurchaseReturn::leftJoin('purchase_return_item', 'purchase_return.id_retur', '=', 'purchase_return_item.id')
                                        ->select(
                                            DB::raw('purchase_return_item.kode_retur as kode_retur_item'),
                                            'purchase_return.*'
                                        )
                                        ->where([
                                            ['purchase_return.id', '=', $id]
                                        ])
                                        ->first();
                $dataSupplier = Supplier::find($dataRetur->id_supplier);

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataRetur->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Purchase Return',
                    'action' => 'Detil',
                    'desc' => 'Detil Purchase Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_return.detail', $data);
            }
            else {
                return redirect('/PurchaseReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetak($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataPurchaseOrder = PurchaseOrder::leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->select(
                                                'supplier.kode_supplier',
                                                'supplier.nama_supplier',
                                                'supplier.npwp_supplier',
                                                'supplier.telp_supplier',
                                                'supplier.fax_supplier',
                                                'supplier.email_supplier',
                                                'supplier.kategori_supplier',
                                                'purchase_order.*'
                                            )
                                            ->where([
                                                ['purchase_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = PurchaseOrderTerms::where('id_po', $id)->get();
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
                                            ->where('flag_po', 'Y')
                                            ->first();
                $detailPurchaseOrder = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'purchase_order_detail.id',
                                                                'purchase_order_detail.id_item',
                                                                'purchase_order_detail.qty_item',
                                                                'purchase_order_detail.harga_beli',
                                                                DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['purchase_order_detail.id_po', '=', $id]
                                                            ])
                                                            ->get();
                $dataAlamat = SupplierDetail::find($dataPurchaseOrder->id_alamat);
                $taxSettings = TaxSettings::find(1);

                $data['taxSettings'] = $taxSettings;

                $data['dataPurchaseOrder'] = $dataPurchaseOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseOrder'] = $detailPurchaseOrder;

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperPurchaseOrder::cetakPdfPO($data);

                $fpdf->Output('I', strtoupper($dataPurchaseOrder->no_po).".pdf");
                exit;
            }
            else {
                return redirect('/PurchaseOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function print($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataPurchaseOrder = PurchaseOrder::leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->select(
                                                'supplier.kode_supplier',
                                                'supplier.nama_supplier',
                                                'supplier.npwp_supplier',
                                                'supplier.telp_supplier',
                                                'supplier.fax_supplier',
                                                'supplier.email_supplier',
                                                'supplier.kategori_supplier',
                                                'purchase_order.*'
                                            )
                                            ->where([
                                                ['purchase_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = PurchaseOrderTerms::where('id_po', $id)->get();
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
                                            ->where('flag_po', 'Y')
                                            ->first();
                $detailPurchaseOrder = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'purchase_order_detail.id',
                                                                'purchase_order_detail.id_item',
                                                                'purchase_order_detail.qty_item',
                                                                'purchase_order_detail.harga_beli',
                                                                DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['purchase_order_detail.id_po', '=', $id]
                                                            ])
                                                            ->get();
                $dataAlamat = SupplierDetail::find($dataPurchaseOrder->id_alamat);
                $taxSettings = TaxSettings::find(1);

                $data['taxSettings'] = $taxSettings;


                $data['dataPurchaseOrder'] = $dataPurchaseOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseOrder'] = $detailPurchaseOrder;


                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_order.print', $data);
            }
            else {
                return redirect('/PurchaseOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getSupplierReturn(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataProduct = PurchaseReturnItem::select('purchase_return_item.id', 'purchase_return_item.kode_retur')
                                        ->where([
                                            ['purchase_return_item.id_supplier', '=', $idSupplier]
                                        ])
                                        ->orderBy('purchase_return_item.kode_retur', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
        $idIndex = $request->input('id_index');
        $idSupplier = $request->input('id_supplier');

        if ($idProduct != "" && $idSatuan != "") {

            $dataProduct = PurchaseReturnItemDetail::select('id_item', 'id_satuan', 'qty_item')
                                                ->where([
                                                    ['purchase_return_item_detail.id_supplier', '=', $idSupplier],
                                                    ['purchase_return_item_detail.id_item', '=', $idProduct],
                                                    ['purchase_return_item_detail.id_satuan', '=', $idSatuan],
                                                    ['purchase_return_item_detail.id_index', '=', $idIndex],
                                                ])
                                                ->first();

            $dataProductRetur = $dataProduct->qty_item;
        }
        else {
            $dataProductRetur = "";
        }

        return response()->json($dataProductRetur);
    }

    public function GetPurchaseReturnDetail(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        $detailData = [];
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

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = PurchaseReturnDetail::leftJoin('product', 'purchase_return_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_return_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'purchase_return_detail.id',
                                            'purchase_return_detail.id_item',
                                            'purchase_return_detail.id_satuan',
                                            'purchase_return_detail.id_index',
                                            'purchase_return_detail.qty_item',
                                            'purchase_return_detail.harga_retur',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['purchase_return_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_return_detail.created_by', $user);
                                        })
                                        ->get();

            foreach($detail as $data) {
                $txtIndex = "-";
                foreach ($list as $txt) {
                    if ($txt["id"] == $data->id_index) {
                        $idIndex = $txt["id"];
                        $txtIndex = $txt["nama_index"];
                    }
                }
                $dataAlloc = [
                    'id' => $data->id,
                    'id_item' => $data->id_item,
                    'id_index' => $data->id_index,
                    'id_satuan' => $data->id_satuan,
                    'qty_item' => $data->qty_item,
                    'harga_retur' => $data->harga_retur,
                    'kode_item' => $data->kode_item,
                    'nama_item' => $data->nama_item,
                    'nama_satuan' => $data->nama_satuan,
                    'value_spesifikasi' => $data->value_spesifikasi,
                    'txt_index' => $txtIndex,
                ];
                array_push($detailData, $dataAlloc);
            }
        }
        else {
            //Legend
            // 'value1' => $detail->id_retur,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,

            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_return']
                                        ])
                                        ->get();

            foreach($detail as $data) {
                $txtIndex = "-";
                foreach ($list as $txt) {
                    if ($txt["id"] == $data->value4) {
                        $idIndex = $txt["id"];
                        $txtIndex = $txt["nama_index"];
                    }
                }
                $dataAlloc = [
                    'id' => $data->id,
                    'id_item' => $data->value2,
                    'id_satuan' => $data->value3,
                    'id_index' => $data->value4,
                    'qty_item' => $data->value5,
                    'harga_retur' => $data->value6,
                    'kode_item' => $data->kode_item,
                    'nama_item' => $data->nama_item,
                    'nama_satuan' => $data->nama_satuan,
                    'value_spesifikasi' => $data->value_spesifikasi,
                    'txt_index' => $txtIndex,
                ];
                array_push($detailData, $dataAlloc);
            }
        }


        return response()->json($detailData);
    }

    public function DeletePurchaseReturnDetail(Request $request)
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
                $delete = DB::table('purchase_return_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetPurchaseReturnFooter(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = PurchaseReturnDetail::leftJoin('product', 'purchase_return_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_return_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(purchase_return_detail.qty_item) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(purchase_return_detail.harga_retur,0) * COALESCE(purchase_return_detail.qty_item)) AS subtotal'),
                                        )
                                        ->where([
                                            ['purchase_return_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_return_detail.created_by', $user);
                                        })
                                        ->groupBy('purchase_return_detail.id_retur')
                                        ->first();
        }
        else {
            //Legend
            // 'value1' => $detail->id_po,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(temp_transaction.value5) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(temp_transaction.value5,0) * COALESCE(temp_transaction.value6)) AS subtotal'),
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_return']
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

    public function store(Request $request)
    {
        $request->validate([
            'supplier'=>'required',
            'tanggal_retur'=>'required',
        ]);

        $tglRetur = $request->input('tanggal_retur');

        $bulanIndonesia = Carbon::parse($tglRetur)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglRetur);
        if (!$aksesTransaksi) {
            return redirect('/PurchaseReturn')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idRetur = $request->input('data_retur');
            $tglRetur = $request->input('tanggal_retur');
            $notaRetur = $request->input('nota_retur');
            $qtyItem = $request->input('qtyTtl');
            $total = $request->input('gt');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));
            $tahunPeriode = date("y", strtotime($tglRetur));

            $countKode = DB::table('purchase_return')
                            ->select(DB::raw("MAX(RIGHT(kode_retur,2)) AS angka"))
                            // ->whereMonth('tanggal_retur', $blnPeriode)
                            // ->whereYear('tanggal_retur', $thnPeriode)
                            ->whereDate('tanggal_retur', $tglRetur)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglRetur)->format('ymd');

            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglRetur))));
            if ($counter < 10) {
                $kodeRetur = "rp-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeRetur = "rp-cv-".$kodeTgl.$counter;
            }

            $PurchaseReturn = new PurchaseReturn();
            $PurchaseReturn->kode_retur = $kodeRetur;
            $PurchaseReturn->nota_retur = $notaRetur;
            $PurchaseReturn->id_supplier = $idSupplier;
            $PurchaseReturn->id_retur = $idRetur;
            $PurchaseReturn->jumlah_total_retur = $qtyItem;
            $PurchaseReturn->nominal_retur = $total;
            $PurchaseReturn->tanggal_retur = $tglRetur;
            $PurchaseReturn->keterangan = $keterangan;
            $PurchaseReturn->status_retur = 'draft';
            $PurchaseReturn->flag_revisi = 0;
            $PurchaseReturn->id_ppn = $taxSettings->ppn_percentage_id;
            $PurchaseReturn->created_by = $user;
            $PurchaseReturn->save();

            $data = $PurchaseReturn;

            $setDetail = DB::table('purchase_return_detail')
                            ->where([
                                        ['id_retur', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_retur' => $PurchaseReturn->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Purchase Return',
                'action' => 'Simpan',
                'desc' => 'Simpan Purchase Return',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseReturn.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_po).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseReturn')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'tanggal_retur'=>'required',
        ]);

        $tglRetur = $request->input('tanggal_retur');

        $bulanIndonesia = Carbon::parse($tglRetur)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglRetur);
        if (!$aksesTransaksi) {
            return redirect('/PurchaseReturn')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $idSupplier = $request->input('supplier');
            $idRetur = $request->input('data_retur');
            $tglRetur = $request->input('tanggal_retur');
            $notaRetur = $request->input('nota_retur');
            $qtyItem = $request->input('qtyTtl');
            $total = $request->input('gt');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));

            $updateFile = $request->input('file_po_supplier');



            $PurchaseReturn = PurchaseReturn::find($id);
            $PurchaseReturn->nota_retur = $notaRetur;
            $PurchaseReturn->id_supplier = $idSupplier;
            $PurchaseReturn->id_retur = $idRetur;
            $PurchaseReturn->jumlah_total_retur = $qtyItem;
            $PurchaseReturn->nominal_retur = $qtyItem;
            $PurchaseReturn->tanggal_retur = $tglRetur;
            $PurchaseReturn->keterangan = $keterangan;
            $PurchaseReturn->updated_by = $user;
            $PurchaseReturn->save();

            // $deletedDetail = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                ['module', '=', 'purchase_return'],
                                ['value1', '=', $id],
                                ['action', '!=' , null]
                            ])
                            ->get();
            $data = $PurchaseReturn;

            //Legend
            // 'value1' => $detail->id_retur,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = PurchaseReturnDetail::find($detail->id_detail);
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->harga_retur = $detail->value6;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new PurchaseReturnDetail();
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->harga_retur = $detail->value6;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('purchase_return_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $log = ActionLog::create([
                'module' => 'Purchase Return',
                'action' => 'Update',
                'desc' => 'Update Purchase Return',
                'username' => Auth::user()->user_name
            ]);
        });
        if (is_null($exception)) {
            return redirect()->route('PurchaseReturn.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_retur).' Telah Diupdate!');
        }
        else {
            return redirect('/PurchaseReturn')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $salesReturn = PurchaseReturn::find($id);
            $data = $salesReturn;

            if ($btnAction == "posting") {

                $returItem = PurchaseReturnItem::find($salesReturn->id_retur);
                $returItem->flag_nota = 1;
                $returItem->save();

                $salesReturn->status_retur = "posted";
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'Purchase Return',
                    'action' => 'Posting',
                    'desc' => 'Posting Purchase Return',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($salesReturn->kode_retur).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "kirim") {
                $status = "kirim";
            }
            elseif ($btnAction == "revisi") {
                $returItem = PurchaseReturnItem::find($salesReturn->id_retur);
                $returItem->flag_nota = 0;
                $returItem->save();

                $salesReturn->status_retur = "draft";
                $salesReturn->flag_revisi = '1';
                $salesReturn->updated_by = Auth::user()->user_name;
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'Purchase Return',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Purchase Return',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Purchase Return '.strtoupper($salesReturn->kode_retur).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $salesReturn->status_retur = "batal";
                $salesReturn->updated_by = Auth::user()->user_name;
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Batal',
                    'desc' => 'Batal Purchase Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Purchase Order '.strtoupper($salesReturn->kode_retur).' Telah Dibatalkan!';
                $status = 'success';
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('PurchaseReturn.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function delete(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReturn');
            $user = Auth::user()->user_name;
            $delete = PurchaseReturn::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Purchase Return',
                'action' => 'Delete',
                'desc' => 'Delete Purchase Return',
                'username' => Auth::user()->user_name
            ]);
        });

        if(is_null($exception)) {
            return response()->json(['success'=> 'Data Berhasil Dihapus!']);
        }
        else {
            return response()->json(['error'=> $exception]);
        }

    }

    public function ResetPurchaseReturnDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idReturn');
            $mode = $request->input('mode');


            if ($id != "DRAFT") {
                // $detail = PurchaseReturnDetail::where([
                //                             ['id_retur', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                //Legend
                // 'value1' => $detail->id_retur,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_return'],
                                    ['value1', '=', $id]
                                ])->delete();
            }
            else {
                $delete = DB::table('purchase_return_detail')->where('id_retur', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getProductDetail(Request $request)
    {
        $id = $request->input('idProduct');

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.id',
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                )
                                ->where([
                                    ['product_detail.id_product', '=', $id]
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function SetPurchaseReturnDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idPurchaseReturn');
            $idRetur = $request->input('idReturn');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != "DRAFT") {
                $update = DB::table('temp_transaction')
                            ->where([
                                ['value1', '=', $id],
                                ['module', '=', "purchase_return"]
                            ])
                            ->update([
                                'action' => "hapus",
                                'deleted_by' => Auth::user()->user_name,
                                'deleted_at' => now()
                            ]);


                $detail = PurchaseReturnItemDetail::leftJoin('purchase_return_item', 'purchase_return_item_detail.id_retur', '=', 'purchase_return_item.id')
                                                ->select(
                                                    'purchase_return_item_detail.id_item',
                                                    'purchase_return_item_detail.id_satuan',
                                                    'purchase_return_item_detail.id_index',
                                                    'purchase_return_item_detail.qty_item',
                                                    'purchase_return_item.id_supplier'
                                                )
                                                ->where([
                                                    ['purchase_return_item_detail.id_retur', '=', $idRetur]
                                                ])
                                                ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailRetur) {

                    $hargaJualTerakhir = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                        ->select('id_item', 'id_satuan', DB::raw("harga_beli AS harga_beli_last"))
                                                        ->whereIn('purchase_order.tanggal_po', function($querySub) use ($detailRetur) {
                                                            $querySub->select(DB::raw("MAX(purchase_order.tanggal_po)"))->from("purchase_order")
                                                                    ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                                    ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                                    ->whereNotIn('purchase_order.status_po', ['draft', 'cancel'])
                                                                    ->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                                    ->where([
                                                                        ['purchase_order.id_supplier', '=', $detailRetur->id_supplier],
                                                                        ['purchase_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                                        ['purchase_order_detail.id_item', '=', $detailRetur->id_item]
                                                                    ]);
                                                        })
                                                        ->where([
                                                            ['purchase_order.id_supplier', '=', $detailRetur->id_supplier],
                                                            ['purchase_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                            ['purchase_order_detail.id_item', '=', $detailRetur->id_item]
                                                        ])
                                                        ->first();

                    $dataDetail = [
                        'module' => "purchase_return",
                        'value1' => $id,
                        'value2' => $detailRetur->id_item,
                        'value3' => $detailRetur->id_satuan,
                        'value4' => $detailRetur->id_index,
                        'value5' => $detailRetur->qty_item,
                        'value6' => $hargaJualTerakhir->harga_beli_last,
                        'action' => "tambah",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                TempTransaction::insert($listDetail);
            }
            else {
                $delete = DB::table('purchase_return_detail')
                            ->where('id_retur', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('purchase_return_detail.created_by', $user);
                            })
                            ->delete();

                $detail = PurchaseReturnItemDetail::leftJoin('purchase_return_item', 'purchase_return_item_detail.id_retur', '=', 'purchase_return_item.id')
                                                ->select(
                                                    'purchase_return_item_detail.id_item',
                                                    'purchase_return_item_detail.id_satuan',
                                                    'purchase_return_item_detail.id_index',
                                                    'purchase_return_item_detail.qty_item',
                                                    'purchase_return_item.id_supplier'
                                                )
                                                ->where([
                                                    ['purchase_return_item_detail.id_retur', '=', $idRetur]
                                                ])
                                                ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailRetur) {

                    $hargaJualTerakhir = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                        ->select('id_item', 'id_satuan', DB::raw("harga_beli AS harga_beli_last"))
                                                        ->whereIn('purchase_order.tanggal_po', function($querySub) use ($detailRetur) {
                                                            $querySub->select(DB::raw("MAX(purchase_order.tanggal_po)"))->from("purchase_order")
                                                                    ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                                    ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                                    ->whereNotIn('purchase_order.status_po', ['draft', 'cancel'])
                                                                    ->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                                    ->where([
                                                                        ['purchase_order.id_supplier', '=', $detailRetur->id_supplier],
                                                                        ['purchase_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                                        ['purchase_order_detail.id_item', '=', $detailRetur->id_item]
                                                                    ]);
                                                        })
                                                        ->where([
                                                            ['purchase_order.id_supplier', '=', $detailRetur->id_supplier],
                                                            ['purchase_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                            ['purchase_order_detail.id_item', '=', $detailRetur->id_item]
                                                        ])
                                                        ->first();

                    $dataDetail = [
                        'id_retur' => $id,
                        'id_item' => $detailRetur->id_item,
                        'id_satuan' => $detailRetur->id_satuan,
                        'id_index' => $detailRetur->id_index,
                        'qty_item' => $detailRetur->qty_item,
                        'harga_retur' => $hargaJualTerakhir->harga_beli_last,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                PurchaseReturnDetail::insert($listDetail);
            }


        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }
}
