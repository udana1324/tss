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
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Session;
use stdClass;

class QuotationController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Quotation'],
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
                                                ['module.url', '=', '/Quotation'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = Quotation::distinct()->get('status_quotation');
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('quotation_detail')->where('deleted_at', '!=', null)->delete();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Quotation',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.quotation.index', $data);
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

        $quotation = Quotation::leftJoin('customer', 'quotation.id_customer', '=', 'customer.id')
                            ->select(
                                'customer.nama_customer',
                                'quotation.id',
                                'quotation.no_quotation',
                                'quotation.jumlah_total_quotation',
                                'quotation.tanggal_quotation',
                                'quotation.nominal_quotation',
                                'quotation.flag_revisi',
                                'quotation.status_quotation')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('quotation.tanggal_quotation', Carbon::parse($periode)->format('m'));
                                $q->whereYear('quotation.tanggal_quotation', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('quotation.id', 'desc')
                            ->get();
        return response()->json($quotation);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Quotation'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataUnit = ProductUnit::all();

                $parentMenu = Module::find($hakAkses->parent);
                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataUnit'] = $dataUnit;

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Buat',
                    'desc' => 'Buat Quotation',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('quotation_detail')
                            ->where([
                                ['id_quotation', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.quotation.add', $data);
            }
            else {
                return redirect('/Quotation')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Quotation'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataQuotation = Quotation::find($id);
                $dataUnit = ProductUnit::all();
                $dataAlamat = CustomerDetail::find($dataQuotation->id_alamat);
                $dataTerms = QuotationTerms::where('id_quotation', $id)->get();
                if ($dataQuotation->status_quotation != "draft") {
                    return redirect('/Quotation')->with('warning', 'Quotation tidak dapat diubah karena status Quotation bukan DRAFT!');
                }

                //$restore = QuotationDetail::onlyTrashed()->where([['id_quotation', '=', $id]]);
                //$restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'quotation'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = QuotationDetail::where([
                                                    ['id_quotation', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'quotation',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_quotation,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_item,
                            'value5' => $detail->harga_jual,
                            'value6' => $detail->keterangan
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
                $data['dataCustomer'] = $dataCustomer;
                $data['dataQuotation'] = $dataQuotation;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataTerms'] = $dataTerms;
                $data['dataUnit'] = $dataUnit;

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Quotation',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.quotation.edit', $data);
            }
            else {
                return redirect('/Quotation')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Quotation'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataQuotation = Quotation::find($id);
                $dataAlamat = CustomerDetail::find($dataQuotation->id_alamat);
                $dataTerms = QuotationTerms::where('id_quotation', $id)->get();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $taxSettings = TaxSettingsPPN::find($dataQuotation->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataQuotation'] = $dataQuotation;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Detail',
                    'desc' => 'Detail Quotation',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.quotation.detail', $data);
            }
            else {
                return redirect('/Quotation')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Quotation'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataQuotation = Quotation::leftJoin('customer', 'quotation.id_customer', '=', 'customer.id')
                                            ->leftJoin('sales', 'customer.sales', '=', 'sales.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'sales.nama_sales',
                                                'sales.telp_sales',
                                                'sales.email_sales',
                                                'quotation.*'
                                            )
                                            ->where([
                                                ['quotation.id', '=', $id],
                                            ])
                                            ->first();

                $dataTerms = QuotationTerms::where('id_quotation', $id)->get();
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
                                            ->where('flag_quo', 'Y')
                                            ->first();
                $detailQuotation = QuotationDetail::leftJoin('product', 'quotation_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'quotation_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'quotation_detail.id',
                                                                'quotation_detail.id_item',
                                                                'quotation_detail.qty_item',
                                                                'quotation_detail.harga_jual',
                                                                'quotation_detail.keterangan',
                                                                DB::raw('COALESCE(quotation_detail.harga_jual,0) * COALESCE(quotation_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['quotation_detail.id_quotation', '=', $id]
                                                            ])
                                                            ->get();

                $dataDetails = array();

                foreach ($detailQuotation as $details) {
                    $spek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', '=', 'product_specification.id')
                                                        ->select('product_specification.flag_cetak','product_specification.nama_spesifikasi','product_detail_specification.value_spesifikasi')
                                                        ->where([
                                                            ['product_detail_specification.id_product', '=', $details->id_item]
                                                        ])
                                                        ->get();

                    $dataItem = [
                        'id_item' => $details->id_item,
                        'qty_item' => $details->qty_item,
                        'harga_jual' => $details->harga_jual,
                        'subtotal' => $details->subtotal,
                        'kode_item' => $details->kode_item,
                        'nama_item' => $details->nama_item,
                        'nama_satuan' => $details->nama_satuan,
                        'spesifikasi' => $spek,
                    ];
                    array_push($dataDetails, $dataItem);
                }

                $dataAlamat = CustomerDetail::find($dataQuotation->id_alamat);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataQuotation'] = $dataQuotation;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailQuotation'] = $detailQuotation;
                $data['dataDetails'] = $dataDetails;

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Quotation',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperQuotation::cetakPdfQuotation($data);

                $fpdf->Output('I', strtoupper($dataQuotation->no_quotation).".pdf");
                exit;
            }
            else {
                return redirect('/Quotation')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
        $idCustomer = $request->input('id_customer');

        $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                    ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
                                                    ->whereIn('sales_order.tanggal_so', function($querySub) use ($idProduct, $idCustomer, $idSatuan) {
                                                        $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
                                                                ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                                ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                                ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
                                                                ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                                ->where([
                                                                    ['sales_order.id_customer', '=', $idCustomer],
                                                                    ['sales_order_detail.id_satuan', '=', $idSatuan],
                                                                    ['sales_order_detail.id_item', '=', $idProduct]
                                                                ]);
                                                    })
                                                    ->where([
                                                        ['sales_order.id_customer', '=', $idCustomer],
                                                        ['sales_order_detail.id_satuan', '=', $idSatuan],
                                                        ['sales_order_detail.id_item', '=', $idProduct]
                                                    ]);


        $dataProduct = Product::leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($hargaJualTerakhir) {
                                    $hargaJualTerakhir->on('product.id', '=', 'hargaJualTerakhir.id_item');
                                })
                                ->leftJoin('product_detail', function($join) {
                                    $join->on('product_detail.id_satuan', '=', 'hargaJualTerakhir.id_satuan');
                                    $join->on('product_detail.id_product', '=', 'hargaJualTerakhir.id_item');
                                })
                                ->leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_detail.harga_jual',
                                    DB::raw("COALESCE(hargaJualTerakhir.harga_jual_last,0) AS harga_jual_last")
                                )
                                ->where([
                                    ['product.id', '=', $idProduct]
                                ])
                                ->get();

        return response()->json($dataProduct);
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

    public function StoreQuotationDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idQuotation');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $harga = $request->input('hargaJual');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $harga = str_replace(",", ".", $harga);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('quotation_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_quotation', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan],
                                    ['deleted_at', '=', null]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new QuotationDetail();
                    $listItem->id_quotation = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_item = $qty;
                    $listItem->harga_jual = $harga;
                    $listItem->keterangan = $keterangan;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Quotation Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Quotation Detail',
                        'username' => Auth::user()->user_name
                    ]);
                    $data = "success";
                }
            }
            else {
                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'quotation'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idItem],
                                    ['value3', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'quotation';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->value5 = $harga;
                    $listItem->value6 = $keterangan;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Quotation Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Quotation Detail',
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

    public function UpdateQuotationDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idQuotation');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyItem');
            $harga = $request->input('hargaJual');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $harga = str_replace(",", ".", $harga);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';
                $listItem = QuotationDetail::find($idDetail);
                $listItem->id_quotation = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_item = $qty;
                $listItem->harga_jual = $harga;
                $listItem->keterangan = $keterangan;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->value5 = $harga;
                $listItem->value6 = $keterangan;
                $listItem->action = 'tambah';
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }



            $log = ActionLog::create([
                'module' => 'Quotation Detail',
                'action' => 'Update',
                'desc' => 'Update Quotation Detail',
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

            $quotation = Quotation::find($id);
            $quotation->id_customer = $idCustomer;
            $quotation->id_alamat = $idAlamat;
            $quotation->jumlah_total_quotation = 0;
            $quotation->tanggal_quotation = $tglQuo;
            $quotation->nominal_quotation = 0;
            $quotation->ppn_quotation = 0;
            $quotation->grand_total_quotation = 0;
            $quotation->flag_ppn = $flagPPn;
            $quotation->metode_pembayaran = $metodePembayaran;
            $quotation->durasi_jt = $durasiJt;
            $quotation->pic_penawaran = $pic;
            $quotation->posisi_pic = $posisi;
            $quotation->id_ppn = $taxSettings->ppn_percentage_id;
            $quotation->updated_by = $user;
            $quotation->save();

            $data = $quotation;


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
                $delete = DB::table('quotation_terms')->where('id_quotation', '=', $quotation->id)->delete();
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
                'action' => 'Update',
                'desc' => 'Update Quotation',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('Quotation.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_quotation).' Telah Diupdate!');
        }
        else {
            return redirect('/Quotation')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $quotation = Quotation::find($id);

            $data = $quotation;

            if ($btnAction == "posting") {
                $quotation->status_quotation = "posted";
                $quotation->save();
                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Posting',
                    'desc' => 'Posting Quotation',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Penawaran '.strtoupper($quotation->no_quotation).' Telah Diposting!';
                $status = "success";
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "buat_so") {
                $status = "buat_so";
            }
            elseif ($btnAction == "revisi") {
                $quotation->status_quotation = "draft";
                $quotation->flag_revisi = '1';
                $quotation->updated_by = Auth::user()->user_name;
                $quotation->save();

                $log = ActionLog::create([
                    'module' => 'Quotation',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Quotation',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Penawaran '.strtoupper($quotation->no_quotation).' Telah Direvisi!';
                $status = "success";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('Quotation.edit', [$id]);
            }
            elseif($status == "buat_so") {
                Session::put('id_quo', $id);
                Session::put('id_cust', $data->id_customer);
                Session::save();

                return redirect('SalesOrder/Add');
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
