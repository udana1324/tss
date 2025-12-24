<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
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
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductSpecification;
use App\Models\Setting\Module;
use App\Models\Setting\Preference;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
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
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = PurchaseOrder::distinct()->get('status_po');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;

                $delete = DB::table('purchase_order_detail')->where('deleted_at', '!=', null)->delete();

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_order.index', $data);
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

        $purchaseOrder = PurchaseOrder::leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'purchase_order.id',
                                'purchase_order.no_po',
                                'purchase_order.nominal_dp',
                                'purchase_order.jumlah_total_po',
                                'purchase_order.outstanding_po',
                                'purchase_order.tanggal_po',
                                'purchase_order.tanggal_request',
                                'purchase_order.tanggal_deadline',
                                'purchase_order.nominal_po_ttl',
                                'purchase_order.flag_revisi',
                                'purchase_order.metode_pembayaran',
                                'purchase_order.durasi_jt',
                                'purchase_order.status_po')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('purchase_order.tanggal_po', Carbon::parse($periode)->format('m'));
                                $q->whereYear('purchase_order.tanggal_po', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('purchase_order.id', 'desc')
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
                                            ['module.url', '=', '/PurchaseOrder'],
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
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['taxSettings'] = $taxSettings;

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Buat',
                    'desc' => 'Buat Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('purchase_order_detail')
                            ->where([
                                ['id_po', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.purchasing.purchase_order.add', $data);
            }
            else {
                return redirect('/PurchaseOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::orderBy('nama_supplier')->get();
                $dataPurchaseOrder = PurchaseOrder::find($id);
                if ($dataPurchaseOrder->status_po != "draft") {
                    return redirect('/PurchaseOrder')->with('warning', 'Purchase Order tidak dapat diubah karena status Pembelian bukan DRAFT!');
                }
                $dataTerms = PurchaseOrderTerms::where('id_po', $id)->get();

                // $restore = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_order'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = PurchaseOrderDetail::where([
                                                    ['id_po', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'purchase_order',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_po,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_order,
                            'value5' => $detail->outstanding_qty,
                            'value6' => $detail->harga_beli
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataPurchaseOrder'] = $dataPurchaseOrder;
                $data['dataTerms'] = $dataTerms;
                $data['taxSettings'] = $taxSettings;

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_order.edit', $data);
            }
            else {
                return redirect('/PurchaseOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataPurchaseOrder = PurchaseOrder::find($id);
                $dataTerms = PurchaseOrderTerms::where('id_po', $id)->get();
                $rcvCount = Receiving::where('id_po', $id)->count();

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataPurchaseOrder->id_ppn);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataPurchaseOrder'] = $dataPurchaseOrder;
                $data['dataTerms'] = $dataTerms;
                $data['taxSettings'] = $taxSettings;
                $data['rcvCount'] = $rcvCount;

                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Detail',
                    'desc' => 'Detail Purchase Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.purchase_order.detail', $data);
            }
            else {
                return redirect('/PurchaseOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ->where('flag_default', 'Y')
                                            ->first();

                $alamatKirim = Preference::leftJoin('company_account', 'preference.rekening', '=', 'company_account.id')
                                            ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                            ->select(
                                                'bank.kode_bank',
                                                'bank.nama_bank',
                                                'company_account.nomor_rekening',
                                                'company_account.cabang',
                                                'company_account.atas_nama',
                                                'preference.*'
                                            )
                                            ->where([
                                                ['preference.id', '=', $dataPurchaseOrder->id_alamat]
                                            ])
                                            ->first();

                $detailPurchaseOrder = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'purchase_order_detail.id',
                                                                'purchase_order_detail.id_item',
                                                                'purchase_order_detail.qty_order',
                                                                'purchase_order_detail.harga_beli',
                                                                DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_order) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product.jenis_item',
                                                                'product_unit.nama_satuan',
                                                            )
                                                            ->where([
                                                                ['purchase_order_detail.id_po', '=', $id]
                                                            ])
                                                            ->get();

                $dataDetails = array();

                foreach ($detailPurchaseOrder as $details) {
                    $spek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', '=', 'product_specification.id')
                                                        ->select('product_specification.flag_cetak','product_specification.nama_spesifikasi','product_detail_specification.value_spesifikasi')
                                                        ->where([
                                                            ['product_detail_specification.id_product', '=', $details->id_item]
                                                        ])
                                                        ->get();

                    $dataItem = [
                        'id_item' => $details->id_item,
                        'qty_order' => $details->qty_order,
                        'harga_beli' => $details->harga_beli,
                        'jenis_item' => $details->jenis_item,
                        'subtotal' => $details->subtotal,
                        'kode_item' => $details->kode_item,
                        'nama_item' => $details->nama_item,
                        'nama_satuan' => $details->nama_satuan,
                        'spesifikasi' => $spek,
                    ];
                    array_push($dataDetails, $dataItem);
                }
                //dd($dataDetails);

                $dataAlamat = SupplierDetail::where([
                                                ['id_supplier', '=', $dataPurchaseOrder->id_supplier],
                                                ['default', '=', 'Y']
                                            ])
                                            ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;


                $data['dataPurchaseOrder'] = $dataPurchaseOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['alamatKirim'] = $alamatKirim;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseOrder'] = $detailPurchaseOrder;
                $data['dataDetails'] = $dataDetails;


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

    public function RestorePurchaseOrderDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idPo');
            $restore = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {
            return response()->json("success");
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

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
        $idSupplier = $request->input('id_supplier');

        $hargaBeliTerakhir = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                ->select('id_item', 'id_satuan', DB::raw("harga_beli AS harga_beli_last"))
                                                ->whereIn('purchase_order.tanggal_po', function($querySub) use ($idProduct, $idSupplier, $idSatuan) {
                                                    $querySub->select(DB::raw("MAX(purchase_order.tanggal_po)"))->from("purchase_order")
                                                            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                            //->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                            ->whereNotIn('purchase_order.status_po', ['draft', 'cancel'])
                                                            //->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                            ->where([
                                                                ['purchase_order.id_supplier', '=', $idSupplier],
                                                                ['purchase_order_detail.id_item', '=', $idProduct],
                                                                ['purchase_order_detail.id_satuan', '=', $idSatuan]
                                                            ]);
                                                })
                                                ->where([
                                                    ['purchase_order.id_supplier', '=', $idSupplier],
                                                    ['purchase_order_detail.id_item', '=', $idProduct],
                                                    ['purchase_order_detail.id_satuan', '=', $idSatuan]
                                                ]);

        $stokIn = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in']
                                            ])
                                    ->groupBy('id_item');

        $stokOut = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out']
                                    ])
                                    ->groupBy('id_item');


        $dataProduct = Product::leftJoinSub($hargaBeliTerakhir, 'hargaBeliTerakhir', function($hargaBeliTerakhir) {
                                    $hargaBeliTerakhir->on('product.id', '=', 'hargaBeliTerakhir.id_item');
                                })
                                ->leftJoin('product_detail', function($join) {
                                    $join->on('product_detail.id_satuan', '=', 'hargaBeliTerakhir.id_satuan');
                                    $join->on('product_detail.id_product', '=', 'hargaBeliTerakhir.id_item');
                                })
                                ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                    $join_in->on('product.id', '=', 'stokIn.id_item');
                                })
                                ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                    $join_out->on('product.id', '=', 'stokOut.id_item');
                                })
                                ->leftJoin('product_unit', 'product_detail.id_satuan', 'product_unit.id')
                                ->select(
                                    DB::raw("COALESCE(product_detail.harga_beli,0) AS harga_beli"),
                                    DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                    DB::raw("COALESCE(hargaBeliTerakhir.harga_beli_last,0) AS harga_beli_last"),
                                    'product_unit.nama_satuan'
                                )
                                ->where([
                                    ['product.id', '=', $idProduct],
                                    ['hargaBeliTerakhir.id_satuan', '=', $idSatuan],
                                ])
                                ->get();

        return response()->json($dataProduct);
    }

    public function getDefaultAddress(Request $request)
    {
        $idAlamat = $request->input('id_alamat');

        if ($idAlamat == null) {
            $defaultAddress = Preference::where([
                                            ['flag_po', '=', 'Y']
                                        ])
                                        ->get();
        }
        else {
            $defaultAddress = Preference::where([
                                            ['id', '=', $idAlamat]
                                        ])
                                        ->get();
        }


        return response()->json($defaultAddress);
    }

    public function getSupplierAddress(Request $request)
    {
        $supplierAddress = Preference::get();

        return response()->json($supplierAddress);
    }

    public function getProduct(Request $request)
    {
        $idSupplier = $request->input('id_supplier');
        $dataProduct = "";

        if ($idSupplier != "") {

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $dataProduct = Product::leftJoin('product_brand', 'product.merk_item', 'product_brand.id')
                                    ->leftJoin('product_category', 'product.kategori_item', 'product_category.id')
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->whereNOTIn('product.id', function($query) use ($idSupplier) {
                                        $query->select('id_item')->from('supplier_product')
                                            ->where('id_supplier', $idSupplier);
                                    })
                                    ->get();

        }

        return response()->json($dataProduct);
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

    public function addSupllierProduct(Request $request)
    {
        $data = "";
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

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function StorePurchaseOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idPo');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyOrder');
            $hargaBeli = $request->input('hargaBeli');
            $user = Auth::user()->user_name;

            $hargaBeli = str_replace(",", ".", $hargaBeli);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('purchase_order_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_po', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new PurchaseOrderDetail();
                    $listItem->id_po = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_order = $qty;
                    $listItem->outstanding_qty = $qty;
                    $listItem->harga_beli = $hargaBeli;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Order Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'purchase_order'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idItem],
                                    ['value3', '=', $idSatuan],
                                    ['action', '!=', 'hapus'],
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'purchase_order';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->value5 = $qty;
                    $listItem->value6 = $hargaBeli;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Order Detail',
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

    public function UpdatePurchaseOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idPo');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyOrder');
            $hargaBeli = $request->input('hargaBeli');
            $user = Auth::user()->user_name;

            $hargaBeli = str_replace(",", ".", $hargaBeli);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = PurchaseOrderDetail::find($idDetail);
                $listItem->id_po = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_order = $qty;
                $listItem->outstanding_qty = $qty;
                $listItem->harga_beli = $hargaBeli;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->value5 = $qty;
                $listItem->value6 = $hargaBeli;
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

            $data = "success";
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetPurchaseOrderDetail(Request $request)
    {
        $id = $request->input('idPurchaseOrder');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != 'edit') {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'purchase_order_detail.id',
                                                'purchase_order_detail.id_item',
                                                'purchase_order_detail.qty_order',
                                                'purchase_order_detail.outstanding_qty',
                                                'purchase_order_detail.harga_beli',
                                                DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_order) AS subtotal'),
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['purchase_order_detail.id_po', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('purchase_order_detail.created_by', $user);
                                            })
                                            ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            DB::raw('COALESCE(temp_transaction.value6,0) * COALESCE(temp_transaction.value4) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_order']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditPurchaseOrderDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'purchase_order_detail.id',
                                                'purchase_order_detail.id_item',
                                                'purchase_order_detail.id_satuan',
                                                'purchase_order_detail.qty_order',
                                                'purchase_order_detail.outstanding_qty',
                                                'purchase_order_detail.harga_beli',
                                                DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_order) AS subtotal'),
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'product_unit.kode_satuan'
                                            )
                                            ->where([
                                                ['purchase_order_detail.id', '=', $id]
                                            ])
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
                                            'temp_transaction.value6',
                                            DB::raw('COALESCE(temp_transaction.value6,0) * COALESCE(temp_transaction.value4) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_order']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeletePurchaseOrderDetail(Request $request)
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
                $delete = DB::table('purchase_order_detail')->where('id', '=', $id)->delete();
            }


        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function GetPurchaseOrderFooter(Request $request)
    {
        $id = $request->input('idPo');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(purchase_order_detail.qty_order) AS qtyOrder'),
                                            DB::raw('SUM(COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(purchase_order_detail.qty_order,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['purchase_order_detail.id_po', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_order_detail.created_by', $user);
                                        })
                                        ->groupBy('purchase_order_detail.id_po')
                                        ->first();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(temp_transaction.value4) AS qtyOrder'),
                                            DB::raw('SUM(COALESCE(temp_transaction.value6,0) * COALESCE(temp_transaction.value4,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_order']
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
            'id_alamat'=>'required',
            'tanggal_po'=>'required',
            'tanggal_req'=>'required',
            'tanggal_deadline'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tglPo = $request->input('tanggal_po');
        $flagPpn = $request->input('status_ppn');

        $bulanIndonesia = Carbon::parse($tglPo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglPo);
        if (!$aksesTransaksi) {
            return redirect('/PurchaseOrder')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglPo);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/PurchaseOrder')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tglPo = $request->input('tanggal_po');
            $tglReq = $request->input('tanggal_req');
            $tglDeadline = $request->input('tanggal_deadline');
            $metodeBayar = $request->input('metode_bayar');
            $flagPPn = $request->input('status_ppn');
            $jenisDiskon = $request->input('jenis_diskon');
            $persenDiskon = $request->input('disc_percent');
            $nominalDiskon = $request->input('disc_nominal');
            $dpp =  $request->input('dpp');
            $ppn = $request->input('ppn');
            $grandTotal = $request->input('gt');
            $durasiJt = $request->input('durasi_jt');
            $qtyOrder = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyOrder = str_replace(",", ".", $qtyOrder);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $grandTotal = str_replace(",", ".", $grandTotal);

            $blnPeriode = date("m", strtotime($tglPo));
            $thnPeriode = date("Y", strtotime($tglPo));
            $tahunPeriode = date("y", strtotime($tglPo));

            $countKode = DB::table('purchase_order')
                            ->select(DB::raw("MAX(RIGHT(no_po,2)) AS angka"))
                            // ->whereMonth('tanggal_po', $blnPeriode)
                            // ->whereYear('tanggal_po', $thnPeriode)
                            ->whereDate('tanggal_po', $tglPo)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglPo)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglPo))));

            if ($counter < 10) {
                $nmrPo = "po-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrPo = "po-cv-".$kodeTgl.$counter;
            }

            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder->no_po = $nmrPo;
            $purchaseOrder->id_supplier = $idSupplier;
            $purchaseOrder->id_alamat = $idAlamat;
            $purchaseOrder->jumlah_total_po = $qtyOrder;
            $purchaseOrder->outstanding_po = $qtyOrder;
            $purchaseOrder->tanggal_po = $tglPo;
            $purchaseOrder->tanggal_request = $tglReq;
            $purchaseOrder->tanggal_deadline = $tglDeadline;
            $purchaseOrder->flag_ppn = $flagPPn;
            $purchaseOrder->nominal_po_dpp = $dpp;
            $purchaseOrder->nominal_po_ppn = $ppn;
            $purchaseOrder->nominal_po_ttl = $grandTotal;
            $purchaseOrder->jenis_diskon = $jenisDiskon;
            $purchaseOrder->nominal_diskon = $nominalDiskon;
            $purchaseOrder->persentase_diskon = $persenDiskon;
            $purchaseOrder->metode_pembayaran = $metodeBayar;
            $purchaseOrder->durasi_jt = $durasiJt;
            $purchaseOrder->status_po = 'draft';
            $purchaseOrder->id_ppn = $taxSettings->ppn_percentage_id;
            $purchaseOrder->created_by = $user;
            $purchaseOrder->save();

            $data = $purchaseOrder;

            $setDetail = DB::table('purchase_order_detail')
                            ->where([
                                        ['id_po', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_po' => $purchaseOrder->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_po' => $purchaseOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                PurchaseOrderTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Purchase Order',
                'action' => 'Simpan',
                'desc' => 'Simpan Purchase Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseOrder.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_po).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseOrder')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'id_alamat'=>'required',
            'tanggal_po'=>'required',
            'tanggal_req'=>'required',
            'tanggal_deadline'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tglPo = $request->input('tanggal_po');
        $flagPpn = $request->input('status_ppn');

        $bulanIndonesia = Carbon::parse($tglPo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglPo);
        if (!$aksesTransaksi) {
            return redirect()->route('PurchaseOrder.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglPo);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/PurchaseOrder')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tglPo = $request->input('tanggal_po');
            $tglReq = $request->input('tanggal_req');
            $tglDeadline = $request->input('tanggal_deadline');
            $metodeBayar = $request->input('metode_bayar');
            $persenDiskon = $request->input('persen_diskon');
            $flagPPn = $request->input('status_ppn');
            $dpp =  $request->input('dpp');
            $ppn = $request->input('ppn');
            $grandTotal = $request->input('gt');
            $jenisDiskon = $request->input('jenis_diskon');
            $persenDiskon = $request->input('disc_percent');
            $nominalDiskon = $request->input('disc_nominal');
            $durasiJt = $request->input('durasi_jt');
            $qtyOrder = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyOrder = str_replace(",", ".", $qtyOrder);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $grandTotal = str_replace(",", ".", $grandTotal);

            $blnPeriode = date("m", strtotime($tglPo));
            $thnPeriode = date("Y", strtotime($tglPo));

            $countKode = DB::table('purchase_order')
                            ->select(DB::raw("MAX(RIGHT(no_po,2)) AS angka"))
                            // ->whereMonth('tanggal_po', $blnPeriode)
                            // ->whereYear('tanggal_po', $thnPeriode)
                            ->whereDate('tanggal_po', $tglPo)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglPo)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglPo))));

            if ($counter < 10) {
                $nmrPo = "po-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrPo = "po-cv-".$kodeTgl.$counter;
            }

            $purchaseOrder = PurchaseOrder::find($id);
            if ($tglPo != $purchaseOrder->tanggal_po) {
                $purchaseOrder->no_po = $nmrPo;
            }
            $purchaseOrder->id_supplier = $idSupplier;
            $purchaseOrder->id_alamat = $idAlamat;
            $purchaseOrder->jumlah_total_po = $qtyOrder;
            $purchaseOrder->outstanding_po = $qtyOrder;
            $purchaseOrder->tanggal_po = $tglPo;
            $purchaseOrder->tanggal_request = $tglReq;
            $purchaseOrder->tanggal_deadline = $tglDeadline;
            $purchaseOrder->flag_ppn = $flagPPn;
            $purchaseOrder->nominal_po_dpp = $dpp;
            $purchaseOrder->nominal_po_ppn = $ppn;
            $purchaseOrder->nominal_po_ttl = $grandTotal;
            $purchaseOrder->jenis_diskon = $jenisDiskon;
            $purchaseOrder->nominal_diskon = $nominalDiskon;
            $purchaseOrder->persentase_diskon = $persenDiskon;
            $purchaseOrder->metode_pembayaran = $metodeBayar;
            $purchaseOrder->durasi_jt = $durasiJt;
            $purchaseOrder->id_ppn = $taxSettings->ppn_percentage_id;
            $purchaseOrder->updated_by = $user;
            $purchaseOrder->save();

            $data = $purchaseOrder;

            // $deletedDetail = PurchaseOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'purchase_order'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = PurchaseOrderDetail::find($detail->id_detail);
                        $listItem->id_po = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_order = $detail->value4;
                        $listItem->outstanding_qty = $detail->value5;
                        $listItem->harga_beli = $detail->value6;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new PurchaseOrderDetail();
                        $listItem->id_po = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_order = $detail->value4;
                        $listItem->outstanding_qty = $detail->value5;
                        $listItem->harga_beli = $detail->value6;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('purchase_order_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_order'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($terms != "") {
                $delete = DB::table('purchase_order_terms')->where('id_po', '=', $purchaseOrder->id)->delete();
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_po' => $purchaseOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                PurchaseOrderTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Purchase Order',
                'action' => 'Update',
                'desc' => 'Update Purchase Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseOrder.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_po).' Telah Diupdate!');
        }
        else {
            return redirect('/PurchaseOrder')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id) {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $dp = $request->input('dp');
            $purchaseOrder = PurchaseOrder::find($id);

            $cekSjPosted = Receiving::where([
                ['id_po', '=', $id],
                ['status_penerimaan', '!=', 'draft']
            ])
            ->count();
            if ($btnAction == "posting") {
                $purchaseOrder->nominal_dp = $dp;
                $purchaseOrder->sisa_dp = $dp;
                $purchaseOrder->status_po = "posted";
                $purchaseOrder->save();
                $log = ActionLog::create([
                    'module' => 'Purchase Order',
                    'action' => 'Posting',
                    'desc' => 'Posting Purchase Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Telah Diposting!';
                $status = "success";
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($cekSjPosted == 0) {
                    $purchaseOrder->status_po = "draft";
                    $purchaseOrder->flag_revisi = '1';
                    $purchaseOrder->updated_by = Auth::user()->user_name;
                    $purchaseOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Order',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Purchase Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Telah Direvisi!';
                    $status = "success";
                }
                else {
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Tidak dapat Direvisi karena terdapat Surat Jalan Penerimaan atas Purchase Order '.strtoupper($purchaseOrder->no_po).' !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "tutup") {
                if ($cekSjPosted != 0) {
                    $purchaseOrder->status_po = "close";
                    $purchaseOrder->updated_by = Auth::user()->user_name;
                    $purchaseOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Order',
                        'action' => 'Tutup',
                        'desc' => 'Tutup Purchase Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Telah Ditutup!';
                    $status = "success";
                }
                else {
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Tidak dapat Ditutup karena belum terdapat Surat Jalan Penerimaan atas Purchase Order '.strtoupper($purchaseOrder->no_po).' !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "batal") {
                if ($cekSjPosted == 0) {
                    $purchaseOrder->status_po = "batal";
                    $purchaseOrder->updated_by = Auth::user()->user_name;
                    $purchaseOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Order',
                        'action' => 'Batal',
                        'desc' => 'Batal Purchase Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Telah Dibatalkan!';
                    $status = "success";
                }
                else {
                    $msg = 'Purchase Order '.strtoupper($purchaseOrder->no_po).' Tidak dapat Dibatalkan karena terdapat Surat Jalan Penerimaan atas Purchase Order '.strtoupper($purchaseOrder->no_po).' !';
                    $status = "success";
                }
            }
            $data = $purchaseOrder;
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('PurchaseOrder.edit', [$id]);
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
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idPurchaseOrder');
            $user = Auth::user()->user_name;
            $delete = PurchaseOrder::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Purchase Order',
                'action' => 'Delete',
                'desc' => 'Delete Purchase Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json(['success'=>'Data Berhasil Dihapus!']);
        }
        else {
            return response()->json(['error'=> $exception]);
        }
    }

    public function ResetPurchaseOrderDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idPO');


            if ($id != "DRAFT") {
                // $detail = PurchaseOrderDetail::where([
                //                             ['id_po', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);
                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'purchase_order'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('purchase_order_detail')->where('id_po', '=', $id)->delete();
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

    public function getProductHistory(Request $request)
    {
        $idProduct = $request->input('id_product');
        $dataProduct = "";

        if ($idProduct != "") {
            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $dataProduct = Receiving::leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', 'receiving.id')
                                    ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('purchase_order_detail', function($join) {
                                        $join->on('purchase_order.id' , '=', 'purchase_order_detail.id_po');
                                        $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                    })
                                    ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftjoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoin('supplier_detail', 'supplier_detail.id', '=', 'purchase_order.id_alamat')
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
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
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product.id', '=', $idProduct]
                                    ])
                                    ->orderBy('purchase_order.tanggal_po', 'desc')
                                    ->groupBy('receiving.id_po')
                                    ->get();
        }

        return response()->json($dataProduct);
    }
}
