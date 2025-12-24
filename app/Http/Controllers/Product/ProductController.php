<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Classes\BusinessManagement\SetMenu;
use App\Exports\ProductExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Product\Product;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductUnit;
use App\Models\Library\Supplier;
use App\Models\Library\Customer;
use App\Models\Library\CustomerProduct;
use App\Models\Library\SupplierProduct;
use App\Models\Stock\StockTransaction;
use App\Models\ActionLog;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductSpecification;
use App\Models\Library\CustomerCategory;
use App\Models\Library\SupplierCategory;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Setting\Module;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ProductController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            if ($countAkses > 0) {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Product'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaJual = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaBeli = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataCategory = ProductCategory::all();
                $dataBrand = ProductBrand::all();
                $dataUnit = ProductUnit::all();

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
                                ->select(
                                    'product.*',
                                    'product_brand.nama_merk',
                                    'product_category.nama_kategori',
                                    'dataSpek.value_spesifikasi'
                                )
                                ->orderBy('product.id', 'desc')
                                ->get();

                $data['hakAkses'] = $hakAkses;
                $data['hakAksesHargaJual'] = $hakAksesHargaJual;
                $data['hakAksesHargaBeli'] = $hakAksesHargaBeli;
                $data['dataCategory'] = $dataCategory;
                $data['dataProduct'] = $dataProduct;
                $data['dataBrand'] = $dataBrand;
                $data['dataUnit'] = $dataUnit;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $countHakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            if ($countHakAkses > 0) {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

                $hakAksesHargaJual = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaBeli = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::find($id);
                $dataCategory = ProductCategory::find($dataProduct->kategori_item);
                $dataBrand = ProductBrand::find($dataProduct->merk_item);

                $data['hakAkses'] = $hakAkses;
                $data['hakAksesHargaJual'] = $hakAksesHargaJual ? $hakAksesHargaJual->add:null;
                $data['hakAksesHargaBeli'] = $hakAksesHargaBeli ? $hakAksesHargaBeli->add:null;
                $data['dataProduct'] = $dataProduct;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product',
                    'action' => 'Detail',
                    'desc' => 'Detail Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product.detail', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $hakAksesHargaJual = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaBeli = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $restoreC = CustomerProduct::onlyTrashed()->where([['id_item', '=', $id]]);
                $restoreC->restore();

                $restoreS = SupplierProduct::onlyTrashed()->where([['id_item', '=', $id]]);
                $restoreS->restore();

                $dataProduct = Product::find($id);
                $dataCategory = ProductCategory::find($dataProduct->kategori_item);
                $dataBrand = ProductBrand::all();
                $dataUnit = ProductUnit::all();
                $dataSpek = ProductSpecification::all();

                $data['kategoriSupplier'] = SupplierCategory::all();
                $data['kategoriCustomer'] = CustomerCategory::all();
                $dataSpek = ProductSpecification::all();

                // $restore = ProductDetail::onlyTrashed()->where([['id_product', '=', $id]]);
                //$restore->restore();

                $data['hakAkses'] = $hakAkses;
                $data['hakAksesHargaJual'] = $hakAksesHargaJual ? $hakAksesHargaJual->add:null;
                $data['hakAksesHargaBeli'] = $hakAksesHargaBeli ? $hakAksesHargaBeli->add:null;
                $data['dataProduct'] = $dataProduct;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $data['dataUnit'] = $dataUnit;
                $data['dataSpek'] = $dataSpek;
                $data['dataSpek'] = $dataSpek;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $restore = ProductDetailSpecification::onlyTrashed()->where([['id_product', '=', $id]]);
                $restore->restore();

                $log = ActionLog::create([
                    'module' => 'Product',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product.edit', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function history($id)
    {
        if (Auth::check()) {

            $countHakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            if ($countHakAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

                $hakAksesHargaJual = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaBeli = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::find($id);

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataPembelian = Receiving::leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', 'receiving.id')
                                            ->leftJoin('purchase_order', 'receiving.id_po', 'purchase_order.id')
                                            ->leftJoin('purchase_order_detail', function($join) {
                                                $join->on('purchase_order.id' , '=', 'purchase_order_detail.id_po');
                                                $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                            })

                                            ->leftjoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->leftJoin('product', 'receiving_detail.id_item', 'product.id')
                                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('purchase_invoice_detail', 'purchase_invoice_detail.id_sj', 'receiving.id')
                                            ->leftJoin('purchase_invoice', 'purchase_invoice_detail.id_invoice', 'purchase_invoice.id')
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
                                                'purchase_invoice.kode_invoice'
                                            )
                                            ->where([
                                                ['product.id', '=', $id]
                                            ])
                                            ->orderBy('purchase_order.tanggal_po', 'desc')
                                            ->get();


                $dataPenjualan = Delivery::leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', 'delivery.id')
                                        ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                        ->leftJoin('sales_order_detail', function($join) {
                                            $join->on('sales_order.id' , '=', 'sales_order_detail.id_so');
                                            $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                        })
                                        ->leftjoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', '=', 'product_unit.id')
                                        ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_sj', 'delivery.id')
                                        ->leftJoin('sales_invoice', 'sales_invoice_detail.id_invoice', 'sales_invoice.id')
                                        ->select(
                                            'product.id',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'customer.nama_customer',
                                            'sales_order.no_so',
                                            'sales_order_detail.harga_jual',
                                            'delivery.tanggal_sj',
                                            'delivery.kode_pengiriman',
                                            'delivery_detail.qty_item',
                                            'sales_invoice.kode_invoice'
                                        )
                                        ->where([
                                            ['product.id', '=', $id]
                                        ])
                                        ->orderBy('sales_order.tanggal_so', 'desc')
                                        ->get();


                $data['hakAkses'] = $hakAkses;
                $data['hakAksesHargaJual'] = $hakAksesHargaJual ? $hakAksesHargaJual->add:null;
                $data['hakAksesHargaBeli'] = $hakAksesHargaBeli ? $hakAksesHargaBeli->add:null;
                $data['userGroup'] = Auth::user()->user_group;
                $data['dataPembelian'] = $dataPembelian;
                $data['dataPenjualan'] = $dataPenjualan;
                $data['dataProduct'] = $dataProduct;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product',
                    'action' => 'History',
                    'desc' => 'History Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product.history', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Product'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $hakAksesHargaJual = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $hakAksesHargaBeli = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseOrder'],
                                                ['module_access.add', '=', 'Y'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $delete = DB::table('product_detail_specification')->where('id_product', '=', 'DRAFT')->delete();
                $delete = DB::table('customer_product')->where('id_item', '=', 'DRAFT')->delete();
                $delete = DB::table('supplier_product')->where('id_item', '=', 'DRAFT')->delete();

                $dataCategory = ProductCategory::all();
                $dataBrand = ProductBrand::all();
                $dataUnit = ProductUnit::all();
                $dataSpek = ProductSpecification::all();


                $data['kategoriSupplier'] = SupplierCategory::all();
                $data['kategoriCustomer'] = CustomerCategory::all();

                $data['hakAkses'] = $hakAkses;
                $data['hakAksesHargaJual'] = $hakAksesHargaJual;
                $data['hakAksesHargaBeli'] = $hakAksesHargaBeli;
                $data['dataCategory'] = $dataCategory;
                $data['dataBrand'] = $dataBrand;
                $data['dataUnit'] = $dataUnit;
                $data['dataSpek'] = $dataSpek;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $delete = DB::table('product_detail')->where('id_product', '=', 'DRAFT')->delete();
                $delete = DB::table('product_detail_specification')->where('id_product', '=', 'DRAFT')->delete();

                $log = ActionLog::create([
                    'module' => 'Product',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product.add', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function storePriceData(Request $request)
    {
        $id = $request->input('idProduct');
        $hargaBeli = $request->input('hargaBeli');
        $hargaJual = $request->input('hargaJual');

        $items = Product::find($id);

        if ($hargaBeli != "") {
            $items->harga_beli = $hargaBeli;
        }

        if ($hargaJual != "") {
            $items->harga_jual = $hargaJual;
        }

        $items->save();

        $log = ActionLog::create([
            'module' => 'Product',
            'action' => 'Update Harga',
            'desc' => 'Update Harga Product',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");

    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_item'=>'required',
            'kategori_item'=>'required',
            'jenis_item'=>'required',
            'merk_item'=>'required',
        ]);

        // $kd = strtolower($request->input('kode_item'));
        $nm = ucwords($request->input('nama_item'));
        $kategori = $request->input('kategori_item');
        $jenis = $request->input('jenis_item');
        $merk = $request->input('merk_item');
        $ketItem = $request->input('keterangan_item_txt');
        $p = $request->input('panjang_item');
        $l = $request->input('lebar_item');
        $t = $request->input('tinggi_item');
        $b = $request->input('berat_item');
        $pDus = $request->input('panjang_dus');
        $lDus = $request->input('lebar_dus');
        $tDus = $request->input('tinggi_dus');
        $bDus = $request->input('berat_dus');
        $qtyDus = $request->input('qty_per_dus');
        $hrgBeli = $request->input('harga_beli');
        $hrgJual = $request->input('harga_jual');
        $user = Auth::user()->user_name;

        // $stokMinimum = str_replace(",", ".", $stokMinimum);
        // $stokMaks = str_replace(",", ".", $stokMaks);

        $initKode = ProductCategory::select('kode_kategori')->where('id', $kategori)->first();
        $kode = $initKode->kode_kategori;
        $countKode = Product::select(DB::raw("MAX(RIGHT(kode_item,4)) AS angka"))->where('kategori_item', $kategori)->first();
        $count = $countKode->angka;
        $counter = $count + 1;

        if ($counter < 10) {
          $kode_item = $kode.'000'.$counter;
        }
        elseif ($counter < 100) {
          $kode_item = $kode.'00'.$counter;
        }
        elseif ($counter < 1000) {
          $kode_item = $kode.'0'.$counter;
        }

        $img = $request->file('img_item');
        if ($img != "") {
            $ext = $img->getClientOriginalExtension();
            $namaImgItem = $kode_item.".".$ext;
            $request->file('img_item')->storeAs('products/', $namaImgItem, 'images');
        }
        else {
            $namaImgItem = "";
        }

        $items = Product::firstOrCreate(
            ['kode_item' => strtolower($kode_item)],
            [
                'nama_item' => $nm,
                'kategori_item' => $kategori,
                'jenis_item' => $jenis,
                'merk_item' => $merk,
                // 'panjang_item' => $p,
                // 'lebar_item' => $l,
                // 'tinggi_item' => $t,
                // 'berat_item' => $b,
                // 'panjang_dus' => $pDus,
                // 'lebar_dus' => $lDus,
                // 'tinggi_dus' => $tDus,
                // 'berat_dus' => $bDus,
                // 'qty_per_dus' => $qtyDus,
                // 'harga_beli' => $hrgBeli,
                // 'harga_jual' => $hrgJual,
                'product_image_path' => $namaImgItem,
                'active' => 'Y',
                // 'stok_minimum' => $stokMinimum,
                // 'stok_maksimum' => $stokMaks,
                'keterangan_item' => $ketItem,
                'created_by' => $user
            ]
        );

        $setDetail = DB::table('product_detail')
                            ->where([
                                        ['id_product', '=', 'DRAFT']
                                    ])
                            ->update([
                                'id_product' => $items->id,
                                'updated_by' => $user
                            ]);

        $setDetailSpec = DB::table('product_detail_specification')
                            ->where([
                                        ['id_product', '=', 'DRAFT']
                                    ])
                            ->update([
                                'id_product' => $items->id,
                                'updated_by' => $user
                            ]);

        $setDetailSpec = DB::table('product_detail_specification')
                            ->where([
                                        ['id_product', '=', 'DRAFT']
                                    ])
                            ->update([
                                'id_product' => $items->id,
                                'updated_by' => $user
                            ]);

        $arrayDetailSupplier = $request->input('supplier');
        if ($arrayDetailSupplier != "") {

            $countDetailSupplier = count($arrayDetailSupplier);
            $listDetailSupplier = [];
            for ($j = 0; $j < $countDetailSupplier; $j++) {
                $dataSupplier=[
                    'id_supplier' => $arrayDetailSupplier[$j],
                    'id_item' => $items->id,
                    'created_by' => $user,
                    'created_at' => now()
                ];
                array_push($listDetailSupplier, $dataSupplier);
            }

            SupplierProduct::insert($listDetailSupplier);
        }

        $arrayDetailCustomer = $request->input('customer');
        if ($arrayDetailCustomer != "") {

            $countDetailCustomer = count($arrayDetailCustomer);
            $listDetailCustomer = [];
            for ($j = 0; $j < $countDetailCustomer; $j++) {
                $dataCustomer=[
                    'id_customer' => $arrayDetailCustomer[$j],
                    'id_item' => $items->id,
                    'created_by' => $user,
                    'created_at' => now()
                ];
                array_push($listDetailCustomer, $dataCustomer);
            }

            CustomerProduct::insert($listDetailCustomer);
        }

        $setProductC = DB::table('customer_product')
                         ->where([
                                    ['id_item', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_item' => $items->id,
                            'updated_by' => $user
                        ]);

        $setProductS = DB::table('supplier_product')
                         ->where([
                                    ['id_item', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_item' => $items->id,
                            'updated_by' => $user
                        ]);

        // if ($stokAwal != null or $stokAwal > 0) {
        //     $inputStockAwal = new StockTransaction();
        //     $inputStockAwal->kode_transaksi = strtolower($kode_item);
        //     $inputStockAwal->id_item = $items->id;
        //     $inputStockAwal->qty_item = $stokAwal;
        //     $inputStockAwal->tgl_transaksi = now();
        //     $inputStockAwal->jenis_transaksi = "stok_awal";
        //     $inputStockAwal->transaksi = "in";
        //     $inputStockAwal->created_by = Auth::user()->user_name;
        //     $inputStockAwal->save();
        // }


        if ($items->wasRecentlyCreated) {
            return redirect('/Product')->with('success', 'Data '.strtoupper($kode_item).' Telah Disimpan!');
        }
        else {
            return redirect('/Product')->with('danger', 'Kode '.strtoupper($kode_item).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_item'=>'required',
            'jenis_item'=>'required',
            'merk_item'=>'required',
        ]);

        $hakAksesHarga = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductPrice'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

        $kode_item = ucwords($request->input('kode_item'));
        $nm = ucwords($request->input('nama_item'));
        $jenis = $request->input('jenis_item');
        $merk = $request->input('merk_item');
        // $stokMinimum = $request->input('stok_minimum');
        // $stokMaks = $request->input('stok_maksimum');
        // $satuanItem = $request->input('satuan_item');
        $ketItem = $request->input('keterangan_item_txt');
        // $p = $request->input('panjang_item');
        // $l = $request->input('lebar_item');
        // $t = $request->input('tinggi_item');
        // $b = $request->input('berat_item');
        // $pDus = $request->input('panjang_dus');
        // $lDus = $request->input('lebar_dus');
        // $tDus = $request->input('tinggi_dus');
        // $bDus = $request->input('berat_dus');
        // $qtyDus = $request->input('qty_per_dus');
        // $hrgBeli = $request->input('harga_beli');
        // $hrgJual = $request->input('harga_jual');
        $user = Auth::user()->user_name;

        $updateGambar = $request->input('gambar_item');

        $img = $request->file('img_item');
        if ($img != "") {
            Storage::disk('images')->delete('products/'.$updateGambar);
            $ext = $img->getClientOriginalExtension();
            $namaImgItem = $kode_item.".".$ext;
            $request->file('img_item')->storeAs('products/', $namaImgItem, 'images');
            $updateGambar = $namaImgItem;
        }

        $items = Product::find($id);
        $items->nama_item = $nm;
        $items->jenis_item = $jenis;
        $items->merk_item = $merk;
        // $items->satuan_item = $satuanItem;
        // $items->panjang_item = $p;
        // $items->lebar_item = $l;
        // $items->tinggi_item = $t;
        // $items->berat_item = $b;
        // $items->panjang_dus = $pDus;
        // $items->lebar_dus = $lDus;
        // $items->tinggi_dus = $tDus;
        // $items->berat_dus = $bDus;
        // $items->qty_per_dus = $qtyDus;

        // if ($hakAksesHarga != null && (Auth::user()->user_group != "pembelian" || Auth::user()->user_group != "admin" || Auth::user()->user_group != "super_admin"))
        // {
        //     $items->harga_beli = $hrgBeli;

        // }

        // if ($hakAksesHarga != null && (Auth::user()->user_group != "penjualan" || Auth::user()->user_group != "admin" || Auth::user()->user_group != "super_admin"))
        // {
        //     $items->harga_jual = $hrgJual;

        // }

        $items->product_image_path = $updateGambar;
        $items->keterangan_item = $ketItem;
        $items->updated_by = $user;
        $items->save();

        $deleteDetailSpek = DB::table('product_detail_specification')
                                    ->where([
                                        ['id_product', '=', $id],
                                        ['deleted_at', '!=', null]
                                    ])
                                    ->delete();

        $deleteDetailS = DB::table('supplier_product')
                                    ->where([
                                        ['id_item', '=', $id],
                                        ['deleted_at', '!=', null]
                                    ])
                                    ->delete();

        $deleteDetailC = DB::table('customer_product')
                                    ->where([
                                        ['id_item', '=', $id],
                                        ['deleted_at', '!=', null]
                                    ])
                                    ->delete();

        if ($items) {
            return redirect('/Product')->with('success', 'Data '.strtoupper($kode_item).' Berhasil di Update!');
        }
        else {
            return redirect('/Product')->with('danger', 'Kode '.strtoupper($kode_item).' Gagal di Update!');
        }
    }

    public function delete(Request $request)
    {

        $id = $request->input('id_product');

        $cekPO = PurchaseOrderDetail::where([
                                        ['id_item', '=', $id]
                                    ])
                                    ->count();

        $cekSO = SalesOrderDetail::where([
                                        ['id_item', '=', $id]
                                    ])
                                    ->count();

        if ($cekPO > 0 && $cekSO > 0) {
            return response()->json(['failUsed']);
        }
        else {
            $user = Auth::user()->user_name;
            $delete = Product::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Product',
                'action' => 'Delete',
                'desc' => 'Delete Product',
                'username' => Auth::user()->user_name
            ]);

            return response()->json('success');
        }
    }

    public function DeleteProductSpec(Request $request)
    {
        $msg = "";
        $exception = DB::transaction(function () use ($request, &$msg) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');
            $detail = ProductDetailSpecification::find($id);

            if ($mode != "") {

                $detail->deleted_by = Auth::user()->user_name;
                $detail->save();

                $detail->delete();
            }
            else {
                $delete = DB::table('product_detail_specification')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function EditProductSpec(Request $request)
    {
        $id = $request->input('idDetail');

        $detail = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', '=', 'product_specification.id')
                                ->select(
                                    'product_specification.kode_spesifikasi',
                                    'product_specification.nama_spesifikasi',
                                    'product_detail_specification.*',
                                )
                                ->where([
                                    ['product_detail_specification.id', '=', $id]
                                ])
                                ->first();

        return response()->json($detail);
    }

    public function UpdateProductSpec(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $idDetail = $request->input('idDetail');
            $id = $request->input('idProduct');
            $idSpek = $request->input('id_spesifikasi');
            $valSpec = $request->input('nilai_spesifikasi');
            $mode = $request->input('mode');

            if ($id == "") {
                $id = 'DRAFT';
            }

            $listItem = ProductDetailSpecification::find($idDetail);
            $listItem->id_product = $id;
            $listItem->id_spesifikasi = $idSpek;
            $listItem->value_spesifikasi = $valSpec;
            $listItem->created_by = Auth::user()->user_name;
            $listItem->save();

            $log = ActionLog::create([
                'module' => 'Product Specification Detail',
                'action' => 'Update',
                'desc' => 'Update Product Specification Detail',
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

    public function StoreProductSpec(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProduct');
            $idSpec = $request->input('id_spesifikasi');
            $valSpec = $request->input('nilai_spesifikasi');
            $mode = $request->input('mode');

            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            $countItem = DB::table('product_detail_specification')->select(DB::raw("COUNT(*) AS angka"))->where([['id_product', '=' , $id], ['id_spesifikasi', '=', $idSpec]])->first();
            $count = $countItem->angka;

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listSpec = new ProductDetailSpecification();
                $listSpec->id_product = $id;
                $listSpec->id_spesifikasi = $idSpec;
                $listSpec->value_spesifikasi = $valSpec;
                $listSpec->created_by = $user;
                $listSpec->save();

                $log = ActionLog::create([
                    'module' => 'Produk Detail Spek',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Produk Detail Spek',
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

    public function getProductDetail(Request $request)
    {
        $id = $request->input('idProduct');
        if ($id == "") {
            $id = 'DRAFT';
        }

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                    'product_detail.*'
                                )
                                ->where([
                                    ['product_detail.id_product', '=', $id]
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function getProductSpec(Request $request)
    {
        $id = $request->input('idProduct');
        if ($id == "") {
            $id = 'DRAFT';
        }

        $detail = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', '=', 'product_specification.id')
                                ->select(
                                    'product_specification.kode_spesifikasi',
                                    'product_specification.nama_spesifikasi',
                                    'product_detail_specification.*'
                                )
                                ->where([
                                    ['product_detail_specification.id_product', '=', $id]
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function DetailItems(Request $request)
    {
        $id = $request->input('id');
        $module = $request->input('module');

        if ($id == "") {
            $id = 'DRAFT';
        }

        if ($module == "customer") {

            $data = CustomerProduct::leftJoin('customer', 'customer_product.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_category', 'customer.kategori_customer', '=', 'customer_category.id')
                                    ->select(
                                        'customer_product.id',
                                        'customer.kode_customer',
                                        'customer.nama_customer',
                                        'customer_category.nama_kategori',
                                    )
                                    ->where('id_item', $id)
                                    ->get();
        }
        else if ($module == "supplier") {

            $data = SupplierProduct::leftJoin('supplier', 'supplier_product.id_supplier', '=', 'supplier.id')
                                    ->leftJoin('supplier_category', 'supplier.kategori_supplier', '=', 'supplier_category.id')
                                    ->select(
                                        'supplier_product.id',
                                        'supplier.kode_supplier',
                                        'supplier.nama_supplier',
                                        'supplier_category.nama_kategori',
                                    )
                                    ->where('id_item', $id)
                                    ->get();
        }

        return response()->json($data);
    }

    public function getSuppAndCustomer(Request $request)
    {
        $id = $request->input('id');
        $module = $request->input('module');
        $dataProduct = "";
        if ($id == "") {
            $id = 'DRAFT';
        }

        if ($module == "customer") {

            $dataProduct = Customer::leftJoin('customer_category', 'customer.kategori_customer', 'customer_category.id')
                                    ->select(
                                        'customer.*',
                                        'customer_category.nama_kategori'
                                    )
                                    ->whereNOTIn('customer.id', function($query) use ($id) {
                                        $query->select('id_customer')->from('customer_product')
                                        ->when($id == "DRAFT", function($q) use ($id) {
                                            $q->where([
                                                ['id_item', '=', $id],
                                                ['created_by', '=', Auth::user()->user_name]
                                            ]);
                                        })
                                        ->when($id != "DRAFT", function($q) use ($id) {
                                            $q->where([
                                                ['id_item', '=', $id]
                                            ]);
                                        });
                                    })
                                    ->get();

        }
        else if ($module == "supplier") {

            $dataProduct = Supplier::leftJoin('supplier_category', 'supplier.kategori_supplier', 'supplier_category.id')
                                    ->select(
                                        'supplier.*',
                                        'supplier_category.nama_kategori'
                                    )
                                    ->whereNOTIn('supplier.id', function($query) use ($id) {
                                        $query->select('id_supplier')->from('supplier_product')
                                        ->when($id == "DRAFT", function($q) use ($id) {
                                            $q->where([
                                                ['id_item', '=', $id],
                                                ['created_by', '=', Auth::user()->user_name]
                                            ]);
                                        })
                                        ->when($id != "DRAFT", function($q) use ($id) {
                                            $q->where([
                                                ['id_item', '=', $id]
                                            ]);
                                        });
                                    })
                                    ->get();
        }

        return response()->json($dataProduct);
    }

    public function addCustomerOrSupplier(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('id_item');
            $idCustOrSupp = $request->input('cust_supp');
            $module = $request->input('module');

            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($module == "customer") {
                $customerProduct = new CustomerProduct();
                $customerProduct->id_customer = $idCustOrSupp;
                $customerProduct->id_item = $id;
                $customerProduct->created_by = Auth::user()->user_name;
                $customerProduct->save();
                $data = $customerProduct;
            }
            else if ($module == "supplier") {
                $supplierProduct = new SupplierProduct();
                $supplierProduct->id_supplier = $idCustOrSupp;
                $supplierProduct->id_item = $id;
                $supplierProduct->created_by = Auth::user()->user_name;
                $supplierProduct->save();
                $data = $supplierProduct;
            }
        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function DeleteCustomerOrSupplierItem(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');
        $module = $request->input('module');
        $user = Auth::user()->user_name;

        if ($mode == "edit") {
            if ($module == "customer") {
                $data = CustomerProduct::find($id);
                $data->deleted_by = $user;
                $data->save();
                $data->delete();
            }
            else if ($module == "supplier") {
                $data = SupplierProduct::find($id);
                $data->deleted_by = $user;
                $data->save();
                $data->delete();
            }
        }
        else {
            if ($module == "customer") {
                $delete = DB::table('customer_product')->where('id', '=', $id)->delete();
            }
            else if ($module == "supplier") {
                $delete = DB::table('supplier_product')->where('id', '=', $id)->delete();
            }
        }

        return response()->json("success");
    }

    public function StoreProductDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idProduct');
            $idSatuan = $request->input('id_satuan');
            $p = $request->input('panjang');
            $l = $request->input('lebar');
            $t = $request->input('tinggi');
            $b = $request->input('berat');
            $hargaBeli = $request->input('harga_beli');
            $hargaJual = $request->input('harga_jual');
            $stokMin = $request->input('stok_min');
            $stokMax = $request->input('stok_max');
            $pDus = $request->input('panjangDus');
            $lDus = $request->input('lebarDus');
            $tDus = $request->input('tinggiDus');
            $bDus = $request->input('beratDus');
            $qDus = $request->input('qtyPerDus');
            $mode = $request->input('mode');

            $user = Auth::user()->user_name;

            $hakAksesHargaJual = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.add', '=', 'Y'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $hakAksesHargaBeli = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.add', '=', 'Y'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();


            if ($id == "") {
                $id = 'DRAFT';
            }

            $countItem = DB::table('product_detail')->select(DB::raw("COUNT(*) AS angka"))->where([['id_product', '=' , $id], ['id_satuan', '=', $idSatuan]])->first();
            $count = $countItem->angka;

            $countDef = DB::table('product_detail')->select(DB::raw("COUNT(*) AS angkaDef"))->where([['id_product', '=' , $id], ['default', '=', 'Y']])->first();
            $countDefault = $countDef->angkaDef;

            if ($countDefault > 0) {
                $flagDef = 'N';
            }
            else {
                $flagDef = 'Y';
            }

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listItem = new ProductDetail();
                $listItem->id_product = $id;
                $listItem->id_satuan = $idSatuan;
                $listItem->panjang_item = $p;
                $listItem->lebar_item = $l;
                $listItem->tinggi_item = $t;
                $listItem->berat_item = $b;
                if ($hakAksesHargaBeli != null)
                {
                    $listItem->harga_beli = $hargaBeli;

                }
                if ($hakAksesHargaJual != null)
                {
                    $listItem->harga_jual = $hargaJual;

                }
                $listItem->stok_minimum = $stokMin;
                $listItem->stok_maksimum = $stokMax;
                $listItem->panjang_dus = $pDus;
                $listItem->lebar_dus = $lDus;
                $listItem->tinggi_dus = $tDus;
                $listItem->berat_dus = $bDus;
                // $listItem->qty_per_dus = $qDus;
                $listItem->default = $flagDef;
                $listItem->created_by = $user;
                $listItem->save();

                // if ($mode == "tambah" && ($stokAwal != null && $stokAwal > 0)) {
                //     $product = Product::find($id);
                //     $inputStockAwal = new StockTransaction();
                //     $inputStockAwal->kode_transaksi = strtolower($product->kode_item);
                //     $inputStockAwal->id_item = $id;
                //     $inputStockAwal->id_satuan = $idSatuan;
                //     $inputStockAwal->qty_item = $stokAwal;
                //     $inputStockAwal->tgl_transaksi = now();
                //     $inputStockAwal->jenis_transaksi = "stok_awal";
                //     $inputStockAwal->transaksi = "in";
                //     $inputStockAwal->created_by = Auth::user()->user_name;
                //     $inputStockAwal->save();
                // }

                $log = ActionLog::create([
                    'module' => 'Produk Detail',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Produk Detail',
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

    public function EditProductDetail(Request $request)
    {
        $id = $request->input('idDetail');

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                    'product_detail.*'
                                )
                                ->where([
                                    ['product_detail.id', '=', $id]
                                ])
                                ->first();

        return response()->json($detail);
    }

    public function UpdateProductDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $idDetail = $request->input('idDetail');
            $id = $request->input('idProduct');
            $idSatuan = $request->input('id_satuan');
            $p = $request->input('panjang');
            $l = $request->input('lebar');
            $t = $request->input('tinggi');
            $b = $request->input('berat');
            $hargaBeli = $request->input('harga_beli');
            $hargaJual = $request->input('harga_jual');
            $stokMin = $request->input('stok_min');
            $stokMax = $request->input('stok_max');
            $pDus = $request->input('panjangDus');
            $lDus = $request->input('lebarDus');
            $tDus = $request->input('tinggiDus');
            $bDus = $request->input('beratDus');
            $qDus = $request->input('qtyPerDus');
            $mode = $request->input('mode');

            $hakAksesHargaJual = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.add', '=', 'Y'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $hakAksesHargaBeli = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseOrder'],
                                            ['module_access.add', '=', 'Y'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($id == "") {
                $id = 'DRAFT';
            }

            $listItem = ProductDetail::find($idDetail);
            $listItem->id_product = $id;
            $listItem->id_satuan = $idSatuan;
            $listItem->panjang_item = $p;
            $listItem->lebar_item = $l;
            $listItem->tinggi_item = $t;
            $listItem->berat_item = $b;
            if ($hakAksesHargaBeli != null)
            {
                $listItem->harga_beli = $hargaBeli;

            }
            if ($hakAksesHargaJual != null)
            {
                $listItem->harga_jual = $hargaJual;

            }
            $listItem->stok_minimum = $stokMin;
            $listItem->stok_maksimum = $stokMax;
            $listItem->panjang_dus = $pDus;
            $listItem->lebar_dus = $lDus;
            $listItem->tinggi_dus = $tDus;
            $listItem->berat_dus = $bDus;
            // $listItem->qty_per_dus = $qDus;
            $listItem->created_by = Auth::user()->user_name;
            $listItem->save();

            $log = ActionLog::create([
                'module' => 'Product Detail',
                'action' => 'Update',
                'desc' => 'Update Product Detail',
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

    public function DeleteProductDetail(Request $request)
    {
        $msg = "";
        $exception = DB::transaction(function () use ($request, &$msg) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');
            $detail = ProductDetail::find($id);

            if ($detail->default == "Y") {
                $msg = "failDefault";
            }
            else {
                if ($mode != "") {

                    $detail->deleted_by = Auth::user()->user_name;
                    $detail->save();

                    $detail->delete();
                }
                else {
                    $delete = DB::table('product_detail')->where('id', '=', $id)->delete();
                }
            }
        });

        if (is_null($exception)) {
            if ($msg != "") {
                return response()->json("failDefault");
            }
            else {
                return response()->json("success");
            }
        }
        else {
            return response()->json($exception);
        }
    }

    public function SetDefault(Request $request)
    {
        $id = $request->input('idDetail');
        $idProduct = $request->input('idProduct');

        if ($idProduct == "") {
            $idProduct = 'DRAFT';
        }

        $updateFlagAlamt = $update = DB::table('product_detail')
                                ->where('id_product', $idProduct)
                                ->update([
                                    'default' => 'N'
                                ]);

        $setFlagAlamt = $update = DB::table('product_detail')
                                ->where('id', $id)
                                ->update([
                                    'default' => 'Y'
                                ]);

        $log = ActionLog::create([
                'module' => 'Product Detail',
                'action' => 'Set Default',
                'desc' => 'Set Default Product Detail',
                'username' => Auth::user()->user_name
            ]);

        return response()->json("success");
    }

    public function SetMonitor(Request $request)
    {
        $id = $request->input('idDetail');
        $flag = $request->input('flag');


        $setFlag = $update = DB::table('product_detail')
                                ->where('id', $id)
                                ->update([
                                    'flag_monitor' => $flag
                                ]);

        $log = ActionLog::create([
                'module' => 'Product Detail',
                'action' => 'Set Monitor',
                'desc' => 'Set Monitor Product Detail',
                'username' => Auth::user()->user_name
            ]);

        return response()->json("success");
    }

    public function exportDataProduct(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new ProductExport($request), 'Product_'.$kodeTgl.'.xlsx');
    }
}
