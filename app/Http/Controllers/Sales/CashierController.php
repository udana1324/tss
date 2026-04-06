<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperQuotation;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\CompanyAccount;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationDetail;
use App\Models\Sales\QuotationTerms;
use App\Models\Library\Customer;
use App\Models\Product\Product;
use App\Models\Library\CustomerProduct;
use App\Models\Library\CustomerDetail;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductUnit;
use App\Models\Sales\SalesOrderDetail;
use App\Models\TempTransaction;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Sales\ProductSpecialPricing;
use App\Models\Sales\SalesCashier;
use App\Models\Sales\SalesCashierDetail;
use App\Models\Setting\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Session;
use stdClass;

class CashierController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductSpecialPricing'],
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
                                                ['module.url', '=', '/Cashier'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();


                $parentMenu = Module::find($hakAkses->parent);
                $group = Auth::user()->user_group;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['group'] = $group;

                $log = ActionLog::create([
                    'module' => 'Penjualan Cashier',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Penjualan Cashier',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.cashier.index', $data);
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
        $group = Auth::user()->user_group;
        $userID = Auth::user()->id;

        $transaction = SalesCashier::leftJoin('customer', 'sales_cashier.id_customer', '=', 'customer.id')
                        ->leftJoin('users', 'sales_cashier.id_user', '=', 'users.id')
                        ->select(
                            'customer.nama_customer',
                            'users.user_name',
                            'sales_cashier.id',
                            'sales_cashier.no_ref',
                            'sales_cashier.jumlah_total_qty',
                            'sales_cashier.tanggal_penjualan',
                            'sales_cashier.nominal_total',
                            'sales_cashier.flag_revisi',
                            'sales_cashier.metode_pembayaran',
                            'sales_cashier.flag_revisi',
                            'sales_cashier.status_sales')
                        ->when($periode != "", function($q) use ($periode) {
                            $q->whereMonth('sales_cashier.tanggal_penjualan', Carbon::parse($periode)->format('m'));
                            $q->whereYear('sales_cashier.tanggal_penjualan', Carbon::parse($periode)->format('Y'));
                        })
                        ->when($group == "cashier", function($q) use ($userID) {
                            $q->where('sales_cashier.id_user', $userID);
                        })
                        ->orderBy('sales_cashier.tanggal_penjualan', 'desc')
                        ->orderBy('sales_cashier.id', 'desc')
                        ->get();
        return response()->json($transaction);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Cashier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataProduct = Product::all();

                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.id',
                                                    'company_account.nomor_rekening',
                                                    'company_account.atas_nama',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $parentMenu = Module::find($hakAkses->parent);
                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataProduct'] = $dataProduct;
                $data['dataRekening'] = $dataRekening;

                $log = ActionLog::create([
                    'module' => 'Kasir',
                    'action' => 'Module Kasir',
                    'desc' => 'Module Kasir',
                    'username' => Auth::user()->user_name
                ]);

                // $delete = DB::table('sales_cashier_detail')
                //             ->where([
                //                 ['id_sc', '=', 'DRAFT'],
                //                 ['created_by', '=', Auth::user()->user_name]
                //             ])
                //             ->delete();

                return view('pages.sales.cashier.add', $data);
            }
            else {
                return redirect('/Cashier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Cashier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTransaction = SalesCashier::find($id);
                $dataCustomer = Customer::all();
                $dataProduct = Product::all();

                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.id',
                                                    'company_account.nomor_rekening',
                                                    'company_account.atas_nama',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $detail = SalesCashierDetail::leftJoin('product', 'sales_cashier_detail.id_item', 'product.id')
                                    ->leftJoin('product_detail', function($join) {
                                        $join->on('product_detail.id_product', '=', 'product.id');
                                        $join->on('product_detail.id_satuan', '=', 'sales_cashier_detail.id_satuan');
                                    })
                                    ->leftJoin('product_unit', 'product_detail.id_satuan', 'product_unit.id')
                                    ->select(
                                        'sales_cashier_detail.id',
                                        'sales_cashier_detail.id_item',
                                        'sales_cashier_detail.id_satuan',
                                        'sales_cashier_detail.qty_item',
                                        'sales_cashier_detail.harga_jual',
                                        'sales_cashier_detail.subtotal',
                                        'product.nama_item',
                                        'product_unit.nama_satuan',
                                        'product_detail.harga_jual as harga_jual_standard',
                                        'product_detail.mode',
                                        'product_detail.qty_mode',
                                        'product_detail.jenis_grosir',
                                        'product_detail.qty_grosir',
                                        'product_detail.harga_grosir',
                                        'product_detail.jenis_grosir_2',
                                        'product_detail.qty_grosir_2',
                                        'product_detail.harga_grosir_2',
                                    )
                                    ->where([
                                        ['sales_cashier_detail.id_sc', '=', $id]
                                    ])
                                    ->get();


                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataTransaction'] = $dataTransaction;
                $data['dataProduct'] = $dataProduct;
                $data['dataRekening'] = $dataRekening;
                $data['detail'] = $detail;

                $log = ActionLog::create([
                    'module' => 'Sales Cashier',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Cashier (Ref:'.$dataTransaction->no_ref.')',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.cashier.edit', $data);
            }
            else {
                return redirect('/Cashier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Cashier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $parentMenu = Module::find($hakAkses->parent);

                $dataTransaction = SalesCashier::find($id);
                $dataUser = User::find($dataTransaction->id_user);
                $dataCustomer = Customer::find($dataTransaction->id_customer);
                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->where([
                                                    ['company_account.id', '=', $dataTransaction->id_rekening]
                                                ])
                                                ->first();

                $detail = SalesCashierDetail::leftJoin('product', 'sales_cashier_detail.id_item', 'product.id')
                                    ->leftJoin('product_unit', 'sales_cashier_detail.id_satuan', 'product_unit.id')
                                    ->select(
                                        'sales_cashier_detail.id',
                                        'sales_cashier_detail.id_item',
                                        'sales_cashier_detail.id_satuan',
                                        'sales_cashier_detail.qty_item',
                                        'sales_cashier_detail.harga_jual',
                                        'sales_cashier_detail.subtotal',
                                        'product.nama_item',
                                        'product_unit.nama_satuan'
                                    )
                                    ->where([
                                        ['sales_cashier_detail.id_sc', '=', $id]
                                    ])
                                    ->get();

                $group = Auth::user()->user_group;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataTransaction'] = $dataTransaction;
                $data['dataUser'] = $dataUser;
                $data['dataRekening'] = $dataRekening;
                $data['dataDetail'] = $detail;
                $data['group'] = $group;

                $log = ActionLog::create([
                    'module' => 'Sales Cashier',
                    'action' => 'Detail',
                    'desc' => 'Detail Sales Cashier',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.cashier.detail', $data);
            }
            else {
                return redirect('/Cashier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetak($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Cashier'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTransaction = SalesCashier::find($id);
                $prevTransaction = SalesCashier::find($dataTransaction->id_hutang);
                $dataUser = User::find($dataTransaction->id_user);
                $dataCustomer = Customer::find($dataTransaction->id_customer);
                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->where([
                                                    ['company_account.id', '=', $dataTransaction->id_rekening]
                                                ])
                                                ->first();

                $details = SalesCashierDetail::leftJoin('product', 'sales_cashier_detail.id_item', 'product.id')
                                    ->leftJoin('product_unit', 'sales_cashier_detail.id_satuan', 'product_unit.id')
                                    ->select(
                                        'sales_cashier_detail.id',
                                        'sales_cashier_detail.id_item',
                                        'sales_cashier_detail.id_satuan',
                                        'sales_cashier_detail.qty_item',
                                        'sales_cashier_detail.harga_jual',
                                        'sales_cashier_detail.subtotal',
                                        'product.nama_item',
                                        'product_unit.nama_satuan'
                                    )
                                    ->where([
                                        ['sales_cashier_detail.id_sc', '=', $id]
                                    ])
                                    ->get();


                $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
                $data['dataTransaction'] = $dataTransaction;
                $data['prevTransaction'] = $prevTransaction;
                $data['dataUser'] = $dataUser;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataRekening'] = $dataRekening;
                $data['details'] = $details;
                $data['current_user'] = Auth::user()->user_name;


                $log = ActionLog::create([
                    'module' => 'Cetak Struk Kasir',
                    'action' => 'Generate',
                    'desc' => 'Generate Cetak Struk Kasir. Ref. :'.$dataTransaction->no_ref,
                    'username' => Auth::user()->user_name
                ]);

                // $fpdf = HelperDelivery::cetakPdfDlv($data);

                // $fpdf->Output('I', strtoupper(str_replace(["-","/"],"_",$dataDelivery->kode_pengiriman)).".pdf");
                // exit;

                $pdf = Pdf::loadView('pages.sales.cashier.cetak', ['data' => $data]);
                return $pdf->stream("Receipt.pdf");
            }
            else {
                return redirect('/Cashier')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function RestoreQuotationDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idQuotation');
            $restore = QuotationDetail::onlyTrashed()->where([['id_quotation', '=', $id]]);
            $restore->restore();
         });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getProductCustomer(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = CustomerProduct::leftJoin('product', 'customer_product.id_item', 'product.id')
                                        ->select('product.id', 'product.nama_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['customer_product.id_customer', '=', $idCustomer],
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

        // $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
        //                                             ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
        //                                             ->whereIn('sales_order.tanggal_so', function($querySub) use ($idProduct, $idCustomer, $idSatuan) {
        //                                                 $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
        //                                                         ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
        //                                                         ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                                                         ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
        //                                                         ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
        //                                                         ->where([
        //                                                             ['sales_order.id_customer', '=', $idCustomer],
        //                                                             ['sales_order_detail.id_satuan', '=', $idSatuan],
        //                                                             ['sales_order_detail.id_item', '=', $idProduct]
        //                                                         ]);
        //                                             })
        //                                             ->where([
        //                                                 ['sales_order.id_customer', '=', $idCustomer],
        //                                                 ['sales_order_detail.id_satuan', '=', $idSatuan],
        //                                                 ['sales_order_detail.id_item', '=', $idProduct]
        //                                             ]);


        // $dataProduct = Product::leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($hargaJualTerakhir) {
        //                             $hargaJualTerakhir->on('product.id', '=', 'hargaJualTerakhir.id_item');
        //                         })
        //                         ->leftJoin('product_detail', function($join) {
        //                             $join->on('product_detail.id_satuan', '=', 'hargaJualTerakhir.id_satuan');
        //                             $join->on('product_detail.id_product', '=', 'hargaJualTerakhir.id_item');
        //                         })
        //                         ->leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
        //                         ->select(
        //                             'product_detail.harga_jual',
        //                             DB::raw("COALESCE(hargaJualTerakhir.harga_jual_last,0) AS harga_jual_last")
        //                         )
        //                         ->where([
        //                             ['product.id', '=', $idProduct]
        //                         ])
        //                         ->get();

        $dataProduct = Product::leftJoin('product_detail', 'product_detail.id_product', '=', 'product.id')
                                ->leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    DB::raw("COALESCE(product_detail.harga_jual,0) AS harga_jual"),
                                    'product_detail.jenis_grosir',
                                    DB::raw("COALESCE(product_detail.harga_grosir,0) AS harga_grosir"),
                                    DB::raw("COALESCE(product_detail.qty_grosir,0) AS qty_grosir"),
                                    'product_detail.jenis_grosir_2',
                                    DB::raw("COALESCE(product_detail.harga_grosir_2,0) AS harga_grosir_2"),
                                    DB::raw("COALESCE(product_detail.qty_grosir_2,0) AS qty_grosir_2"),
                                    'product_detail.mode',
                                    DB::raw("COALESCE(product_detail.qty_mode,0) AS qty_mode"),
                                )
                                ->where([
                                    ['product.id', '=', $idProduct],
                                    ['product_detail.id_satuan', '=', $idSatuan],
                                ])
                                ->get();

        return response()->json($dataProduct);
    }

    public function getPreviousDebt(Request $request)
    {
        $idCustomer = $request->input('idCustomer');

        $dataDebt = SalesCashier::select(DB::raw('COALESCE(SUM(nominal_outstanding), 0) AS debt_amount'))
                                    ->where([
                                                ['id_customer', '=', $idCustomer],
                                                ['status_sales', '=', 'posted'],
                                                ['nominal_outstanding', '>', 0],
                                                ['flag_lunas', '=', 1]
                                            ])
                                    ->groupBy('id_customer')
                                    ->first();

        return response()->json($dataDebt);
    }

    public function getDefaultAddress(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $defaultAddress = CustomerDetail::where([
                                            ['id_customer', '=', $idCustomer],
                                            ['default', '=', 'Y']
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getCustomerAddress(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $customerAddress = CustomerDetail::where([
                                            ['id_customer', '=', $idCustomer]
                                        ])
                                        ->get();

        return response()->json($customerAddress);
    }

    public function getProduct(Request $request)
    {
        $idCustomer = $request->input('id_customer');
        $dataProduct = "";

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
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->whereNOTIn('product.id', function($query) use ($idCustomer) {
                                        $query->select('id_item')->from('customer_product')
                                            ->where('id_customer', $idCustomer);
                                    })
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

    public function addCustomerProduct(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('id_customer');
            $idItem = $request->input('id_item');

            $customerProduct = new CustomerProduct();
            $customerProduct->id_customer = $idCustomer;
            $customerProduct->id_item = $idItem;
            $customerProduct->created_by = Auth::user()->user_name;
            $customerProduct->save();

            $data = $customerProduct;
        });

        return response()->json($data);
    }

    public function StoreTransaction(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('idCustomer');
            $tableData = $request->input('tableData');
            $action = $request->input('action');
            $cashPayment = $request->input('cashPayment');
            $cashChange = $request->input('cashChange');
            $ccNumber = $request->input('ccNumber');
            $ccName = $request->input('ccName');
            $ccMonth = $request->input('ccMonth');
            $ccYear = $request->input('ccYear');
            $rekening = $request->input('rekening');
            $debt = $request->input('debt');
            $metodeBayar = $request->input('metodeBayar');
            $arrayData = json_decode($tableData, true);

            $userID = Auth::user()->id;
            $userName = Auth::user()->user_name;

            $tgl = Carbon::now();

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('sales_cashier')
                            ->select(DB::raw("MAX(RIGHT(sales_cashier.no_ref,4)) AS angka"))
                            // ->whereMonth('tanggal_penjualan', $blnPeriode)
                            // ->whereYear('tanggal_penjualan', $thnPeriode)
                            ->whereDate('tanggal_penjualan', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            $grandTotal = 0;
            $totalQty = 0;

            foreach ($arrayData as $detail) {
                $totalQty = $totalQty + $detail['qty'];
                $grandTotal = $grandTotal + $detail['subtotal'];
            }

            $idDebt = "";

            if ($debt != 0 && $debt != "") {
                $cashPayment = $cashPayment - $debt;

                $prevTrans = SalesCashier::where([
                                                ['id_customer', '=', $idCustomer],
                                                ['status_sales', '=', 'posted'],
                                                ['nominal_outstanding', '>', 0],
                                                ['flag_lunas', '=', 1],
                                            ])
                                    ->orderBy('tanggal_penjualan', 'desc')
                                    ->first();

                if ($prevTrans) {
                    $prevTrans->flag_lunas = 0;
                    $prevTrans->save();
                    $idDebt = $prevTrans->id;
                }
            }

            $outstanding = $grandTotal - $cashPayment;

            if ($counter < 10) {
                $noRef = "sale-tss-".$kodeTgl."000".$counter;
            }
            else if ($counter < 100) {
                $noRef = "sale-tss-".$kodeTgl."00".$counter;
            }
            else if ($counter < 1000) {
                $noRef = "sale-tss-".$kodeTgl."0".$counter;
            }
            else {
                $noRef = "sale-tss-".$kodeTgl.$counter;
            }

            $transaction = new SalesCashier();
            $transaction->no_ref = $noRef;
            $transaction->id_customer = $idCustomer;
            $transaction->id_user = $userID;
            $transaction->tanggal_penjualan = $tgl;
            $transaction->jumlah_total_qty = $totalQty;
            $transaction->nominal_total = $grandTotal;
            $transaction->metode_pembayaran = $metodeBayar;
            $transaction->nominal_pembayaran = $cashPayment;
            $transaction->nominal_change = $cashChange;
            $transaction->nominal_outstanding = $outstanding > 1 ? $outstanding : 0;
            $transaction->flag_lunas = $outstanding > 1 ? 1 : 0;
            $transaction->nominal_change = $cashChange;
            $transaction->nominal_pembayaran_hutang = $debt;
            if ($idDebt != "") {
                $transaction->id_hutang = $idDebt;
            }
            $transaction->id_rekening = $rekening;
            $transaction->cc_number = $ccNumber;
            $transaction->cc_name = $ccName;
            $transaction->cc_month = $ccMonth;
            $transaction->cc_year = $ccYear;
            $transaction->status_sales = $action == 'post' ? 'posted' : 'draft';
            $transaction->created_by = $userName;
            $transaction->save();

            if ($transaction) {
                foreach ($arrayData as $detail) {
                    $transactionDetail = new SalesCashierDetail();
                    $transactionDetail->id_sc = $transaction->id;
                    $transactionDetail->id_item = $detail['idProduct'];
                    $transactionDetail->id_satuan = $detail['idSatuan'];
                    $transactionDetail->harga_jual = $detail['harga'];
                    $transactionDetail->qty_item = $detail['qty'];
                    $transactionDetail->subtotal = $detail['subtotal'];
                    $transactionDetail->created_by = $userName;
                    $transactionDetail->save();
                }

                $data->id = $transaction->id;
                $data->no_ref = $transaction->no_ref;
                $data->message = "success";

                Helper::SubmitStockTransaction("post", $transaction);
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            $data->message ="failed";
            $data->exception = $exception;
            return response()->json($data);
        }
    }

    public function UpdateTransaction(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idTransaction');
            $idCustomer = $request->input('idCustomer');
            $tableData = $request->input('tableData');
            $action = $request->input('action');
            $cashPayment = $request->input('cashPayment');
            $cashChange = $request->input('cashChange');
            $ccNumber = $request->input('ccNumber');
            $ccName = $request->input('ccName');
            $ccMonth = $request->input('ccMonth');
            $ccYear = $request->input('ccYear');
            $rekening = $request->input('rekening');
            $debt = $request->input('debt');
            $metodeBayar = $request->input('metodeBayar');
            $arrayData = json_decode($tableData, true);

            $payloadNew = new stdClass();
            $payloadOld = new stdClass();


            $userID = Auth::user()->id;
            $userName = Auth::user()->user_name;

            $grandTotal = 0;
            $totalQty = 0;

            foreach ($arrayData as $detail) {
                $totalQty = $totalQty + $detail['qty'];
                $grandTotal = $grandTotal + $detail['subtotal'];
            }

            $idDebt = "";

            if ($debt != 0 && $debt != "") {
                $cashPayment = $cashPayment - $debt;

                $prevTrans = SalesCashier::where([
                                                ['id_customer', '=', $idCustomer],
                                                ['status_sales', '=', 'posted'],
                                                ['nominal_outstanding', '>', 0],
                                                ['flag_lunas', '=', 1],
                                            ])
                                    ->orderBy('tanggal_penjualan', 'desc')
                                    ->first();

                if ($prevTrans) {
                    $prevTrans->flag_lunas = 0;
                    $prevTrans->save();
                    $idDebt = $prevTrans->id;
                }
            }

            $outstanding = $grandTotal - $cashPayment;

            $transaction = SalesCashier::find($id);

            $transactionDetail = SalesCashierDetail::where([
                ['id_sc', '=', $transaction->id],
            ])
            ->get();

            //assign payload lama
            $payloadOld->transaction = $transaction;
            $payloadOld->detail = $transactionDetail;

            $transaction->id_customer = $idCustomer;
            $transaction->id_user = $userID;
            $transaction->jumlah_total_qty = $totalQty;
            $transaction->nominal_total = $grandTotal;
            $transaction->metode_pembayaran = $metodeBayar;
            $transaction->nominal_pembayaran = $cashPayment;
            $transaction->nominal_change = $cashChange;
            $transaction->nominal_outstanding = $outstanding > 1 ? $outstanding : 0;
            $transaction->flag_lunas = $outstanding > 1 ? 1 : 0;
            $transaction->nominal_change = $cashChange;
            $transaction->nominal_pembayaran_hutang = $debt;
            if ($idDebt != "") {
                $transaction->id_hutang = $idDebt;
            }
            $transaction->id_rekening = $rekening;
            $transaction->cc_number = $ccNumber;
            $transaction->cc_name = $ccName;
            $transaction->cc_month = $ccMonth;
            $transaction->cc_year = $ccYear;
            // $transaction->status_sales = $action == 'post' ? 'posted' : 'draft';
            $transaction->flag_revisi = $transaction->flag_revisi + 1;
            $transaction->flag_request_revisi = 0;
            $transaction->flag_approved = 0;
            $transaction->updated_by = $userName;
            $transaction->save();

            $payloadNew->transaction = $transaction;

            if ($transaction) {
                $delete = DB::table('sales_cashier_detail')
                                ->where([
                                    ['id_sc', '=', $id]
                                ])
                                ->delete();

                foreach ($arrayData as $detail) {



                    $transactionDetail = new SalesCashierDetail();
                    $transactionDetail->id_sc = $transaction->id;
                    $transactionDetail->id_item = $detail['idProduct'];
                    $transactionDetail->id_satuan = $detail['idSatuan'];
                    $transactionDetail->harga_jual = $detail['harga'];
                    $transactionDetail->qty_item = $detail['qty'];
                    $transactionDetail->subtotal = $detail['subtotal'];
                    $transactionDetail->created_by = $userName;
                    $transactionDetail->save();
                }


                $updatedTransactionDetail = SalesCashierDetail::where([
                    ['id_sc', '=', $transaction->id],
                ])
                ->get();

                $payloadNew->detail = $updatedTransactionDetail;

                $data->id = $transaction->id;
                $data->no_ref = $transaction->no_ref;
                $data->message = "success";

                Helper::SubmitStockTransaction("update", $transaction);
            }

            $log = ActionLog::create([
                'module' => 'Transaksi Penjualan',
                'action' => 'Update',
                'desc' => 'Update Transaksi Penjualan Ref:'.strtoupper($data->no_ref),
                'payload_old' => json_encode($payloadOld),
                'payload_new' => json_encode($payloadNew),
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetQuotationDetail(Request $request)
    {
        $id = $request->input('idQuotation');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = QuotationDetail::leftJoin('product_unit', 'quotation_detail.id_satuan', 'product_unit.id')
                                    ->select(
                                        'quotation_detail.id',
                                        'quotation_detail.id_item',
                                        'quotation_detail.id_satuan',
                                        'quotation_detail.qty_item',
                                        'quotation_detail.harga_jual',
                                        'quotation_detail.keterangan',
                                        DB::raw('COALESCE(quotation_detail.harga_jual,0) * COALESCE(quotation_detail.qty_item) AS subtotal'),
                                        'product_unit.nama_satuan'
                                    )
                                    ->where([
                                        ['quotation_detail.id_quotation', '=', $id]
                                    ])
                                    ->when($id == "DRAFT", function($q) use ($user) {
                                        $q->where('quotation_detail.created_by', $user);
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
                                            'temp_transaction.value6',
                                            'product_unit.nama_satuan',
                                            DB::raw('COALESCE(temp_transaction.value5,0) * COALESCE(temp_transaction.value4) AS subtotal')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'quotation']
                                        ])
                                        ->get();
        }
        return response()->json($detail);
    }

    public function EditQuotationDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            if ($mode == "") {

                $detail = QuotationDetail::leftJoin('product', 'quotation_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'quotation_detail.id_satuan', 'product_unit.id')
                                                ->select(
                                                    'quotation_detail.id',
                                                    'quotation_detail.id_item',
                                                    'quotation_detail.id_satuan',
                                                    'quotation_detail.qty_item',
                                                    'quotation_detail.harga_jual',
                                                    'quotation_detail.keterangan',
                                                    DB::raw('COALESCE(quotation_detail.harga_jual,0) * COALESCE(quotation_detail.qty_item) AS subtotal'),
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan'
                                                )
                                                ->where([
                                                    ['quotation_detail.id', '=', $id]
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
                                            DB::raw('COALESCE(temp_transaction.value5,0) * COALESCE(temp_transaction.value4) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'quotation']
                                        ])
                                        ->get();
            }

            $data = $detail;
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function DeleteQuotationDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
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
                $delete = DB::table('quotation_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function ResetQuotationDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idQuotation');


            if ($id != "DRAFT") {
                $detail = QuotationDetail::where([
                                            ['id_quotation', '=' ,$id]
                                        ])
                                        ->update([
                                            'deleted_at' => now(),
                                            'deleted_by' => Auth::user()->user_name
                                        ]);
            }
            else {
                $delete = DB::table('quotation_detail')->where('id_quotation', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetQuotationFooter(Request $request)
    {
        $id = $request->input('idQuotation');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {

            $detail = QuotationDetail::leftJoin('product', 'quotation_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'quotation_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                DB::raw('SUM(quotation_detail.qty_item) AS qtyItem'),
                                                DB::raw('SUM(COALESCE(quotation_detail.harga_jual,0) * COALESCE(quotation_detail.qty_item,0)) AS subtotal')
                                            )
                                            ->where([
                                                ['quotation_detail.id_quotation', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('quotation_detail.created_by', $user);
                                            })
                                            ->groupBy('quotation_detail.id_quotation')
                                            ->first();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(temp_transaction.value4) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(temp_transaction.value5,0) * COALESCE(temp_transaction.value4,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'quotation']
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
            'customer'=>'required',
            'id_alamat'=>'required',
            'tanggal_quo'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tgl = $request->input('tanggal_quo');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/Quotation')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $tglQuo = $request->input('tanggal_quo');
            $flagPPn = $request->input('status_ppn');
            $pic = $request->input('pic');
            $posisi = $request->input('posisi');
            $dpp =  $request->input('dpp');
            $ppn =  $request->input('ppn');
            $gt =  $request->input('gt');
            $qtyOrder = $request->input('qtyTtl');
            $metodePembayaran = $request->input('metode_bayar');
            $durasiJt = $request->input('durasi_jt');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyOrder = str_replace(",", ".", $qtyOrder);
            $dpp = str_replace(",", ".", $dpp);

            $blnPeriode = date("m", strtotime($tglQuo));
            $thnPeriode = date("Y", strtotime($tglQuo));
            $tahunPeriode = date("y", strtotime($tglQuo));

            $countKode = DB::table('quotation')
                            ->select(DB::raw("MAX(RIGHT(no_quotation,2)) AS angka"))
                            // ->whereMonth('tanggal_quotation', $blnPeriode)
                            // ->whereYear('tanggal_quotation', $thnPeriode)
                            ->whereDate('tanggal_quotation', $tglQuo)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglQuo)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglQuo))));

            if ($counter < 10) {
                $nmrQuotation = "quo-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrQuotation = "quo-cv-".$kodeTgl.$counter;
            }

            $quotation = new Quotation();
            $quotation->no_quotation = $nmrQuotation;
            $quotation->id_customer = $idCustomer;
            $quotation->id_alamat = $idAlamat;
            $quotation->jumlah_total_quotation = 0;
            $quotation->tanggal_quotation = $tglQuo;
            $quotation->metode_pembayaran = $metodePembayaran;
            $quotation->nominal_quotation = 0;
            $quotation->ppn_quotation = 0;
            $quotation->grand_total_quotation = 0;
            $quotation->durasi_jt = $durasiJt;
            $quotation->flag_ppn = $flagPPn;
            $quotation->pic_penawaran = $pic;
            $quotation->posisi_pic = $posisi;
            $quotation->status_quotation = 'draft';
            $quotation->id_ppn = $taxSettings->ppn_percentage_id;
            $quotation->created_by = $user;
            $quotation->save();

            $data = $quotation;

            $setDetail = DB::table('quotation_detail')
                            ->where([
                                        ['id_quotation', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_quotation' => $quotation->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_quotation' => $quotation->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                QuotationTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Quotation',
                'action' => 'Simpan',
                'desc' => 'Simpan Quotation',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('Quotation.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_quotation).' Telah Disimpan!');
        }
        else {
            return redirect('/Quotation')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'id_alamat'=>'required',
            'tanggal_quo'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tgl = $request->input('tanggal_quo');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect()->route('Quotation.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $tglQuo = $request->input('tanggal_quo');
            $flagPPn = $request->input('status_ppn');
            $dpp =  $request->input('dpp');
            $ppn =  $request->input('ppn');
            $gt =  $request->input('gt');
            $pic = $request->input('pic');
            $posisi = $request->input('posisi');
            $metodePembayaran = $request->input('metode_bayar');
            $qtyOrder = $request->input('qtyTtl');
            $durasiJt = $request->input('durasi_jt');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyOrder = str_replace(",", ".", $qtyOrder);
            $dpp = str_replace(",", ".", $dpp);

            $blnPeriode = date("m", strtotime($tglQuo));
            $thnPeriode = date("Y", strtotime($tglQuo));

            $transaction = Quotation::find($id);
            $payloadOld = json_encode($transaction);

            $transaction->id_customer = $idCustomer;
            $transaction->id_alamat = $idAlamat;
            $transaction->jumlah_total_quotation = 0;
            $transaction->tanggal_quotation = $tglQuo;
            $transaction->nominal_quotation = 0;
            $transaction->ppn_quotation = 0;
            $transaction->grand_total_quotation = 0;
            $transaction->flag_ppn = $flagPPn;
            $transaction->metode_pembayaran = $metodePembayaran;
            $transaction->durasi_jt = $durasiJt;
            $transaction->pic_penawaran = $pic;
            $transaction->posisi_pic = $posisi;
            $transaction->id_ppn = $taxSettings->ppn_percentage_id;
            $transaction->updated_by = $user;
            $transaction->save();

            $data = $transaction;
            $payloadNew = json_encode($transaction);


            $tempDetail = DB::table('temp_transaction')
                            ->where([
                                ['module', '=', 'quotation'],
                                ['value1', '=', $id],
                                ['action', '!=' , null]
                            ])
                            ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = QuotationDetail::find($detail->id_detail);
                        $listItem->id_quotation = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->harga_jual = $detail->value5;
                        $listItem->keterangan = $detail->value6;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new QuotationDetail();
                        $listItem->id_quotation = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->harga_jual = $detail->value5;
                        $listItem->keterangan = $detail->value6;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('quotation_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'quotation'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($terms != "") {
                $delete = DB::table('quotation_terms')->where('id_quotation', '=', $transaction->id)->delete();
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_quotation' => $transaction->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                QuotationTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Transaksi Penjualan',
                'action' => 'Update',
                'desc' => 'Update Transaksi Penjualan Ref:'.strtoupper($data->no_ref),
                'payload_old' => $payloadOld,
                'payload_new' => $payloadNew,
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('Cashier.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_ref).' Telah Diupdate!');
        }
        else {
            return redirect('/Cashier')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $transaction = SalesCashier::find($id);

            $data = $transaction;

            if ($btnAction == "posting") {
                $transaction->status_sales = "posted";
                $transaction->save();
                $log = ActionLog::create([
                    'module' => 'Penjualan kasir',
                    'action' => 'Posting',
                    'desc' => 'Posting Penjualan kasir',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Transaksi '.strtoupper($transaction->no_ref).' Telah Diposting!';
                $status = "success";
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "approve_revisi") {
                $status = "warning";
                $transaction->status_sales = "revisi";
                $transaction->flag_approved = '1';
                $transaction->updated_by = Auth::user()->user_name;
                $transaction->save();

                $log = ActionLog::create([
                    'module' => 'Penjualan Kasir',
                    'action' => 'Request Revisi',
                    'desc' => 'Request Revisi Penjualan Kasir (Ref:'.$transaction->no_ref.')',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Request Reivisi untuk Transaksi '.strtoupper($transaction->no_ref).' Telah Di Approve!';
            }
            elseif ($btnAction == "request_revisi") {
                $status = "warning";
                $transaction->status_sales = "request_revisi";
                $transaction->flag_request_revisi = '1';
                $transaction->updated_by = Auth::user()->user_name;
                $transaction->save();

                $log = ActionLog::create([
                    'module' => 'Penjualan Kasir',
                    'action' => 'Request Revisi',
                    'desc' => 'Request Revisi Penjualan Kasir (Ref:'.$transaction->no_ref.')',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Request Reivisi untuk Transaksi '.strtoupper($transaction->no_ref).' Telah Disubmit!';
            }
            elseif ($btnAction == "revisi") {
                // $quotation->status_quotation = "draft";
                // $transaction->status_sales = "revisi";
                // $transaction->flag_revisi = '1';
                $transaction->updated_by = Auth::user()->user_name;
                $transaction->save();

                $log = ActionLog::create([
                    'module' => 'Penjualan Kasir',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Penjualan Kasir (Ref:'.$transaction->no_ref.')',
                    'username' => Auth::user()->user_name
                ]);

                // $msg = 'Transaksi '.strtoupper($transaction->no_ref).' Telah Direvisi!';
                $status = "revisi";
            }
        });

        if (is_null($exception)) {
            if ($status == "revisi") {
                return redirect()->route('Cashier.edit', [$id]);
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
            $id = $request->input('idQuotation');
            $user = Auth::user()->user_name;
            $delete = Quotation::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Quotation',
                'action' => 'Delete',
                'desc' => 'Delete Quotation',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json(['success'=>'Data Berhasil Dihapus!']);
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
