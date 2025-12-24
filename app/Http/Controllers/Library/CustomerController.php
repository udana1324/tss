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
use App\Models\Library\Customer;
use App\Models\Library\CustomerCategory;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerProduct;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Models\Library\Sales;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Setting\Module;
use Illuminate\Validation\Rule;
use stdClass;

class CustomerController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Customer'],
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
                                                ['module.url', '=', '/Customer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataKota = CustomerDetail::distinct()->get('kota');
                $dataKategori = CustomerCategory::distinct()->get('nama_kategori');

                $data['hakAkses'] = $hakAkses;
                $data['dataKota'] = $dataKota;
                $data['dataKategori'] = $dataKategori;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Customer',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.customer.index', $data);
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
        $listOutlet = CustomerDetail::select('customer_detail.id_customer',
                                              DB::raw("GROUP_CONCAT(customer_detail.nama_outlet SEPARATOR ',') as list_outlets")
                                            )
                                    ->groupBy('customer_detail.id_customer');

        $customer = Customer::leftJoin('customer_category', 'customer.kategori_customer', '=', 'customer_category.id')
                            ->leftJoin('customer_detail', 'customer.id', '=', 'customer_detail.id_customer')
                            ->leftJoinSub($listOutlet, 'listOutlet', function($listOutlet) {
                                $listOutlet->on('customer.id', '=', 'listOutlet.id_customer');
                            })
                            ->select(
                                'customer.id',
                                'customer.kode_customer',
                                'customer.nama_customer',
                                'customer.npwp_customer',
                                'customer.ktp_customer',
                                'customer.telp_customer',
                                'customer.fax_customer',
                                'customer.email_customer',
                                'customer.kategori_customer',
                                'customer.jenis_customer',
                                'customer_detail.kota',
                                'customer_category.nama_kategori',
                                DB::raw("COALESCE(listOutlet.list_outlets, '-') AS list_outlets"))
                            ->where([
                                ['customer_detail.default', '=', 'Y']
                            ])
                            ->orderBy('customer.id', 'desc')
                            ->get();
        return response()->json($customer);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Customer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == 'Y') {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $delete = DB::table('customer_detail')->where('id_customer', '=', 'DRAFT')->delete();
                $delete = DB::table('customer_product')->where('id_customer', '=', 'DRAFT')->delete();

                $customerCategory = CustomerCategory::all();
                $dataKota = CustomerDetail::distinct()->get('kota');
                $sales = Sales::all();
                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $product = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();

                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Customer',
                    'username' => Auth::user()->user_name
                ]);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

                $data['customerCategory'] = $customerCategory;
                $data['sales'] = $sales;
                $data['product'] = $product;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.customer.add', $data);
            }
            else {
                return redirect('/Customer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Customer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == 'Y') {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $delete = DB::table('customer_detail')->where('id_customer', '=', 'DRAFT')->delete();

                $customerCategory = CustomerCategory::all();
                $dataKota = CustomerDetail::distinct()->get('kota');
                $sales = Sales::all();
                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $product = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();
                $dataCustomer = Customer::find($id);
                $dataProductCustomer = CustomerProduct::where('id_customer', $id)->get();

                $restore = CustomerProduct::onlyTrashed()->where([['id_customer', '=', $id]]);
                $restore->restore();


                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Customer',
                    'username' => Auth::user()->user_name
                ]);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

                $data['customerCategory'] = $customerCategory;
                $data['sales'] = $sales;
                $data['product'] = $product;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataProductCustomer'] = $dataProductCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.customer.edit', $data);
            }
            else {
                return redirect('/Customer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Customer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Customer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $delete = DB::table('customer_detail')->where('id_customer', '=', 'DRAFT')->delete();

                $customerCategory = CustomerCategory::all();
                $dataKota = CustomerDetail::distinct()->get('kota');
                $sales = Sales::all();

                $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
                                                // ->whereIn('sales_order.tanggal_so', function($querySub) use ($id) {
                                                //     $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
                                                //             ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                //             ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                //             ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
                                                //             ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                //             ->where([
                                                //                 ['sales_order.id_customer', '=', $id],
                                                //                 // ['sales_order_detail.id_item', '=', $idProduct]
                                                //             ]);
                                                //             //->groupBy('sales_order_detail.id_item');
                                                // })
                                                ->where([
                                                    ['sales_order.id_customer', '=', $id],
                                                ])
                                                ->groupBy('sales_order_detail.id_item')
                                                ->groupBy('sales_order_detail.id_satuan')
                                                ->groupBy('sales_order.tanggal_so')
                                                ->orderBy('sales_order.tanggal_so', 'desc');

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $product = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();
                $dataCustomer = Customer::find($id);

                $dataProductCustomer = CustomerProduct::distinct()
                                                        ->leftJoin('product', 'customer_product.id_item', '=', 'product.id')
                                                        ->leftJoin('product_detail', 'product.id', '=', 'product_detail.id_product')
                                                        ->leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                                        // ->leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($hargaJualTerakhir) {
                                                        //     $hargaJualTerakhir->on('product.id', '=', 'hargaJualTerakhir.id_item');
                                                        // })
                                                        ->leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($join_in) {
                                                                $join_in->on('product_detail.id_product', '=', 'hargaJualTerakhir.id_item');
                                                                $join_in->on('product_detail.id_satuan', '=', 'hargaJualTerakhir.id_satuan');
                                                            })
                                                        ->select(
                                                            'product.*',
                                                            'product_unit.nama_satuan',
                                                            'customer_product.id_item',
                                                            'customer_product.id_customer',
                                                            DB::raw("COALESCE(hargaJualTerakhir.harga_jual_last,0) AS harga_jual_last")
                                                        )
                                                        ->where([
                                                            ['customer_product.id_customer', '=', $id],
                                                            ['product_detail.deleted_at', '=', null],
                                                        ])
                                                        ->get();



                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'Detail',
                    'desc' => 'Detail Customer',
                    'username' => Auth::user()->user_name
                ]);

                $data['customerCategory'] = $customerCategory;
                $data['sales'] = $sales;
                $data['product'] = $product;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataProductCustomer'] = $dataProductCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.customer.detail', $data);
            }
            else {
                return redirect('/Customer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function history($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Customer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Customer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataSalesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                            'customer.nama_customer',
                                            'sales_order.id',
                                            'sales_order.no_so',
                                            'sales_order.jumlah_total_so',
                                            'sales_order.outstanding_so',
                                            'sales_order.tanggal_so',
                                            'sales_order.tanggal_request',
                                            'sales_order.nominal_so_ttl',
                                            'sales_order.flag_revisi',
                                            'sales_order.status_so')
                                        ->where([
                                            ['sales_order.id_customer', '=', $id]
                                        ])
                                        ->orderBy('sales_order.id', 'desc')
                                        ->get();

                $dataDelivery = Delivery::leftJoin('sales_order', 'delivery.id_so', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->select(
                                        'customer.nama_customer',
                                        'sales_order.no_so',
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.no_sj_manual',
                                        'delivery.jumlah_total_sj',
                                        'delivery.tanggal_sj',
                                        'delivery.tanggal_kirim',
                                        'delivery.metode_pengiriman',
                                        'delivery.flag_revisi',
                                        'delivery.status_pengiriman')
                                    ->where([
                                        ['sales_order.id_customer', '=', $id]
                                    ])
                                    ->orderBy('delivery.id', 'desc')
                                    ->get();

                $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                                ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                                ->select(
                                                    'customer.nama_customer',
                                                    'sales_order.no_so',
                                                    'sales_invoice.id',
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.dpp',
                                                    'sales_invoice.ppn',
                                                    'sales_invoice.grand_total',
                                                    'sales_invoice.ttl_qty',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'sales_invoice.flag_revisi',
                                                    'sales_invoice.flag_tf',
                                                    'sales_invoice.flag_pembayaran',
                                                    'sales_invoice.status_invoice')
                                                ->where([
                                                    ['sales_order.id_customer', '=', $id]
                                                ])
                                                ->orderBy('sales_invoice.id', 'desc')
                                                ->get();

                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'History',
                    'desc' => 'History Customer',
                    'username' => Auth::user()->user_name
                ]);

                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataDelivery'] = $dataDelivery;
                $data['dataSalesInvoice'] = $dataSalesInvoice;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.customer.history', $data);
            }
            else {
                return redirect('/Customer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function customerItems(Request $request)
    {
        $id = $request->input('idCust');

        if ($id == "") {
            $id = 'DRAFT';
        }

        $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                ->select('id_item', DB::raw("harga_jual AS harga_jual_last"))
                                                // ->whereIn('sales_order.tanggal_so', function($querySub) use ($id) {
                                                //     $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
                                                //             ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                //             ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                //             ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
                                                //             ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                //             ->where([
                                                //                 ['sales_order.id_customer', '=', $id],
                                                //                 // ['sales_order_detail.id_item', '=', $idProduct]
                                                //             ]);
                                                //             //->groupBy('sales_order_detail.id_item');
                                                // })
                                                ->where([
                                                    ['sales_order.id_customer', '=', $id],
                                                ])
                                                ->groupBy('sales_order_detail.id_item')
                                                ->groupBy('sales_order.tanggal_so')
                                                ->orderBy('sales_order.tanggal_so', 'desc');

        $dataProductCustomer = CustomerProduct::leftJoin('product', 'customer_product.id_item', '=', 'product.id')
                                                        ->leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($hargaJualTerakhir) {
                                                            $hargaJualTerakhir->on('product.id', '=', 'hargaJualTerakhir.id_item');
                                                        })
                                                        ->select(
                                                            'customer_product.id',
                                                            'product.kode_item',
                                                            'product.nama_item',
                                                            DB::raw("COALESCE(hargaJualTerakhir.harga_jual_last, 0) AS harga_jual_last")
                                                        )
                                                        ->where('id_customer', $id)
                                                        ->get();

        return response()->json($dataProductCustomer);
    }

    public function detail_barang($id)
    {
        if (Auth::check()) {

            $countHakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Customer'],
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
                                            ['module.url', '=', '/Customer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

                $dataCustomer = Customer::find($id);

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataPenjualan = Delivery::leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', 'delivery.id')
                                        ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                        ->leftJoin('sales_order_detail', function($join) {
                                            $join->on('sales_order.id' , '=', 'sales_order_detail.id_so');
                                            $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                            $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                        })
                                        ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                        ->leftjoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', '=', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.id',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'customer.nama_customer',
                                            'sales_order.no_so',
                                            'sales_order.no_po_customer',
                                            'sales_order_detail.harga_jual',
                                            'delivery.tanggal_sj',
                                            'delivery.kode_pengiriman',
                                            'delivery.status_pengiriman',
                                            'delivery.flag_terkirim',
                                            'delivery_detail.qty_item',
                                            'sales_invoice.kode_invoice',
                                            'sales_invoice.flag_tf',
                                            'sales_invoice.status_invoice',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['customer.id', '=', $id]
                                        ])
                                        ->orderBy('delivery.id', 'desc')
                                        ->get();


                $data['hakAkses'] = $hakAkses;
                $data['userGroup'] = Auth::user()->user_group;
                $data['dataPenjualan'] = $dataPenjualan;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer',
                    'action' => 'History',
                    'desc' => 'History Customer Product',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.customer.detail_product', $data);
            }
            else {
                return redirect('/Product')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function StoreCustomerAddress(Request $request)
    {
        $id = $request->input('idAlamat');
        $idCustomer = $request->input('idCustomer');
        $namaOutlet = $request->input('namaOutlet');
        $alamat = $request->input('alamat');
        $kecamatan = $request->input('kecamatan');
        $kelurahan = $request->input('kelurahan');
        $kota = $request->input('kota');
        $kodePos = $request->input('kodePos');
        $jenisAlamat = $request->input('jenisAlamat');
        $pic = $request->input('pic');
        $noPic = $request->input('noPic');
        $user = Auth::user()->user_name;

        if ($idCustomer == "") {
            $idCustomer = 'DRAFT';
        }

        $countKode = DB::table('customer_detail')->select(DB::raw("COUNT(*) AS angka"))->where([['id_customer', '=' , $idCustomer], ['jenis_alamat', '=', 'NPWP']])->first();
        $count = $countKode->angka;

        $countDef = DB::table('customer_detail')->select(DB::raw("COUNT(*) AS angkaDef"))->where([['id_customer', '=' , $idCustomer], ['default', '=', 'Y']])->first();
        $countDefault = $countDef->angkaDef;

        if ($countDefault > 0) {
            $flagDef = 'N';
        }
        else {
            $flagDef = 'Y';
        }

        if ($id != "") {
            $getFlag = DB::table('customer_detail')->select('default')->where([['id', '=' , $id]])->first();
            $flagDef = $getFlag->default;
        }

        if ($count > 0 && $jenisAlamat == "NPWP" && $id == "") {
            return response()->json("failNpwp");
        }
        else {
            CustomerDetail::updateOrCreate(
                ['id' => $id],
                [
                    'id_customer' => $idCustomer,
                    'nama_outlet' => $namaOutlet,
                    'alamat_customer' => $alamat,
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
                'module' => 'Customer',
                'action' => 'Simpan',
                'desc' => 'Simpan Alamat Customer',
                'username' => Auth::user()->user_name
            ]);

            return response()->json("success");
        }
    }

    public function GetCustomerAddress(Request $request)
    {
        $idCustomer = $request->input('idCustomer');
        if ($idCustomer == "") {
            $idCustomer = 'DRAFT';
        }

        $displayAddress = CustomerDetail::where([
                                            ['id_customer', '=', $idCustomer]
                                        ])
                                        ->get();

        return response()->json($displayAddress);
    }

    public function SetDefaultAddress(Request $request)
    {
        $id = $request->input('idAddress');
        $idCustomer = $request->input('idCustomer');

        if ($idCustomer == "") {
            $idCustomer = 'DRAFT';
        }

        $updateFlagAlamt = $update = DB::table('customer_detail')
                                ->where('id_customer', $idCustomer)
                                ->update([
                                    'default' => 'N'
                                ]);

        $setFlagAlamt = $update = DB::table('customer_detail')
                                ->where('id', $id)
                                ->update([
                                    'default' => 'Y'
                                ]);

        $log = ActionLog::create([
                'module' => 'Customer',
                'action' => 'Set Default',
                'desc' => 'Set Default Alamat Customer',
                'username' => Auth::user()->user_name
            ]);

        return response()->json("success");
    }

    public function EditCustomerAddress(Request $request)
    {
        $id = $request->input('idAddress');

        $dataAddress = CustomerDetail::find($id);

        return response()->json($dataAddress);
    }

    public function getProduct(Request $request)
    {
        $idCustomer = $request->input('id_customer');
        $dataProduct = "";
        if ($idCustomer == "") {
            $idCustomer = 'DRAFT';
        }

        if ($idCustomer != "") {
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
                                    ->whereNOTIn('product.id', function($query) use ($idCustomer) {
                                        $query->select('id_item')->from('customer_product')
                                        ->when($idCustomer == "DRAFT", function($q) use ($idCustomer) {
                                            $q->where([
                                                ['id_customer', '=', $idCustomer],
                                                ['created_by', '=', Auth::user()->user_name]
                                            ]);
                                        })
                                        ->when($idCustomer != "DRAFT", function($q) use ($idCustomer) {
                                            $q->where([
                                                ['id_customer', '=', $idCustomer]
                                            ]);
                                        });
                                    })
                                    ->get();

        }

        return response()->json($dataProduct);
    }

    public function addCustomerProduct(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('id_customer');
            $idItem = $request->input('id_item');

            if ($idCustomer == "") {
                $idCustomer = 'DRAFT';
            }


            $customerProduct = new CustomerProduct();
            $customerProduct->id_customer = $idCustomer;
            $customerProduct->id_item = $idItem;
            $customerProduct->created_by = Auth::user()->user_name;
            $customerProduct->save();
            $data = $customerProduct;

        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function DeleteCustomerAddress(Request $request)
    {
        $id = $request->input('idAddress');

        $delete = DB::table('customer_detail')->where('id', '=', $id)->delete();

        return response()->json("success");
    }

    public function DeleteCustomerItem(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode == "edit") {
            $data = CustomerProduct::find($id);
            $data->deleted_by = $user;
            $data->save();
            $data->delete();
        }
        else {
            $delete = DB::table('customer_product')->where('id', '=', $id)->delete();
        }

        return response()->json("success");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cust'=>'required',
            'jenis_customer'=>'required',
            'ktp_cust' => Rule::requiredIf($request->input('jenis_customer') == "I"),
            'npwp_custp1'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp2'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp3'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp4'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp5'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp6'=> Rule::requiredIf($request->input('jenis_customer') == "C")
        ]);

        $nm = strtoupper($request->input('nama_cust'));
        $kategori = $request->input('kategori_cust');
        $npwpP1 = $request->input('npwp_custp1');
        $npwpP2 = $request->input('npwp_custp2');
        $npwpP3 = $request->input('npwp_custp3');
        $npwpP4 = $request->input('npwp_custp4');
        $npwpP5 = $request->input('npwp_custp5');
        $npwpP6 = $request->input('npwp_custp6');
        $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
        $npwp16P1 = $request->input('npwp16_cust1');
        $npwp16P2 = $request->input('npwp16_cust2');
        $npwp16P3 = $request->input('npwp16_cust3');
        $npwp16P4 = $request->input('npwp16_cust4');
        $npwp16 = $npwp16P1.".".$npwp16P2.".".$npwp16P3.".".$npwp16P4;
        $email = $request->input('email_cust');
        $head_telp_cust = $request->input('head_telp_cust');
        $body_telp_cust = $request->input('body_telp_cust');
        $head_fax_cust = $request->input('head_fax_cust');
        $body_fax_cust = $request->input('body_fax_cust');
        $telp_cust = $request->input('telp_cust');
        $fax_cust = $request->input('fax_cust');
        $limitCust = $request->input('limit_cust');
        $sales = $request->input('sales');
        $ktp = $request->input('ktp_cust');
        $jenisCust = $request->input('jenis_customer');
        // $telp_cust = $head_telp_cust."-".$body_telp_cust;
        // $fax_cust = $head_fax_cust."-".$body_fax_cust;
        $user = Auth::user()->user_name;


        $initKode = DB::table('customer_category')->select('kode_kategori')->where('id', $kategori)->first();
        $kode = $initKode->kode_kategori;
        $countKode = DB::table('customer')->select(DB::raw("MAX(RIGHT(kode_customer,4)) AS angka"))->where('kategori_customer', $kategori)->first();
        $count = $countKode->angka;
        $counter = $count + 1;

        if ($counter < 10) {
          $kode_cust = 'c'.$kode.'000'.$counter;
        }
        elseif ($counter < 100) {
          $kode_cust = 'c'.$kode.'00'.$counter;
        }
        elseif ($counter < 1000) {
          $kode_cust = 'c'.$kode.'0'.$counter;
        }

        $customer = new Customer();
        $customer->kode_customer = $kode_cust;
        $customer->nama_customer = $nm;
        $customer->npwp_customer = $npwp;
        $customer->npwp_customer_16 = $npwp16;
        $customer->ktp_customer = $ktp;
        $customer->telp_customer = $telp_cust;
        $customer->fax_customer = $fax_cust;
        $customer->email_customer = $email;
        $customer->kategori_customer = $kategori;
        $customer->jenis_customer = $jenisCust;
        $customer->limit_customer = $limitCust;
        $customer->sales = $sales;
        $customer->created_by = $user;
        $customer->save();

        $arrayItem = $request->input('product');
        if ($arrayItem != "") {

            $countDetail = count($arrayItem);
            $listDetailSupplier = [];
            for ($j = 0; $j < $countDetail; $j++) {
                $dataSupplier=[
                    'id_customer' => $customer->id,
                    'id_item' => $arrayItem[$j],
                    'created_by' => $user,
                    'created_at' => now()
                ];
                array_push($listDetailSupplier, $dataSupplier);
            }

            CustomerProduct::insert($listDetailSupplier);
        }

        $setAlamat = DB::table('customer_detail')
                         ->where([
                                    ['id_customer', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_customer' => $customer->id,
                            'updated_by' => $user
                        ]);

        $setProduct = DB::table('customer_product')
                         ->where([
                                    ['id_customer', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_customer' => $customer->id,
                            'updated_by' => $user
                        ]);

        //Generate Sub-Account jika terdapat account PIUTANG
        $cekAccount = GLAccount::where('account_name', '=', 'PIUTANG')->first();
        if ($cekAccount != null) {
            $lastSubAccount = GLSubAccount::where([
                ['id_account', '=', $cekAccount->id]
            ])
            ->orderBy('order_number', 'desc')
            ->first();

            $nmAccount = $customer->nama_customer;

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

                $customer->id_account = $subAccount->id;
                $customer->save();
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

                $customer->id_account = $subAccount->id;
                $customer->save();
            }
        }

        if ($customer) {
            return redirect('/Customer')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return redirect('/Customer')->with('error', 'Kode '.strtoupper($nm).' Telah Digunakan!');
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'nama_cust'=>'required',
            'jenis_customer'=>'required',
            'ktp_cust' => Rule::requiredIf($request->input('jenis_customer') == "I"),
            'npwp_custp1'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp2'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp3'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp4'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp5'=> Rule::requiredIf($request->input('jenis_customer') == "C"),
            'npwp_custp6'=> Rule::requiredIf($request->input('jenis_customer') == "C")
        ]);

        $kd = strtolower($request->input('kode_cust'));
        $nm = strtoupper($request->input('nama_cust'));
        //$kategori = $request->input('kategori_cust');
        $npwpP1 = $request->input('npwp_custp1');
        $npwpP2 = $request->input('npwp_custp2');
        $npwpP3 = $request->input('npwp_custp3');
        $npwpP4 = $request->input('npwp_custp4');
        $npwpP5 = $request->input('npwp_custp5');
        $npwpP6 = $request->input('npwp_custp6');
        $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
        $npwp16P1 = $request->input('npwp16_cust1');
        $npwp16P2 = $request->input('npwp16_cust2');
        $npwp16P3 = $request->input('npwp16_cust3');
        $npwp16P4 = $request->input('npwp16_cust4');
        $npwp16 = $npwp16P1.".".$npwp16P2.".".$npwp16P3.".".$npwp16P4;
        $email = $request->input('email_cust');
        $head_telp_cust = $request->input('head_telp_cust');
        $body_telp_cust = $request->input('body_telp_cust');
        $head_fax_cust = $request->input('head_fax_cust');
        $body_fax_cust = $request->input('body_fax_cust');
        $telp_cust = $request->input('telp_cust');
        $fax_cust = $request->input('fax_cust');
        $sales = $request->input('sales');
        $ktp = $request->input('ktp_cust');
        $jenisCust = $request->input('jenis_customer');
        // $telp_cust = $head_telp_cust."-".$body_telp_cust;
        // $fax_cust = $head_fax_cust."-".$body_fax_cust;
        $user = Auth::user()->user_name;

        $update = Customer::find($id);

        $update->kode_customer = $kd;
        $update->nama_customer = $nm;
        $update->ktp_customer = $ktp;
        $update->npwp_customer = $npwp;
        $update->npwp_customer_16 = $npwp16;
        $update->telp_customer = $telp_cust;
        $update->fax_customer = $fax_cust;
        $update->jenis_customer = $jenisCust;
        $update->sales = $sales;
        $update->email_customer = $email;
        $update->updated_by = $user;
        $update->save();

        $deleteDetailSupplier = DB::table('customer_product')
                                    ->where([
                                        ['id_customer', '=', $id],
                                        ['deleted_at', '!=', null]
                                    ])
                                    ->delete();


        //Update Sub-Account Name jika terdapat account
        if ($update->id_account != null) {
            $subAccount = GLSubAccount::find($update->id_account);

            if ($subAccount != null) {
                $nmAccount = $update->nama_customer;
                $subAccount->account_name = $nmAccount;
                $subAccount->updated_by = $user;
                $subAccount->save();
            }
            else {
                $nmAccount = $update->nama_customer;
                $subAccount->account_name = $nmAccount;
                $subAccount->updated_by = $user;
                $subAccount->save();
            }
        }
        else {
            //Generate Sub-Account jika terdapat account PIUTANG
            $cekAccount = GLAccount::where('account_name', '=', 'PIUTANG')->first();
            if ($cekAccount != null) {
                $lastSubAccount = GLSubAccount::where([
                    ['id_account', '=', $cekAccount->id]
                ])
                ->orderBy('order_number', 'desc')
                ->first();

                $nmAccount = $update->nama_customer;

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
            return redirect('/Customer')->with('success', 'Data '.strtoupper($nm).' Berhasil Diupdate!');
        }
        else {
            return redirect('/Customer')->with('danger', 'Data '.strtoupper($nm).' Gagal Diupdate!');
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('idCustomer');
        $user = Auth::user()->user_name;

        $cekSO = SalesOrder::where([
                                        ['id_customer', '=', $id]
                                    ])
                                    ->count();

        if ($cekSO > 0) {
            return response()->json(['failUsed']);
        }
        else {

            $delete = Customer::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Customer',
                'action' => 'Delete',
                'desc' => 'Delete Customer',
                'username' => Auth::user()->user_name
            ]);
            return response()->json('success');
        }
     }
}
