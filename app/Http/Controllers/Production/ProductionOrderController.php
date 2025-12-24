<?php

namespace App\Http\Controllers\Production;

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
use App\Models\Production\ProductionOrder;
use App\Models\Production\ProductionOrderDetail;
use App\Models\Production\ProductionOrderTerms;
use App\Models\Production\Receiving;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperProductionOrder;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductSpecification;
use App\Models\Production\ProductionReceiving;
use App\Models\Setting\Module;
use App\Models\Setting\Preference;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class ProductionOrderController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductionOrder'],
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
                                                ['module.url', '=', '/ProductionOrder'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = ProductionOrder::distinct()->get('status');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;

                $delete = DB::table('production_order_detail')->where('deleted_at', '!=', null)->delete();

                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Production Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_order.index', $data);
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

        $productionOrder = ProductionOrder::leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'production_order.id',
                                'production_order.no_production_order',
                                'production_order.po_production',
                                'production_order.nominal_dp',
                                'production_order.jumlah_total',
                                'production_order.outstanding_qty',
                                'production_order.tanggal',
                                'production_order.tanggal_request',
                                'production_order.tanggal_deadline',
                                'production_order.flag_revisi',
                                'production_order.flag_internal',
                                'production_order.status')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('production_order.tanggal', Carbon::parse($periode)->format('m'));
                                $q->whereYear('production_order.tanggal', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('production_order.id', 'desc')
                            ->get();
        return response()->json($productionOrder);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductionOrder'],
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
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['taxSettings'] = $taxSettings;

                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Buat',
                    'desc' => 'Buat Production Order',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('production_order_detail')
                            ->where([
                                ['id_po', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.production.production_order.add', $data);
            }
            else {
                return redirect('/ProductionOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::orderBy('nama_supplier')->get();
                $dataProductionOrder = ProductionOrder::find($id);
                if ($dataProductionOrder->status != "draft") {
                    return redirect('/ProductionOrder')->with('warning', 'Perintah Produksi tidak dapat diubah karena status bukan DRAFT!');
                }
                $dataTerms = ProductionOrderTerms::where('id_po', $id)->get();

                // $restore = ProductionOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_order'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = ProductionOrderDetail::where([
                                                    ['id_po', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'production_order',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_po,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_order,
                            'value5' => $detail->outstanding_qty,
                            'value6' => $detail->harga
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
                $data['dataProductionOrder'] = $dataProductionOrder;
                $data['dataTerms'] = $dataTerms;
                $data['taxSettings'] = $taxSettings;

                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Production Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_order.edit', $data);
            }
            else {
                return redirect('/ProductionOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::all();
                $dataProductionOrder = ProductionOrder::find($id);
                $dataTerms = ProductionOrderTerms::where('id_po', $id)->get();
                // $rcvCount = Receiving::where('id_po', $id)->count();

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataProductionOrder->id_ppn);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataProductionOrder'] = $dataProductionOrder;
                $data['dataTerms'] = $dataTerms;
                $data['taxSettings'] = $taxSettings;
                $data['rcvCount'] = 0;

                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Detail',
                    'desc' => 'Detail Production Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.production.production_order.detail', $data);
            }
            else {
                return redirect('/ProductionOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ProductionOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataProductionOrder = ProductionOrder::leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                                                    ->select(
                                                        'supplier.kode_supplier',
                                                        'supplier.nama_supplier',
                                                        'supplier.npwp_supplier',
                                                        'supplier.telp_supplier',
                                                        'supplier.fax_supplier',
                                                        'supplier.email_supplier',
                                                        'supplier.kategori_supplier',
                                                        'production_order.*'
                                                    )
                                                    ->where([
                                                        ['production_order.id', '=', $id],
                                                    ])
                                                    ->first();
                $dataTerms = ProductionOrderTerms::where('id_po', $id)->get();
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
                                                ['preference.id', '=', $dataProductionOrder->id_alamat]
                                            ])
                                            ->first();

                $detailProductionOrder = ProductionOrderDetail::leftJoin('product', 'production_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'production_order_detail.id',
                                                                'production_order_detail.id_item',
                                                                'production_order_detail.qty_order',
                                                                'production_order_detail.harga',
                                                                DB::raw('COALESCE(production_order_detail.harga,0) * COALESCE(production_order_detail.qty_order) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product.jenis_item',
                                                                'product_unit.nama_satuan',
                                                            )
                                                            ->where([
                                                                ['production_order_detail.id_po', '=', $id]
                                                            ])
                                                            ->get();

                $dataDetails = array();

                foreach ($detailProductionOrder as $details) {
                    $spek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', '=', 'product_specification.id')
                                                        ->select('product_specification.flag_cetak','product_specification.nama_spesifikasi','product_detail_specification.value_spesifikasi')
                                                        ->where([
                                                            ['product_detail_specification.id_product', '=', $details->id_item]
                                                        ])
                                                        ->get();

                    $dataItem = [
                        'id_item' => $details->id_item,
                        'qty_order' => $details->qty_order,
                        'harga' => $details->harga,
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
                                                ['id_supplier', '=', $dataProductionOrder->id_supplier],
                                                ['default', '=', 'Y']
                                            ])
                                            ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;


                $data['dataProductionOrder'] = $dataProductionOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['alamatKirim'] = $alamatKirim;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailProductionOrder'] = $detailProductionOrder;
                $data['dataDetails'] = $dataDetails;


                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Production Order',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperProductionOrder::cetakPdfPO($data);

                $fpdf->Output('I', strtoupper($dataProductionOrder->no_production_order).".pdf");
                exit;
            }
            else {
                return redirect('/ProductionOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
            $restore = ProductionOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
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

        $hargaBeliTerakhir = ProductionOrderDetail::leftJoin('production_order', 'production_order_detail.id_po', '=', 'production_order.id')
                                                ->select('id_item', 'id_satuan', DB::raw("harga AS harga_last"))
                                                ->whereIn('production_order.tanggal', function($querySub) use ($idProduct, $idSupplier, $idSatuan) {
                                                    $querySub->select(DB::raw("MAX(production_order.tanggal)"))->from("production_order")
                                                            ->leftJoin('production_order_detail', 'production_order_detail.id_po', '=', 'production_order.id')
                                                            //->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'production_order.id')
                                                            ->whereNotIn('production_order.status', ['draft', 'cancel'])
                                                            //->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                            ->where([
                                                                ['production_order.id_supplier', '=', $idSupplier],
                                                                ['production_order_detail.id_item', '=', $idProduct],
                                                                ['production_order_detail.id_satuan', '=', $idSatuan]
                                                            ]);
                                                })
                                                ->where([
                                                    ['production_order.id_supplier', '=', $idSupplier],
                                                    ['production_order_detail.id_item', '=', $idProduct],
                                                    ['production_order_detail.id_satuan', '=', $idSatuan]
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
                                    DB::raw("COALESCE(product_detail.harga,0) AS harga"),
                                    DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                    DB::raw("COALESCE(hargaBeliTerakhir.harga_last,0) AS harga_last"),
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
        $dataProductionOrder = "";

        if ($idSupplier != "") {
            $dataProductionOrder = ProductionOrder::where([
                                                    ['production_order.id_supplier', '=', $idSupplier]
                                                ])
                                                ->whereNotIn('production_order.status', ['draft', 'batal'])
                                                ->orderBy('production_order.tanggal', 'desc')
                                                ->first();

        }

        return response()->json($dataProductionOrder);
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

    public function StoreProductionOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idPo');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyOrder');
            $harga = $request->input('harga');
            $user = Auth::user()->user_name;

            $harga = str_replace(",", ".", $harga);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('production_order_detail')
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

                    $listItem = new ProductionOrderDetail();
                    $listItem->id_po = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_order = $qty;
                    $listItem->outstanding_qty = $qty;
                    $listItem->harga = $harga;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Production Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Production Order Detail',
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
                    $listItem->module = 'production_order';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->value5 = $qty;
                    $listItem->value6 = $harga;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Production Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Production Order Detail',
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

    public function UpdateProductionOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idPo');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyOrder');
            $harga = $request->input('hargaBeli');
            $user = Auth::user()->user_name;

            $harga = str_replace(",", ".", $harga);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = ProductionOrderDetail::find($idDetail);
                $listItem->id_po = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_order = $qty;
                $listItem->outstanding_qty = $qty;
                $listItem->harga = $harga;
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
                $listItem->value6 = $harga;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'Production Order Detail',
                'action' => 'Update',
                'desc' => 'Update Production Order Detail',
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

    public function GetProductionOrderDetail(Request $request)
    {
        $id = $request->input('idProductionOrder');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != 'edit') {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = ProductionOrderDetail::leftJoin('product', 'production_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'production_order_detail.id',
                                                'production_order_detail.id_item',
                                                'production_order_detail.qty_order',
                                                'production_order_detail.outstanding_qty',
                                                'production_order_detail.harga',
                                                DB::raw('COALESCE(production_order_detail.harga,0) * COALESCE(production_order_detail.qty_order) AS subtotal'),
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['production_order_detail.id_po', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('production_order_detail.created_by', $user);
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
                                            ['temp_transaction.module', '=', 'production_order']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditProductionOrderDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = ProductionOrderDetail::leftJoin('product', 'production_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                'production_order_detail.id',
                                                'production_order_detail.id_item',
                                                'production_order_detail.id_satuan',
                                                'production_order_detail.qty_order',
                                                'production_order_detail.outstanding_qty',
                                                'production_order_detail.harga',
                                                DB::raw('COALESCE(production_order_detail.harga,0) * COALESCE(production_order_detail.qty_order) AS subtotal'),
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'product_unit.kode_satuan'
                                            )
                                            ->where([
                                                ['production_order_detail.id', '=', $id]
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
                                            ['temp_transaction.module', '=', 'production_order']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteProductionOrderDetail(Request $request)
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
                $delete = DB::table('production_order_detail')->where('id', '=', $id)->delete();
            }


        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function GetProductionOrderFooter(Request $request)
    {
        $id = $request->input('idPo');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = ProductionOrderDetail::leftJoin('product', 'production_order_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'production_order_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(production_order_detail.qty_order) AS qtyOrder'),
                                            DB::raw('SUM(COALESCE(production_order_detail.harga,0) * COALESCE(production_order_detail.qty_order,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['production_order_detail.id_po', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('production_order_detail.created_by', $user);
                                        })
                                        ->groupBy('production_order_detail.id_po')
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
                                            ['temp_transaction.module', '=', 'production_order']
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
            'tanggal'=>'required',
            'tanggal_req'=>'required',
            'tanggal_deadline'=>'required',
            // 'metode_bayar'=>'required'
        ]);

        $tglPo = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tglPo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglPo);
        if (!$aksesTransaksi) {
            return redirect('/ProductionOrder')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $idSupplier = $request->input('supplier');
            $po = $request->input('po_production');
            $idAlamat = $request->input('id_alamat');
            $tgl = $request->input('tanggal');
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
            $flagInternal = $request->input('flag_internal');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyOrder = str_replace(",", ".", $qtyOrder);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $grandTotal = str_replace(",", ".", $grandTotal);

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('production_order')
                            ->select(DB::raw("MAX(RIGHT(no_production_order,2)) AS angka"))
                            // ->whereMonth('tanggal_po', $blnPeriode)
                            // ->whereYear('tanggal_po', $thnPeriode)
                            ->whereDate('tanggal', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            if ($counter < 10) {
                $nmrPp = "pp-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrPp = "pp-cv-".$kodeTgl.$counter;
            }

            $productionOrder = new ProductionOrder();
            $productionOrder->no_production_order = $nmrPp;
            $productionOrder->po_production = $po;
            $productionOrder->id_supplier = $idSupplier;
            $productionOrder->id_alamat = $idAlamat;
            $productionOrder->jumlah_total = $qtyOrder;
            $productionOrder->outstanding_qty = $qtyOrder;
            $productionOrder->tanggal = $tgl;
            $productionOrder->tanggal_request = $tglReq;
            $productionOrder->tanggal_deadline = $tglDeadline;
            $productionOrder->flag_internal = $flagInternal;
            // $productionOrder->flag_ppn = $flagPPn;
            // $productionOrder->nominal_po_dpp = $dpp;
            // $productionOrder->nominal_po_ppn = $ppn;
            // $productionOrder->nominal_po_ttl = $grandTotal;
            // $productionOrder->jenis_diskon = $jenisDiskon;
            // $productionOrder->nominal_diskon = $nominalDiskon;
            // $productionOrder->persentase_diskon = $persenDiskon;
            // $productionOrder->metode_pembayaran = $metodeBayar;
            // $productionOrder->durasi_jt = $durasiJt;
            $productionOrder->status = 'draft';
            $productionOrder->created_by = $user;
            $productionOrder->save();

            $data = $productionOrder;

            $setDetail = DB::table('production_order_detail')
                            ->where([
                                        ['id_po', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_po' => $productionOrder->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_po' => $productionOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                ProductionOrderTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Production Order',
                'action' => 'Simpan',
                'desc' => 'Simpan Production Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('ProductionOrder.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_production_order).' Telah Disimpan!');
        }
        else {
            return redirect('/ProductionOrder')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'id_alamat'=>'required',
            'tanggal'=>'required',
            'tanggal_req'=>'required',
            'tanggal_deadline'=>'required',
        ]);

        $tglPo = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tglPo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglPo);
        if (!$aksesTransaksi) {
            return redirect()->route('ProductionOrder.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $po = $request->input('po_production');
            $tgl = $request->input('tanggal');
            $tglReq = $request->input('tanggal_req');
            $tglDeadline = $request->input('tanggal_deadline');
            $flagInternal = $request->input('flag_internal');
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

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));

            $countKode = DB::table('production_order')
                            ->select(DB::raw("MAX(RIGHT(no_production_order,2)) AS angka"))
                            // ->whereMonth('tanggal_po', $blnPeriode)
                            // ->whereYear('tanggal_po', $thnPeriode)
                            ->whereDate('tanggal', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            if ($counter < 10) {
                $nmrPp = "pp-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrPp = "pp-cv-".$kodeTgl.$counter;
            }

            $productionOrder = ProductionOrder::find($id);
            if ($tgl != $productionOrder->tanggal) {
                $productionOrder->no_production_order = $nmrPp;
            }
            $productionOrder->id_supplier = $idSupplier;
            $productionOrder->po_production = $po;
            $productionOrder->id_alamat = $idAlamat;
            $productionOrder->jumlah_total = $qtyOrder;
            $productionOrder->outstanding_qty = $qtyOrder;
            $productionOrder->tanggal = $tgl;
            $productionOrder->tanggal_request = $tglReq;
            $productionOrder->tanggal_deadline = $tglDeadline;
            $productionOrder->flag_internal = $flagInternal;
            // $productionOrder->flag_ppn = $flagPPn;
            // $productionOrder->nominal_po_dpp = $dpp;
            // $productionOrder->nominal_po_ppn = $ppn;
            // $productionOrder->nominal_po_ttl = $grandTotal;
            // $productionOrder->jenis_diskon = $jenisDiskon;
            // $productionOrder->nominal_diskon = $nominalDiskon;
            // $productionOrder->persentase_diskon = $persenDiskon;
            // $productionOrder->metode_pembayaran = $metodeBayar;
            // $productionOrder->durasi_jt = $durasiJt;
            $productionOrder->updated_by = $user;
            $productionOrder->save();

            $data = $productionOrder;

            // $deletedDetail = ProductionOrderDetail::onlyTrashed()->where([['id_po', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'production_order'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = ProductionOrderDetail::find($detail->id_detail);
                        $listItem->id_po = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_order = $detail->value4;
                        $listItem->outstanding_qty = $detail->value5;
                        $listItem->harga = $detail->value6;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new ProductionOrderDetail();
                        $listItem->id_po = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_order = $detail->value4;
                        $listItem->outstanding_qty = $detail->value5;
                        $listItem->harga = $detail->value6;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('production_order_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'production_order'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($terms != "") {
                $delete = DB::table('production_order_terms')->where('id_po', '=', $productionOrder->id)->delete();
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_po' => $productionOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                ProductionOrderTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Production Order',
                'action' => 'Update',
                'desc' => 'Update Production Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('ProductionOrder.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_production_order).' Telah Diupdate!');
        }
        else {
            return redirect('/ProductionOrder')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id) {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $dp = $request->input('dp');
            $ProductionOrder = ProductionOrder::find($id);

            // $cekSjPosted = Receiving::where([
            //     ['id_po', '=', $id],
            //     ['status_penerimaan', '!=', 'draft']
            // ])
            // ->count();
            $cekSjPosted = 0;
            if ($btnAction == "posting") {
                $ProductionOrder->status = "posted";
                $ProductionOrder->save();
                $log = ActionLog::create([
                    'module' => 'Production Order',
                    'action' => 'Posting',
                    'desc' => 'Posting Production Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Telah Diposting!';
                $status = "success";
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($cekSjPosted == 0) {
                    $ProductionOrder->status = "draft";
                    $ProductionOrder->flag_revisi = '1';
                    $ProductionOrder->updated_by = Auth::user()->user_name;
                    $ProductionOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Production Order',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Production Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Telah Direvisi!';
                    $status = "success";
                }
                else {
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Tidak dapat Direvisi karena terdapat Surat Jalan Penerimaan atas Production Order '.strtoupper($ProductionOrder->no_production_order).' !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "tutup") {
                if ($cekSjPosted != 0) {
                    $ProductionOrder->status = "close";
                    $ProductionOrder->updated_by = Auth::user()->user_name;
                    $ProductionOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Production Order',
                        'action' => 'Tutup',
                        'desc' => 'Tutup Production Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Telah Ditutup!';
                    $status = "success";
                }
                else {
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Tidak dapat Ditutup karena belum terdapat Surat Jalan Penerimaan atas Production Order '.strtoupper($ProductionOrder->no_production_order).' !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "batal") {
                if ($cekSjPosted == 0) {
                    $ProductionOrder->status = "batal";
                    $ProductionOrder->updated_by = Auth::user()->user_name;
                    $ProductionOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Production Order',
                        'action' => 'Batal',
                        'desc' => 'Batal Production Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Telah Dibatalkan!';
                    $status = "success";
                }
                else {
                    $msg = 'Production Order '.strtoupper($ProductionOrder->no_production_order).' Tidak dapat Dibatalkan karena terdapat Surat Jalan Penerimaan atas Production Order '.strtoupper($ProductionOrder->no_production_order).' !';
                    $status = "success";
                }
            }
            $data = $ProductionOrder;
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('ProductionOrder.edit', [$id]);
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
            $id = $request->input('idProductionOrder');
            $user = Auth::user()->user_name;
            $delete = ProductionOrder::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Production Order',
                'action' => 'Delete',
                'desc' => 'Delete Production Order',
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

    public function ResetProductionOrderDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idPo');


            if ($id != "DRAFT") {
                // $detail = ProductionOrderDetail::where([
                //                             ['id_po', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);
                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'production_order'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('production_order_detail')->where('id_po', '=', $id)->delete();
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

            $dataProduct = ProductionReceiving::leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', 'receiving.id')
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
                                        'purchase_order.no_production_order',
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
