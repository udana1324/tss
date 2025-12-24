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
use App\Models\Purchasing\PurchaseReturn;
use App\Models\Purchasing\PurchaseReturnItem;
use App\Models\Purchasing\PurchaseReturnItemDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Illuminate\Support\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class PurchaseReturnItemController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseReturnItem'],
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
                                                ['module.url', '=', '/PurchaseReturnItem'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = PurchaseReturnItem::distinct()->get('status_retur');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('purchase_return_item_detail')->where('deleted_at', '!=', null)->delete();
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

                return view('pages.purchasing.purchase_return_item.index', $data);
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

        $purchaseOrder = PurchaseReturnItem::leftJoin('supplier', 'purchase_return_item.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'purchase_return_item.id',
                                'purchase_return_item.kode_retur',
                                'purchase_return_item.no_dokumen_retur',
                                'purchase_return_item.jumlah_total_retur',
                                'purchase_return_item.tanggal_retur',
                                'purchase_return_item.flag_revisi',
                                'purchase_return_item.status_retur')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('purchase_return_item.tanggal_retur', Carbon::parse($periode)->format('m'));
                                $q->whereYear('purchase_return_item.tanggal_retur', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('purchase_return_item.id', 'desc')
                            ->get();
        return response()->json($purchaseOrder);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();

                $parentMenu = Module::find($hakAkses->parent);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

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
                $data['dataSupplier'] = $dataSupplier;
                $data['listIndex'] = $list;

                $log = ActionLog::create([
                    'module' => 'Purchase Retur',
                    'action' => 'Buat',
                    'desc' => 'Buat Purchase Retur',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('purchase_return_item_detail')
                            ->where([
                                ['id_retur', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.purchasing.purchase_return_item.add', $data);
            }
            else {
                return redirect('/PurchaseReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataRetur = PurchaseReturnItem::find($id);

                if ($dataRetur->status_retur != "draft") {
                    return redirect('/PurchaseReturnItem')->with('warning', 'Retur tidak dapat diubah karena status Retur bukan DRAFT!');
                }

                // $restore = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_return_item'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = PurchaseReturnItemDetail::where([
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
                            'module' => 'purchase_return_item',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_retur,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->id_index,
                            'value5' => $detail->qty_item,
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

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

                $data['listIndex'] = $list;

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Purchase Return Item',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Purchase Return Item',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_return_item.edit', $data);
            }
            else {
                return redirect('/PurchaseReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataRetur = PurchaseReturnItem::find($id);
                $dataSupplier = Supplier::find($dataRetur->id_supplier);

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataRetur->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Purchase Return Item',
                    'action' => 'Detil',
                    'desc' => 'Detil Purchase Return Item',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_return_item.detail', $data);
            }
            else {
                return redirect('/PurchaseReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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


    public function getProductSupplier(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataProduct = SupplierProduct::leftJoin('product', 'supplier_product.id_item', 'product.id')
                                        ->where([
                                            ['supplier_product.id_supplier', '=', $idSupplier],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->select('product.id', 'product.nama_item')
                                        ->whereIn('supplier_product.id_item', function($subQuery) use ($idSupplier) {
                                            $subQuery->select('receiving_detail.id_item')->from('receiving_detail')->distinct()
                                            ->leftJoin('receiving', 'receiving.id', '=', 'receiving_detail.id_penerimaan')
                                            ->whereIn('receiving.id', function($sub2) use ($idSupplier) {
                                                $sub2->select('receiving.id')->from('purchase_invoice_detail')
                                                ->leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'purchase_invoice_detail.id_invoice')
                                                ->where('purchase_invoice.status_invoice', 'posted')
                                                ->whereIn('purchase_invoice.id_po', function($sub3) use ($idSupplier) {
                                                    $sub3->select('purchase_order.id')->from('purchase_order')
                                                    ->where('purchase_order.id_supplier', $idSupplier);
                                                });
                                            });
                                        })
                                        ->orderBy('product.nama_item', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
        $idSupplier = $request->input('id_supplier');

        if ($idProduct != "" && $idSatuan != "") {

            $dataRetur = PurchaseReturnItemDetail::select('id_item', 'id_satuan', DB::raw('COALESCE(SUM(qty_item),0) AS returned_item'))
                                        ->leftJoin('purchase_return_item', 'purchase_return_item.id', '=', 'purchase_return_item_detail.id_retur')
                                        ->where([
                                                    ['purchase_return_item.id_supplier', '=', $idSupplier],
                                                    ['purchase_return_item_detail.id_item', '=', $idProduct],
                                                    ['purchase_return_item_detail.id_satuan', '=', $idSatuan],
                                                    ['purchase_return_item.status_retur', '=', 'posted']
                                                ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->first();

            $dataProduct = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS sold_item'))
                                        ->leftJoin('receiving', 'receiving.kode_penerimaan', '=', 'stock_transaction.kode_transaksi')
                                        ->leftJoin('purchase_order', 'purchase_order.id', 'receiving.id_po')
                                        ->where([
                                            ['stock_transaction.transaksi', '=', 'in'],
                                            ['stock_transaction.jenis_transaksi', '=', 'penerimaan'],
                                            ['purchase_order.id_supplier', '=', $idSupplier],
                                            ['stock_transaction.id_item', '=', $idProduct],
                                            ['stock_transaction.id_satuan', '=', $idSatuan],
                                        ])
                                        ->groupBy('stock_transaction.id_item')
                                        ->groupBy('stock_transaction.id_satuan')
                                        ->first();

            if ($dataRetur != null) {
                $dataProductRetur = $dataProduct->sold_item - $dataRetur->returned_item;
            }
            else {
                $dataProductRetur = $dataProduct->sold_item;
            }

        }
        else {
            $dataProductRetur = "";
        }

        return response()->json($dataProductRetur);
    }

    public function getDefaultAddress(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $defaultAddress = SupplierDetail::where([
                                            ['id_supplier', '=', $idSupplier],
                                            ['default', '=', 'Y']
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function RestorePurchaseOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idPo');
            $restore = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getSupplierAddress(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $supplierAddress = SupplierDetail::where([
                                            ['id_supplier', '=', $idSupplier]
                                        ])
                                        ->get();

        return response()->json($supplierAddress);
    }

    public function getProduct(Request $request)
    {
        $idSupplier = $request->input('id_supplier');
        $dataProduct = "";

        if ($idSupplier != "") {
            $dataProduct = Product::leftJoin('product_brand', 'product.merk_item', 'product_brand.id')
                                    ->leftJoin('product_category', 'product.kategori_item', 'product_category.id')
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
                                    )
                                    ->whereNOTIn('product.id', function($query) use ($idSupplier) {
                                        $query->select('id_item')->from('supplier_product')
                                            ->where('id_supplier', $idSupplier);
                                    })
                                    ->get();

        }

        return response()->json($dataProduct);
    }

    public function getProductHistory(Request $request)
    {
        $idSupplier = $request->input('id_supplier');
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
        $idIndex = $request->input('id_index');
        $dataProduct = "";

        if ($idSupplier != "" && $idProduct != "") {
            $dataProduct = Receiving::leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', 'receiving.id')
                                    ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('purchase_order_detail', function($join) {
                                        $join->on('purchase_order.id' , '=', 'purchase_order_detail.id_po');
                                        $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                        $join->on('receiving_detail.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                    })
                                    ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftjoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', '=', 'product_unit.id')
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_unit.nama_satuan',
                                        'supplier.nama_supplier',
                                        'purchase_order.no_po',
                                        'purchase_order_detail.harga_beli',
                                        'receiving.tanggal_sj',
                                        'receiving.kode_penerimaan',
                                        'receiving_detail.qty_item',
                                        'purchase_invoice.kode_invoice',
                                    )
                                    ->where([
                                        ['supplier.id', '=', $idSupplier],
                                        ['product.id', '=', $idProduct],
                                        ['product_unit.id', '=', $idSatuan]
                                    ])
                                    ->orderBy('purchase_order.tanggal_po', 'desc')
                                    ->groupBy('receiving.id_po')
                                    ->get();
        }

        return response()->json($dataProduct);
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

    public function addSupplierProduct(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idSupplier = $request->input('id_supplier');
            $idItem = $request->input('id_item');

            $supplierProduct = new SupplierProduct();
            $supplierProduct->id_supplier = $idSupplier;
            $supplierProduct->id_item = $idItem;
            $supplierProduct->created_by = Auth::user()->user_name;
            $supplierProduct->save();
            $data = $supplierProduct;

        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function getPreviousOrder(Request $request)
    {
        $idSupplier = $request->input('id_supplier');
        $dataPurchaseOrder = "";

        if ($idSupplier != "") {
            $dataPurchaseOrder = PurchaseOrder::where([
                                                    ['purchase_order.id_supplier', '=', $idSupplier]
                                                ])
                                                ->whereNotIn('purchase_order.status_po', ['draft', 'batal'])
                                                ->orderBy('purchase_order.tanggal_po', 'desc')
                                                ->first();

        }

        return response()->json($dataPurchaseOrder);
    }

    public function StorePurchaseReturnItemDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReturn');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndex = $request->input('idIndex');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';
                $countItem = DB::table('purchase_return_item_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_retur', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new PurchaseReturnItemDetail();
                    $listItem->id_retur = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->id_index = $idIndex;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Return Item Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Return Item Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {
                //Legend
                // 'value1' => $detail->id_retur,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'purchase_return_item'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idItem],
                                    ['value3', '=', $idSatuan],
                                    ['value4', '=', $idIndex]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'purchase_return_item';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $idIndex;
                    $listItem->value5 = $qty;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Return Item Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Return Item Detail',
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

    public function UpdatePurchaseReturnItemDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idReturn');
            $idDetail = $request->input('idDetail');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndex = $request->input('idIndex');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';
                $listItem = PurchaseReturnItemDetail::find($idDetail);
                $listItem->id_retur = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->id_index = $idIndex;
                $listItem->qty_item = $qty;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                //Legend
                // 'value1' => $detail->id_po,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,
                // 'value5' => $detail->qty_outstanding,
                // 'value6' => $detail->qty_order,
                // 'value7' => $detail->harga_beli
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $idIndex;
                $listItem->value5 = $qty;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'Purchase Order Detail',
                'action' => 'Update',
                'desc' => 'Update Purchase Order Detail',
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

    public function GetPurchaseReturnItemDetail(Request $request)
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

            $detail = PurchaseReturnItemDetail::leftJoin('product', 'purchase_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_return_item_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'purchase_return_item_detail.id',
                                            'purchase_return_item_detail.id_item',
                                            'purchase_return_item_detail.id_satuan',
                                            'purchase_return_item_detail.id_index',
                                            'purchase_return_item_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['purchase_return_item_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_return_item_detail.created_by', $user);
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
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
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
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_return_item']
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

    public function EditPurchaseReturnItemDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $idSupp = $request->input('idSupp');
        $mode = $request->input('mode');

        if ($mode == "") {
            $detail = PurchaseReturnItemDetail::leftJoin('product', 'purchase_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_return_item_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'purchase_return_item_detail.id',
                                            'purchase_return_item_detail.id_item',
                                            'purchase_return_item_detail.id_satuan',
                                            'purchase_return_item_detail.id_index',
                                            'purchase_return_item_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['purchase_return_item_detail.id', '=', $id]
                                        ])
                                        ->first();
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
                                            'temp_transaction.id_detail',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_return_item']
                                        ])
                                        ->first();

        }

        $dataRetur = PurchaseReturnItemDetail::select('id_item', 'id_satuan', DB::raw('COALESCE(SUM(qty_item),0) AS returned_item'))
                                        ->leftJoin('purchase_return_item', 'purchase_return_item.id', '=', 'purchase_return_item_detail.id_retur')
                                        ->when($mode == "", function($q) use ($idSupp, $detail) {
                                            $q->where([
                                                ['purchase_return_item.id_supplier', '=', $idSupp],
                                                ['purchase_return_item_detail.id_item', '=', $detail->id_item],
                                                ['purchase_return_item_detail.id_satuan', '=', $detail->id_satuan],
                                                ['purchase_return_item.status_retur', '=', 'posted']
                                            ]);
                                        })
                                        ->when($mode == "edit", function($q) use ($idSupp, $detail) {
                                            $q->where([
                                                ['purchase_return_item.id_supplier', '=', $idSupp],
                                                ['purchase_return_item_detail.id_item', '=', $detail->value2],
                                                ['purchase_return_item_detail.id_satuan', '=', $detail->value3],
                                                ['purchase_return_item.status_retur', '=', 'posted']
                                            ]);
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->first();

        $dataProduct = StockTransaction::leftJoin('receiving', 'receiving.kode_penerimaan', '=', 'stock_transaction.kode_transaksi')
                                        ->leftJoin('purchase_order', 'purchase_order.id', 'receiving.id_po')
                                        ->select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS sold_item'))
                                        ->when($mode == "", function($q) use ($idSupp, $detail) {
                                            $q->where([
                                                ['stock_transaction.transaksi', '=', 'in'],
                                                ['stock_transaction.jenis_transaksi', '=', 'penerimaan'],
                                                ['purchase_order.id_supplier', '=', $idSupp],
                                                ['stock_transaction.id_item', '=', $detail->id_item],
                                                ['stock_transaction.id_satuan', '=', $detail->id_satuan],
                                            ]);
                                        })
                                        ->when($mode == "edit", function($q) use ($idSupp, $detail) {
                                            $q->where([
                                                ['stock_transaction.transaksi', '=', 'in'],
                                                ['stock_transaction.jenis_transaksi', '=', 'penerimaan'],
                                                ['purchase_order.id_supplier', '=', $idSupp],
                                                ['stock_transaction.id_item', '=', $detail->value2],
                                                ['stock_transaction.id_satuan', '=', $detail->value3],
                                            ]);
                                        })
                                        ->groupBy('stock_transaction.id_item')
                                        ->groupBy('stock_transaction.id_satuan')
                                        ->first();

        if ($dataRetur != null) {
            $dataProductRetur = $dataProduct->sold_item - $dataRetur->returned_item;
        }
        else {
            $dataProductRetur = $dataProduct->sold_item;
        }

        $detail->limit_retur = $dataProductRetur;

        return response()->json($detail);
    }

    public function DeletePurchaseReturnItemDetail(Request $request)
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
                $delete = DB::table('purchase_return_item_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetPurchaseReturnItemFooter(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = PurchaseReturnItemDetail::leftJoin('product', 'purchase_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_return_item_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(purchase_return_item_detail.qty_item) AS qtyItem'),
                                        )
                                        ->where([
                                            ['purchase_return_item_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_return_item_detail.created_by', $user);
                                        })
                                        ->groupBy('purchase_return_item_detail.id_retur')
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
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_return_item']
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
            return redirect('/PurchaseReturnItem')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $noDokumen = $request->input('nmr_sj_retur');
            $tglRetur = $request->input('tanggal_retur');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));
            $tahunPeriode = date("y", strtotime($tglRetur));

            $countKode = DB::table('purchase_return_item')
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
                $kodeRetur = "rpi-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeRetur = "rpi-cv-".$kodeTgl.$counter;
            }

            $PurchaseReturnItem = new PurchaseReturnItem();
            $PurchaseReturnItem->kode_retur = $kodeRetur;
            $PurchaseReturnItem->no_dokumen_retur = $noDokumen;
            $PurchaseReturnItem->id_supplier = $idSupplier;
            $PurchaseReturnItem->jumlah_total_retur = $qtyItem;
            $PurchaseReturnItem->tanggal_retur = $tglRetur;
            $PurchaseReturnItem->keterangan = $keterangan;
            $PurchaseReturnItem->status_retur = 'draft';
            $PurchaseReturnItem->flag_revisi = 0;
            $PurchaseReturnItem->id_ppn = $taxSettings->ppn_percentage_id;
            $PurchaseReturnItem->created_by = $user;
            $PurchaseReturnItem->save();

            $data = $PurchaseReturnItem;

            $setDetail = DB::table('purchase_return_item_detail')
                            ->where([
                                        ['id_retur', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_retur' => $PurchaseReturnItem->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Purchase Return Item',
                'action' => 'Simpan',
                'desc' => 'Simpan Purchase Return Item',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseReturnItem.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_po).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseReturnItem')->with('error', $exception);
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
            return redirect('/PurchaseReturnItem')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $noDokumen = $request->input('nmr_sj_retur');
            $tglRetur = $request->input('tanggal_retur');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));

            $updateFile = $request->input('file_po_supplier');



            $PurchaseReturnItem = PurchaseReturnItem::find($id);
            $PurchaseReturnItem->no_dokumen_retur = $noDokumen;
            $PurchaseReturnItem->id_supplier = $idSupplier;
            $PurchaseReturnItem->jumlah_total_retur = $qtyItem;
            $PurchaseReturnItem->tanggal_retur = $tglRetur;
            $PurchaseReturnItem->keterangan = $keterangan;
            $PurchaseReturnItem->updated_by = $user;
            $PurchaseReturnItem->id_ppn = $taxSettings->ppn_percentage_id;
            $PurchaseReturnItem->save();

            // $deletedDetail = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                ['module', '=', 'purchase_return_item'],
                                ['value1', '=', $id],
                                ['action', '!=' , null]
                            ])
                            ->get();
            $data = $PurchaseReturnItem;

            //Legend
            // 'value1' => $detail->id_retur,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = PurchaseReturnItemDetail::find($detail->id_detail);
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new PurchaseReturnItemDetail();
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('purchase_return_item_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $log = ActionLog::create([
                'module' => 'Purchase Return Item',
                'action' => 'Update',
                'desc' => 'Update Purchase Return Item',
                'username' => Auth::user()->user_name
            ]);
        });
        if (is_null($exception)) {
            return redirect()->route('PurchaseReturnItem.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_retur).' Telah Diupdate!');
        }
        else {
            return redirect('/PurchaseReturnItem')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $dp = $request->input('dp');
            $purchaseReturn = PurchaseReturnItem::find($id);
            $data = $purchaseReturn;

            $cekRetur = PurchaseReturn::where([
                                            ['purchase_return.id_retur', '=', $id],
                                            ['purchase_return.status_retur', '!=', 'draft']
                                        ])
                                        ->count();

            if ($btnAction == "posting") {
                $detailRetur = PurchaseReturnItemDetail::leftJoin('product', 'purchase_return_item_detail.id_item', '=', 'product.id')
                                                    ->leftJoin('product_unit', 'purchase_return_item_detail.id_satuan', 'product_unit.id')
                                                    ->select(
                                                        'purchase_return_item_detail.id',
                                                        'purchase_return_item_detail.id_item',
                                                        'purchase_return_item_detail.id_satuan',
                                                        'purchase_return_item_detail.id_index',
                                                        'purchase_return_item_detail.qty_item',
                                                        'product.kode_item',
                                                        'product.nama_item',
                                                        'product_unit.nama_satuan'
                                                    )
                                                    ->where([
                                                        ['purchase_return_item_detail.id_retur', '=', $id]
                                                    ])
                                                    ->get();

                $transaksi = [];
                foreach ($detailRetur As $detail) {
                    $dataDetail = [
                        'kode_transaksi' => $purchaseReturn->kode_retur,
                        'id_item' => $detail->id_item,
                        'id_satuan' => $detail->id_satuan,
                        'id_index' => $detail->id_index,
                        'qty_item' => $detail->qty_item,
                        'tgl_transaksi' => $purchaseReturn->tanggal_retur,
                        'jenis_transaksi' => "retur_pembelian",
                        'jenis_sumber' => 6,
                        'transaksi' => "out",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transaksi, $dataDetail);
                }
                StockTransaction::insert($transaksi);

                $purchaseReturn->status_retur = "posted";
                $purchaseReturn->save();

                $log = ActionLog::create([
                    'module' => 'purchase Return Item',
                    'action' => 'Posting',
                    'desc' => 'Posting purchase Return Item',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($purchaseReturn->kode_retur).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "nota") {
                $status = "nota";
            }
            elseif ($btnAction == "revisi") {
                if ($cekRetur == 0) {
                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $purchaseReturn->kode_retur)->delete();
                    $purchaseReturn->status_retur = "draft";
                    $purchaseReturn->flag_revisi = '1';
                    $purchaseReturn->updated_by = Auth::user()->user_name;
                    $purchaseReturn->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Return Item',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Purchase Return Item',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Purchase Return Item '.strtoupper($purchaseReturn->kode_retur).' Telah Direvisi!';
                    $status = 'success';
                }
                else {
                    $msg = 'Pembuatan Nota Retur '.strtoupper($purchaseReturn->kode_retur).' Tidak dapat Direvisi karena Retur: '.strtoupper($purchaseReturn->kode_retur).' Telah diproses !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "batal") {
                $purchaseReturn->status_retur = "batal";
                $purchaseReturn->updated_by = Auth::user()->user_name;
                $purchaseReturn->save();

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Batal',
                    'desc' => 'Batal Purchase Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Purchase Order '.strtoupper($purchaseReturn->kode_retur).' Telah Dibatalkan!';
                $status = 'success';
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('PurchaseReturnItem.edit', [$id]);
            }
            elseif ($status == "nota") {
                Session::put('id_retur', $id);
                Session::put('id_supp', $data->id_supplier);
                Session::save();

                return redirect('PurchaseReturn/Add');
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
            $delete = PurchaseReturnItem::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Purchase Return Item',
                'action' => 'Delete',
                'desc' => 'Delete Purchase Return Item',
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

    public function ResetPurchaseReturnItemDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idReturn');
            $mode = $request->input('mode');


            if ($id != "DRAFT") {
                // $detail = PurchaseReturnItemDetail::where([
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
                                    ['module', '=', 'purchase_return_item'],
                                    ['value1', '=', $id]
                                ])->delete();
            }
            else {
                $delete = DB::table('purchase_return_item_detail')->where('id_retur', '=', $id)->delete();
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
}
