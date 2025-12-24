<?php

namespace App\Http\Controllers\Library;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLSubAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierCategory;
use App\Models\Library\SupplierDetail;
use App\Models\Library\SupplierProduct;
use App\Models\Product\Product;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use App\Models\ActionLog;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Setting\Module;
use stdClass;

class SupplierController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Supplier'],
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
                                                ['module.url', '=', '/Supplier'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataKota = SupplierDetail::distinct()->get('kota');

                $data['hakAkses'] = $hakAkses;
                $data['dataKota'] = $dataKota;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Supplier',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.supplier.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $supplier = Supplier::leftJoin('supplier_category', 'supplier.kategori_supplier', '=', 'supplier_category.id')
                            ->leftJoin('supplier_detail', 'supplier.id', '=', 'supplier_detail.id_supplier')
                            ->select(
                                'supplier.id',
                                'supplier.kode_supplier',
                                'supplier.nama_supplier',
                                'supplier.npwp_supplier',
                                'supplier.telp_supplier',
                                'supplier.fax_supplier',
                                'supplier.email_supplier',
                                'supplier.kategori_supplier',
                                'supplier_detail.kota',
                                'supplier_category.nama_kategori')
                            ->where([
                                ['supplier_detail.default', '=', 'Y']
                            ])
                            ->get();
        return response()->json($supplier);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == 'Y') {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $delete = DB::table('supplier_detail')->where('id_supplier', '=', 'DRAFT')->delete();
                $delete = DB::table('supplier_product')->where('id_supplier', '=', 'DRAFT')->delete();

                $supplierCategory = SupplierCategory::all();
                $dataKota = SupplierDetail::distinct()->get('kota');
                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Supplier',
                    'username' => Auth::user()->user_name
                ]);

                $data['supplierCategory'] = $supplierCategory;
                $data['dataKota'] = $dataKota;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);


                return view('pages.library.supplier.add', $data);
            }
            else {
                return redirect('/Supplier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == 'Y') {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSupplier = Supplier::find($id);

                $supplierCategory = SupplierCategory::all();

                $restore = SupplierProduct::onlyTrashed()->where([['id_supplier', '=', $id]]);
                $restore->restore();

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Supplier',
                    'username' => Auth::user()->user_name
                ]);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

                $data['dataSupplier'] = $dataSupplier;
                $data['supplierCategory'] = $supplierCategory;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);


                return view('pages.library.supplier.edit', $data);
            }
            else {
                return redirect('/Supplier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $user = Auth::user()->user_group;

            if ($countHakAkses > 0) {
                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSupplier = Supplier::find($id);

                $supplierCategory = SupplierCategory::all();

                $dataDetail = PurchaseOrder::leftJoin('purchase_order_detail', 'purchase_order.id', '=', 'purchase_order_detail.id_po')
                                            ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                            ->select('id_item', DB::raw("harga_beli AS harga_beli_last"))
                                            ->where([
                                                    ['purchase_order.status_po', '!=', 'draft'],
                                                    ['purchase_order.id_supplier', '=', $id]
                                                    ])
                                            ->groupBy('purchase_order.id_supplier')
                                            ->groupBy('purchase_order_detail.id_item')
                                            ->groupBy('purchase_order_detail.id_satuan')
                                            ->orderBy('purchase_order.tanggal_po', 'desc');

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $supplierProduct = SupplierProduct::leftJoin('product', 'product.id', '=', 'supplier_product.id_item')
                                                    ->leftJoinSub($dataDetail, 'dataDetail', function($dataDetail) {
                                                        $dataDetail->on('supplier_product.id_item', '=', 'dataDetail.id_item');
                                                    })
                                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                                    })
                                                    ->select(
                                                        'product.kode_item',
                                                        'product.nama_item',
                                                        DB::raw('COALESCE(dataDetail.harga_beli_last, 0) AS harga_beli'),
                                                        'dataSpek.value_spesifikasi'
                                                    )
                                                    ->where([
                                                                ['supplier_product.id_supplier', '=', $id],
                                                                ['product.deleted_at', '=', null]
                                                            ])
                                                    ->get();

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'Detail',
                    'desc' => 'Detail Supplier',
                    'username' => Auth::user()->user_name
                ]);

                $data['dataSupplier'] = $dataSupplier;
                $data['supplierCategory'] = $supplierCategory;
                $data['supplierProduct'] = $supplierProduct;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.supplier.detail', $data);
            }
            else {
                return redirect('/Supplier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $userName = Auth::user()->user_name;

            if ($countHakAkses > 0) {
                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataPurchase = PurchaseOrder::leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                ->select(
                                    'purchase_order.id',
                                    'purchase_order.no_po',
                                    'purchase_order.jumlah_total_po',
                                    'purchase_order.outstanding_po',
                                    'purchase_order.tanggal_po',
                                    'purchase_order.tanggal_request',
                                    'purchase_order.tanggal_deadline',
                                    'purchase_order.nominal_po_ttl',
                                    'purchase_order.status_po',
                                    'supplier.nama_supplier',
                                    DB::raw("(SELECT COUNT(*) from print_log WHERE no_dokumen = purchase_order.no_po AND created_by = '".$userName."') AS countprint"))
                                ->where([
                                        ['purchase_order.id_supplier', '=', $id]
                                    ])
                                ->orderBy('purchase_order.id', 'desc')
                                ->get();

                $dataReceiving = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                ->select(
                                    'receiving.id',
                                    'receiving.kode_penerimaan',
                                    'receiving.no_sj_supplier',
                                    'receiving.jumlah_total_sj',
                                    'receiving.tanggal_sj',
                                    'receiving.tanggal_terima',
                                    'receiving.status_penerimaan',
                                    'purchase_order.no_po',
                                    'supplier.nama_supplier')
                                ->where([
                                    ['purchase_order.id_supplier', '=', $id]
                                ])
                                ->orderBy('receiving.tanggal_sj', 'desc')
                                ->get();

                $dataInvoice = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                ->select(
                                    'purchase_invoice.id',
                                    'purchase_invoice.kode_invoice',
                                    'purchase_invoice.grand_total',
                                    'purchase_invoice.ttl_qty',
                                    'purchase_invoice.tanggal_invoice',
                                    'purchase_invoice.tanggal_jt',
                                    'purchase_invoice.status_invoice',
                                    'purchase_order.no_po',
                                    'supplier.nama_supplier')
                                ->where([
                                    ['purchase_order.id_supplier', '=', $id]
                                ])
                                ->orderBy('purchase_invoice.tanggal_invoice', 'desc')
                                ->get();

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'History',
                    'desc' => 'History Supplier',
                    'username' => Auth::user()->user_name
                ]);

                $data['dataPurchase'] = $dataPurchase;
                $data['dataReceiving'] = $dataReceiving;
                $data['dataInvoice'] = $dataInvoice;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.supplier.history', $data);
            }
            else {
                return redirect('/Supplier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail_barang($id)
    {
        if (Auth::check()) {

            $countHakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Supplier'],
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
                                            ['module.url', '=', '/Supplier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

                $dataSupplier = Supplier::find($id);

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataPembelian = Receiving::leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', 'receiving.id')
                                        ->leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                                        ->leftJoin('purchase_order_detail', function($join) {
                                            $join->on('purchase_order.id' , '=', 'purchase_order_detail.id_po');
                                            $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                        })
                                        ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                        ->leftjoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                        ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
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
                                            'receiving.status_penerimaan',
                                            'receiving_detail.qty_item',
                                            'purchase_invoice.kode_invoice',
                                            'purchase_invoice.status_invoice',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['supplier.id', '=', $id]
                                        ])
                                        ->orderBy('receiving.id', 'desc')
                                        ->get();


                $data['hakAkses'] = $hakAkses;
                $data['userGroup'] = Auth::user()->user_group;
                $data['dataPembelian'] = $dataPembelian;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Supplier',
                    'action' => 'History',
                    'desc' => 'History Supplier Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.supplier.detail_product', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function StoreSupplierAddress(Request $request)
    {
        $id = $request->input('idAlamat');
        $idSupplier = $request->input('idSupplier');
        $alamat = $request->input('alamat');
        $kecamatan = $request->input('kecamatan');
        $kelurahan = $request->input('kelurahan');
        $kota = $request->input('kota');
        $kodePos = $request->input('kodePos');
        $jenisAlamat = $request->input('jenisAlamat');
        $pic = $request->input('pic');
        $noPic = $request->input('noPic');
        $user = Auth::user()->user_name;

        if ($idSupplier == "") {
            $idSupplier = 'DRAFT';
        }

        $countKode = DB::table('supplier_detail')->select(DB::raw("COUNT(*) AS angka"))->where([['id_supplier', '=' , $idSupplier], ['jenis_alamat', '=', 'NPWP']])->first();
        $count = $countKode->angka;

        $countDef = DB::table('supplier_detail')->select(DB::raw("COUNT(*) AS angkaDef"))->where([['id_supplier', '=' , $idSupplier], ['default', '=', 'Y']])->first();
        $countDefault = $countDef->angkaDef;

        if ($countDefault > 0) {
            $flagDef = 'N';
        }
        else {
            $flagDef = 'Y';
        }

        if ($id != "") {
            $getFlag = DB::table('supplier_detail')->select('default')->where([['id', '=' , $id]])->first();
            $flagDef = $getFlag->default;
        }

        if ($count > 0 && $jenisAlamat == "NPWP" && $id == "") {
            return response()->json("failNpwp");
        }
        else {
            SupplierDetail::updateOrCreate(
                ['id' => $id],
                [
                    'id_supplier' => $idSupplier,
                    'alamat_supplier' => $alamat,
                    'kelurahan' => $kelurahan,
                    'kecamatan' => $kecamatan,
                    'kota' => $kota,
                    'kode_pos' => $kodePos,
                    'jenis_alamat' => $jenisAlamat,
                    'pic_alamat' => $pic,
                    'telp_pic' => $noPic,
                    'default' => $flagDef,
                    'created_by' => $user
                ]
            );

            $log = ActionLog::create([
                'module' => 'Supplier',
                'action' => 'Simpan',
                'desc' => 'Simpan Alamat Supplier',
                'username' => Auth::user()->user_name
            ]);

            return response()->json("success");
        }
    }

    public function GetSupplierAddress(Request $request)
    {
        $idSupplier = $request->input('idSupplier');
        if ($idSupplier == "") {
            $idSupplier = 'DRAFT';
        }

        $displayAddress = SupplierDetail::where([
                                            ['id_supplier', '=', $idSupplier]
                                        ])
                                        ->get();

        return response()->json($displayAddress);
    }

    public function SetDefaultAddress(Request $request)
    {
        $id = $request->input('idAddress');
        $idSupplier = $request->input('idSupplier');

        if ($idSupplier == "") {
            $idSupplier = 'DRAFT';
        }

        $updateFlagAlamt = $update = DB::table('supplier_detail')
                                ->where('id_supplier', $idSupplier)
                                ->update([
                                    'default' => 'N'
                                ]);

        $setFlagAlamt = $update = DB::table('supplier_detail')
                                ->where('id', $id)
                                ->update([
                                    'default' => 'Y'
                                ]);

        $log = ActionLog::create([
                'module' => 'Supplier',
                'action' => 'Set Default',
                'desc' => 'Set Default Alamat Supplier',
                'username' => Auth::user()->user_name
            ]);

        return response()->json("success");
    }

    public function EditSupplierAddress(Request $request)
    {
        $id = $request->input('idAddress');

        $dataAddress = SupplierDetail::find($id);

        return response()->json($dataAddress);
    }

    public function DeleteSupplierAddress(Request $request)
    {
        $id = $request->input('idAddress');

        $delete = DB::table('supplier_detail')->where('id', '=', $id)->delete();

        return response()->json("success");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supp'=>'required',
            'npwp_suppp1'=>'required',
            'npwp_suppp2'=>'required',
            'npwp_suppp3'=>'required',
            'npwp_suppp4'=>'required',
            'npwp_suppp5'=>'required',
            'npwp_suppp6'=>'required'
        ]);

        $nm = strtoupper($request->input('nama_supp'));
        $kategori = $request->input('kategori_supp');
        $npwpP1 = $request->input('npwp_suppp1');
        $npwpP2 = $request->input('npwp_suppp2');
        $npwpP3 = $request->input('npwp_suppp3');
        $npwpP4 = $request->input('npwp_suppp4');
        $npwpP5 = $request->input('npwp_suppp5');
        $npwpP6 = $request->input('npwp_suppp6');
        $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
        $email = $request->input('email_supp');
        $head_telp_supp = $request->input('head_telp_supp');
        $body_telp_supp = $request->input('body_telp_supp');
        $head_fax_supp = $request->input('head_fax_supp');
        $body_fax_supp = $request->input('body_fax_supp');
        $telp_supp = $request->input('telp_supp');
        $fax_supp = $request->input('fax_supp');
        // $telp_supp = $head_telp_supp."-".$body_telp_supp;
        // $fax_supp = $head_fax_supp."-".$body_fax_supp;
        $user = Auth::user()->user_name;

        $initKode = DB::table('supplier_category')->select('kode_kategori')->where('id', $kategori)->first();
        $kode = $initKode->kode_kategori;
        $countKode = DB::table('supplier')->select(DB::raw("MAX(RIGHT(kode_supplier,4)) AS angka"))->where('kategori_supplier', $kategori)->first();
        $count = $countKode->angka;
        $counter = $count + 1;

        if ($counter < 10) {
          $kode_supp = 's'.$kode.'000'.$counter;
        }
        elseif ($counter < 100) {
          $kode_supp = 's'.$kode.'00'.$counter;
        }
        elseif ($counter < 1000) {
          $kode_supp = 's'.$kode.'0'.$counter;
        }

        $supplier = Supplier::firstOrCreate(
            ['kode_supplier' => $kode_supp],
            [
                'nama_supplier' => $nm,
                'npwp_supplier' => $npwp,
                'telp_supplier' => $telp_supp,
                'fax_supplier' => $fax_supp,
                'email_supplier' => $email,
                'kategori_supplier' => $kategori,
                'created_by' => $user
            ]
        );

        $setAlamat = DB::table('supplier_detail')
                         ->where([
                                    ['id_supplier', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_supplier' => $supplier->id,
                            'updated_by' => $user
                        ]);

        $setProduct = DB::table('supplier_product')
                         ->where([
                                    ['id_supplier', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_supplier' => $supplier->id,
                            'updated_by' => $user
                        ]);

        //Generate Sub-Account jika terdapat account HUTANG DAGANG
        $cekAccount = GLAccount::where('account_name', '=', 'HUTANG LANCAR')->orderBy('id','asc')->first();
        if ($cekAccount != null) {
            $lastSubAccount = GLSubAccount::where([
                ['id_account', '=', $cekAccount->id]
            ])
            ->orderBy('order_number', 'desc')
            ->first();

            $nmAccount = $supplier->nama_supplier;

            if ($lastSubAccount != null) {
                $kodePiutang = explode("-", $lastSubAccount->account_number);
                $nmrAccount = $kodePiutang[0].'-'.str_pad($kodePiutang[1] + 1 , 4 , "0" , STR_PAD_LEFT);


                $subAccount = new GLSubAccount();
                $subAccount->account_name = $nmAccount;
                $subAccount->account_number = $nmrAccount;
                $subAccount->id_mother_account = $lastSubAccount->id_mother_account;
                $subAccount->id_account = $lastSubAccount->id_account;
                $subAccount->order_number = $lastSubAccount->order_number + 1;
                $subAccount->created_by = $user;
                $subAccount->save();

                $supplier->id_account = $subAccount->id;
                $supplier->save();
            }
            else {
                $nmrAccount = str_replace('-','',$cekAccount->account_number).'-'.str_pad(1 , 4 , "0" , STR_PAD_LEFT);
                $subAccount = new GLSubAccount();
                $subAccount->account_name = $nmAccount;
                $subAccount->account_number = $nmrAccount;
                $subAccount->id_mother_account = $cekAccount->id_mother_account;
                $subAccount->id_account = $cekAccount->id;
                $subAccount->order_number = 1;
                $subAccount->created_by = $user;
                $subAccount->save();

                $supplier->id_account = $subAccount->id;
                $supplier->save();
            }
        }

        if ($supplier->wasRecentlyCreated) {
            return redirect('/Supplier')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return redirect('/Supplier')->with('error', 'Kode '.strtoupper($nm).' Telah Digunakan!');
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'nama_supp'=>'required',
            'npwp_suppp1'=>'required',
            'npwp_suppp2'=>'required',
            'npwp_suppp3'=>'required',
            'npwp_suppp4'=>'required',
            'npwp_suppp5'=>'required',
            'npwp_suppp6'=>'required',
            //'kategori_supp'=> 'required'
        ]);

        $kd = strtolower($request->input('kode_supp'));
        $nm = strtoupper($request->input('nama_supp'));
        //$kategori = $request->input('kategori_supp');
        $npwpP1 = $request->input('npwp_suppp1');
        $npwpP2 = $request->input('npwp_suppp2');
        $npwpP3 = $request->input('npwp_suppp3');
        $npwpP4 = $request->input('npwp_suppp4');
        $npwpP5 = $request->input('npwp_suppp5');
        $npwpP6 = $request->input('npwp_suppp6');
        $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
        $email = $request->input('email_supp');
        $head_telp_supp = $request->input('head_telp_supp');
        $body_telp_supp = $request->input('body_telp_supp');
        $head_fax_supp = $request->input('head_fax_supp');
        $body_fax_supp = $request->input('body_fax_supp');
        $telp_supp = $request->input('telp_supp');
        $fax_supp = $request->input('fax_supp');
        // $telp_supp = $head_telp_supp."-".$body_telp_supp;
        // $fax_supp = $head_fax_supp."-".$body_fax_supp;
        $user = Auth::user()->user_name;

        $update = Supplier::find($id);

        $update->kode_supplier = $kd;
        $update->nama_supplier = $nm;
        $update->npwp_supplier = $npwp;
        $update->telp_supplier = $telp_supp;
        $update->fax_supplier = $fax_supp;
        $update->email_supplier = $email;
        $update->updated_by = $user;
        $update->save();

        $deleteDetailSupplier = DB::table('supplier_product')
                                    ->where([
                                        ['id_supplier', '=', $id],
                                        ['deleted_at', '!=', null]
                                    ])
                                    ->delete();

        //Update Sub-Account Name jika terdapat account
        if ($update->id_account != null) {
            $subAccount = GLSubAccount::find($update->id_account);

            if ($subAccount != null) {
                $nmAccount = $update->nama_supplier;
                $subAccount->account_name = $nmAccount;
                $subAccount->updated_by = $user;
                $subAccount->save();
            }
            else {
                $nmAccount = $update->nama_supplier;
                $subAccount->account_name = $nmAccount;
                $subAccount->updated_by = $user;
                $subAccount->save();
            }
        }
        else {
            //Generate Sub-Account jika terdapat account HUTANG DAGANG
            $cekAccount = GLAccount::where('account_name', '=', 'HUTANG LANCAR')->orderBy('id','asc')->first();
            if ($cekAccount != null) {
                $lastSubAccount = GLSubAccount::where([
                    ['id_account', '=', $cekAccount->id]
                ])
                ->orderBy('order_number', 'desc')
                ->first();

                $nmAccount = $update->nama_supplier;

                if ($lastSubAccount != null) {
                    $kodePiutang = explode("-", $lastSubAccount->account_number);
                    $nmrAccount = $kodePiutang[0].'-'.str_pad($kodePiutang[1] + 1 , 4 , "0" , STR_PAD_LEFT);


                    $subAccount = new GLSubAccount();
                    $subAccount->account_name = $nmAccount;
                    $subAccount->account_number = $nmrAccount;
                    $subAccount->id_mother_account = $lastSubAccount->id_mother_account;
                    $subAccount->id_account = $lastSubAccount->id_account;
                    $subAccount->order_number = $lastSubAccount->order_number + 1;
                    $subAccount->created_by = $user;
                    $subAccount->save();

                    $update->id_account = $subAccount->id;
                    $update->save();
                }
                else {
                    $nmrAccount = str_replace('-','',$cekAccount->account_number).'-'.str_pad(1 , 4 , "0" , STR_PAD_LEFT);
                    $subAccount = new GLSubAccount();
                    $subAccount->account_name = $nmAccount;
                    $subAccount->account_number = $nmrAccount;
                    $subAccount->id_mother_account = $cekAccount->id_mother_account;
                    $subAccount->id_account = $cekAccount->id;
                    $subAccount->order_number = 1;
                    $subAccount->created_by = $user;
                    $subAccount->save();

                    $update->id_account = $subAccount->id;
                    $update->save();
                }
            }
        }

        if($update) {
            return redirect('/Supplier')->with('success', 'Data '.strtoupper($nm).' Berhasil Diupdate!');
        }
        else {
            return redirect('/Supplier')->with('danger', 'Data '.strtoupper($nm).' Gagal Diupdate!');
        }
    }

    public function delete(Request $request)
    {

        $id = $request->input('id_supplier');
        $user = Auth::user()->user_name;
        $cekPo = PurchaseOrder::where([
                                        ['id_supplier', '=', $id]
                                    ])
                                    ->count();

        if ($cekPo > 0) {
            return response()->json(['failUsed']);
        }
        else {

            $delete = Supplier::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Supplier',
                'action' => 'Delete',
                'desc' => 'Delete Supplier',
                'username' => Auth::user()->user_name
            ]);
            return response()->json('success');
        }
    }

    public function supplierItems(Request $request)
    {
        $id = $request->input('idSupp');

        if ($id == "") {
            $id = 'DRAFT';
        }

        $hargaBeliTerakhir = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                ->select('id_item', DB::raw("harga_beli AS harga_beli_last"))
                                                // ->whereIn('purchase_order.tanggal_po', function($querySub) use ($id) {
                                                //     $querySub->select(DB::raw("MAX(purchase_order.tanggal_po)"))->from("purchase_order")
                                                //             ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                //             ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                //             ->whereNotIn('purchase_order.status_so', ['draft', 'cancel'])
                                                //             ->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                //             ->where([
                                                //                 ['purchase_order.id_supplier', '=', $id],
                                                //                 // ['purchase_order_detail.id_item', '=', $idProduct]
                                                //             ]);
                                                //             //->groupBy('purchase_order_detail.id_item');
                                                // })
                                                ->where([
                                                    ['purchase_order.id_supplier', '=', $id],
                                                ])
                                                ->groupBy('purchase_order_detail.id_item')
                                                ->groupBy('purchase_order.tanggal_po')
                                                ->orderBy('purchase_order.tanggal_po', 'desc');

        $dataProductSupplier = SupplierProduct::leftJoin('product', 'supplier_product.id_item', '=', 'product.id')
                                                        ->leftJoinSub($hargaBeliTerakhir, 'hargaBeliTerakhir', function($hargaBeliTerakhir) {
                                                            $hargaBeliTerakhir->on('product.id', '=', 'hargaBeliTerakhir.id_item');
                                                        })
                                                        ->select(
                                                            'supplier_product.id',
                                                            'product.kode_item',
                                                            'product.nama_item',
                                                            DB::raw("COALESCE(hargaBeliTerakhir.harga_beli_last, product.harga_beli) AS harga_beli_last")
                                                        )
                                                        ->where('id_supplier', $id)
                                                        ->get();

        return response()->json($dataProductSupplier);
    }

    public function getProduct(Request $request)
    {
        $idSupplier = $request->input('id_supplier');
        $dataProduct = "";
        if ($idSupplier == "") {
            $idSupplier = 'DRAFT';
        }

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
                                        'product.*',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->whereNOTIn('product.id', function($query) use ($idSupplier) {
                                        $query->select('id_item')->from('supplier_product')
                                        ->when($idSupplier == "DRAFT", function($q) use ($idSupplier) {
                                            $q->where([
                                                ['id_supplier', '=', $idSupplier],
                                                ['created_by', '=', Auth::user()->user_name]
                                            ]);
                                        })
                                        ->when($idSupplier != "DRAFT", function($q) use ($idSupplier) {
                                            $q->where([
                                                ['id_supplier', '=', $idSupplier]
                                            ]);
                                        });
                                    })
                                    ->get();

        }

        return response()->json($dataProduct);
    }

    public function addSupplierProduct(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idSupplier = $request->input('id_supplier');
            $idItem = $request->input('id_item');

            if ($idSupplier == "") {
                $idSupplier = 'DRAFT';
            }


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

    public function DeleteSupplierItem(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode == "edit") {
            $data = SupplierProduct::find($id);
            $data->deleted_by = $user;
            $data->save();
            $data->delete();
        }
        else {
            $delete = DB::table('supplier_product')->where('id', '=', $id)->delete();
        }

        return response()->json("success");
    }
}
