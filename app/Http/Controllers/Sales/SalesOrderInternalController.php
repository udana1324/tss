<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\DeliveryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerProduct;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Sales\SalesOrderInternal;
use App\Models\Sales\SalesOrderInternalDetail;
use App\Models\Sales\SalesOrderInternalTerms;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationDetail;
use App\Models\Sales\Delivery;
use App\Models\Library\ExpeditionBranch;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperSalesOrder;
use App\Exports\SalesOrderExport;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\CustomerGroup;
use App\Models\Library\CustomerGroupDetail;
use App\Models\Library\Sales;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductUnit;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Sales\DeliveryDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class SalesOrderInternalController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesOrderInternal'],
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
                                                ['module.url', '=', '/SalesOrderInternal'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = SalesOrderInternal::distinct()->get('status_so');
                $dataCustomer = Customer::all();

                $delete = DB::table('sales_order_internal_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_order_internal.index', $data);
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

        $salesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                            ->leftJoin('customer_detail', 'sales_order_internal.id_alamat', '=', 'customer_detail.id')
                            ->select(
                                'customer.nama_customer',
                                DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                'sales_order_internal.id',
                                'sales_order_internal.id_customer',
                                'sales_order_internal.no_so',
                                'sales_order_internal.nominal_dp',
                                'sales_order_internal.no_po_customer',
                                'sales_order_internal.jumlah_total_so',
                                'sales_order_internal.outstanding_so',
                                'sales_order_internal.tanggal_so',
                                'sales_order_internal.tanggal_request',
                                'sales_order_internal.nominal_so_ttl',
                                'sales_order_internal.flag_revisi',
                                'sales_order_internal.metode_pembayaran',
                                'sales_order_internal.durasi_jt',
                                'sales_order_internal.status_so')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('sales_order_internal.tanggal_so', Carbon::parse($periode)->format('m'));
                                $q->whereYear('sales_order_internal.tanggal_so', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('sales_order_internal.id', 'desc')
                            ->get();
        return response()->json($salesOrder);
    }

    public function getPPN(Request $request)
    {
        $periode = $request->input('periode');

        $dataPPN = TaxSettingsPPN::select(
                                'tax_settings_ppn.id',
                                'tax_settings_ppn.ppn_name',
                                'tax_settings_ppn.ppn_percentage',
                                'tax_settings_ppn.ppn_start_date',
                                'tax_settings_ppn.ppn_end_date',
                            )
                            ->where('tax_settings_ppn.ppn_start_date', '<=', $periode)
                            ->where(DB::raw("(tax_settings_ppn.ppn_end_date is null or tax_settings_ppn.ppn_end_date >= ".$periode.")"))
                            ->orderBy('tax_settings_ppn.ppn_start_date', 'desc')
                            ->first();

        return response()->json($dataPPN);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $idQuo = Session::get('id_quo');
                $idCust = Session::get('id_cust');
                if ($idCust == "" && $idQuo == "") {
                    $mode = "tambah";
                }
                else {
                    $mode = "so";
                }

                $data['idQuo'] = $idQuo;
                $data['idCust'] = $idCust;
                $data['mode'] = $mode;

                $dataCustomer = Customer::all();
                $dataEkspedisi = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                    ->select(
                                                        'expedition_branch.id',
                                                        'expedition_branch.nama_cabang'
                                                    )
                                                    ->get();

                $parentMenu = Module::find($hakAkses->parent);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataEkspedisi'] = $dataEkspedisi;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Buat',
                    'desc' => 'Buat Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('sales_order_internal_detail')
                            ->where([
                                ['id_so', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.sales_order_internal.add', $data);
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataSalesOrderInternal = SalesOrderInternal::find($id);
                if ($dataSalesOrderInternal->status_so != "draft") {
                    return redirect('/SalesOrderInternal')->with('warning', 'Sales Order tidak dapat diubah karena status Penjualan bukan DRAFT!');
                }
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
                $dataEkspedisi = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                    ->select(
                                                        'expedition_branch.id',
                                                        'expedition_branch.nama_cabang'
                                                    )
                                                    ->get();
                $dataAlamat = CustomerDetail::find($dataSalesOrderInternal->id_alamat);

                if ($dataSalesOrderInternal->status_so != "draft") {
                    return redirect('/SalesOrderInternal')->with('warning', 'Sales Order tidak dapat diubah karena status Sales Order bukan DRAFT!');
                }

                // $restore = SalesOrderInternalDetail::onlyTrashed()->where([['id_so', '=', $id]]);
                // $restore->restore();
                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_order_internal'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = SalesOrderInternalDetail::where([
                                                    ['id_so', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'sales_order_internal',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_so,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_item,
                            'value5' => $detail->qty_outstanding,
                            'value6' => $detail->qty_order,
                            'value7' => $detail->harga_jual,
                            'value8' => $detail->keterangan
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataSalesOrderInternal'] = $dataSalesOrderInternal;
                $data['dataTerms'] = $dataTerms;
                $data['dataEkspedisi'] = $dataEkspedisi;
                $data['dataAlamat'] = $dataAlamat;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_order_internal.edit', $data);
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataSalesOrderInternal = SalesOrderInternal::find($id);
                $dataCustomer = Customer::withTrashed()->find($dataSalesOrderInternal->id_customer);
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
                $dataAlamat = CustomerDetail::find($dataSalesOrderInternal->id_alamat);

                $delivery = Delivery::where([
                    ['id_so', '=', $id],
                    ['status_pengiriman', '!=', 'draft']
                ])
                ->count();

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataSalesOrderInternal->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataSalesOrderInternal'] = $dataSalesOrderInternal;
                $data['dataTerms'] = $dataTerms;
                $data['dataAlamat'] = $dataAlamat;
                $data['delivery'] = $delivery;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Detil',
                    'desc' => 'Detil Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_order_internal.detail', $data);
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order_internal.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order_internal.*'
                                            )
                                            ->where([
                                                ['sales_order_internal.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
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
                                            ->where('flag_so', 'Y')
                                            ->first();
                $detailSalesOrder = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_internal_detail.id',
                                                                'sales_order_internal_detail.id_item',
                                                                'sales_order_internal_detail.qty_item',
                                                                'sales_order_internal_detail.harga_jual',
                                                                'sales_order_internal_detail.keterangan',
                                                                DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_internal_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesOrder'] = $detailSalesOrder;
                $data['dataSales'] = $dataSales;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesOrder::cetakPdfSO($data);

                $fpdf->Output('I', strtoupper($dataSalesOrder->no_so).".pdf");
                exit;
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order_internal.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order_internal.*'
                                            )
                                            ->where([
                                                ['sales_order_internal.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
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
                                            ->where('flag_so', 'Y')
                                            ->first();
                $detailSalesOrder = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_internal_detail.id',
                                                                'sales_order_internal_detail.id_item',
                                                                'sales_order_internal_detail.qty_item',
                                                                'sales_order_internal_detail.harga_jual',
                                                                DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_internal_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;


                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesOrder'] = $detailSalesOrder;
                $data['dataSales'] = $dataSales;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Sales Order',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_order_internal.print', $data);
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakInvDP($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order_internal.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order_internal.*'
                                            )
                                            ->where([
                                                ['sales_order_internal.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
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
                                            ->where('flag_so', 'Y')
                                            ->first();
                $detailSalesOrder = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_internal_detail.id',
                                                                'sales_order_internal_detail.id_item',
                                                                'sales_order_internal_detail.qty_item',
                                                                'sales_order_internal_detail.harga_jual',
                                                                'sales_order_internal_detail.keterangan',
                                                                DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_internal_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesOrder'] = $detailSalesOrder;
                $data['dataSales'] = $dataSales;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Invoice DP',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesOrder::cetakPdfInvDP($data);
                $no_inv = str_replace("so", "INVDP", $dataSalesOrder->no_so);

                $fpdf->Output('I',strtoupper($no_inv).".pdf");
                exit;
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakInvPelunasan($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order_internal.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order_internal.*'
                                            )
                                            ->where([
                                                ['sales_order_internal.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
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
                                            ->where('flag_so', 'Y')
                                            ->first();
                $detailSalesOrder = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_internal_detail.id',
                                                                'sales_order_internal_detail.id_item',
                                                                'sales_order_internal_detail.qty_item',
                                                                'sales_order_internal_detail.harga_jual',
                                                                'sales_order_internal_detail.keterangan',
                                                                DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_internal_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();

                $shipDate = Delivery::select(
                                DB::raw('MAX(delivery.tanggal_sj) AS lastDate'), 'delivery.kode_pengiriman'
                            )
                            ->whereIn('delivery.id', function($subQuery) use ($id) {
                                $subQuery->select('id_sj')->from('sales_invoice_detail')
                                ->where('id_invoice', $id);
                            })
                            ->first();

                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $dataAlamatPenagihan = CustomerDetail::where([
                    ['id_customer', '=', $dataSalesOrder->id_customer],
                    ['jenis_alamat', '=', 'Penagihan']
                ])
                ->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataAlamatPenagihan'] = $dataAlamatPenagihan;
                $data['detailSalesOrder'] = $detailSalesOrder;
                $data['dataSales'] = $dataSales;
                $data['shipDate'] = $shipDate;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Invoice Pelunasan',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesOrder::cetakPdfInvPelunasan($data);
                $no_inv = str_replace("so", "PINV", $dataSalesOrder->no_so);

                $fpdf->Output('I', strtoupper($no_inv).".pdf");
                exit;
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakInvPerforma($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesOrderInternal'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrderInternal::leftJoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order_internal.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order_internal.*'
                                            )
                                            ->where([
                                                ['sales_order_internal.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderInternalTerms::where('id_so', $id)->get();
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
                                            ->where('flag_so', 'Y')
                                            ->first();
                $detailSalesOrder = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_internal_detail.id',
                                                                'sales_order_internal_detail.id_item',
                                                                'sales_order_internal_detail.qty_item',
                                                                'sales_order_internal_detail.harga_jual',
                                                                'sales_order_internal_detail.keterangan',
                                                                DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_internal_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();

                $shipDate = Delivery::select(
                                DB::raw('MAX(delivery.tanggal_sj) AS lastDate'), 'delivery.kode_pengiriman'
                            )
                            ->whereIn('delivery.id', function($subQuery) use ($id) {
                                $subQuery->select('id_sj')->from('sales_invoice_detail')
                                ->where('id_invoice', $id);
                            })
                            ->first();

                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $dataAlamatPenagihan = CustomerDetail::where([
                    ['id_customer', '=', $dataSalesOrder->id_customer],
                    ['jenis_alamat', '=', 'Penagihan']
                ])
                ->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataSalesOrder'] = $dataSalesOrder;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataAlamatPenagihan'] = $dataAlamatPenagihan;
                $data['detailSalesOrder'] = $detailSalesOrder;
                $data['dataSales'] = $dataSales;
                $data['shipDate'] = $shipDate;

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Invoice Pelunasan',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesOrder::cetakPdfInvPelunasan($data);
                $no_inv = str_replace("so", "PINV", $dataSalesOrder->no_so);

                $fpdf->Output('I', strtoupper($no_inv).".pdf");
                exit;
            }
            else {
                return redirect('/SalesOrderInternal')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
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
        $idAlamat = $request->input('id_alamat');

        if ($idProduct != "" && $idSatuan != "") {
            $GroupCustomer = CustomerGroupDetail::leftJoin('customer_group', 'customer_group_detail.id_group', '=', 'customer_group.id')
                ->select(
                    'customer_group_detail.id_group',
                    'customer_group.flag_harga'
                )
                ->where([
                ['id_customer', '=', $idCustomer]
            ])
            ->first();

            $DataGroupCustomer = new stdClass();
            $flagHarga = 0;
            if ($GroupCustomer != null) {
                $isInGroup = 1;
                $flagHarga = $GroupCustomer->flag_harga;
                $DataGroupCustomer = CustomerGroupDetail::where([
                    ['id_group', '=', $GroupCustomer->id_group]
                ])
                ->get()->pluck('id_customer');
            }
            else {
                $isInGroup = 0;
            }

            $hargaJualQuotation = QuotationDetail::leftJoin('quotation', 'quotation_detail.id_quotation', '=', 'quotation.id')
                                                        ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_quotation"))
                                                        ->whereIn('quotation.tanggal_quotation', function($querySubQuo) use ($idProduct, $idCustomer, $idSatuan) {
                                                            $querySubQuo->select(DB::raw("MAX(quotation.tanggal_quotation)"))->from("quotation")
                                                                    ->leftJoin('quotation_detail', 'quotation_detail.id_quotation', '=', 'quotation.id')
                                                                    ->whereNotIn('quotation.status_quotation', ['draft', 'cancel'])
                                                                    ->where([
                                                                        ['quotation.id_customer', '=', $idCustomer],
                                                                        ['quotation_detail.id_satuan', '=', $idSatuan],
                                                                        ['quotation_detail.id_item', '=', $idProduct]
                                                                    ]);
                                                        })
                                                        ->when($isInGroup == 1 && $flagHarga == 1, function($q) use ($DataGroupCustomer) {
                                                        $q->whereIn('quotation.id_customer', $DataGroupCustomer);
                                                    })
                                                    ->when($isInGroup == 0, function($q) use ($idCustomer) {
                                                        $q->where('quotation.id_customer', '=', $idCustomer);
                                                    })
                                                    ->where([
                                                            // ['quotation.id_customer', '=', $idCustomer],
                                                            ['quotation_detail.id_satuan', '=', $idSatuan],
                                                            ['quotation_detail.id_item', '=', $idProduct]
                                                        ]);

            $hargaJualTerakhir = SalesOrderInternalDetail::leftJoin('sales_order_internal', 'sales_order_internal_detail.id_so', '=', 'sales_order_internal.id')
                                                    ->select('id_so', 'id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
                                                    ->whereIn('sales_order_internal.tanggal_so', function($querySub) use ($idProduct, $idCustomer, $idSatuan, $idAlamat) {
                                                        $querySub->select(DB::raw("MAX(sales_order_internal.tanggal_so)"))->from("sales_order_internal")
                                                                ->leftJoin('sales_order_internal_detail', 'sales_order_internal_detail.id_so', '=', 'sales_order_internal.id')
                                                                ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order_internal.id')
                                                                ->whereNotIn('sales_order_internal.status_so', ['draft', 'cancel'])
                                                                ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                                ->where([
                                                                    ['sales_order_internal.id_customer', '=', $idCustomer],
                                                                    ['sales_order_internal.id_alamat', '=', $idAlamat],
                                                                    ['sales_order_internal_detail.id_satuan', '=', $idSatuan],
                                                                    ['sales_order_internal_detail.id_item', '=', $idProduct]
                                                                ]);
                                                    })
                                                    ->when($isInGroup == 1, function($q) use ($DataGroupCustomer) {
                                                    $q->whereIn('sales_order_internal.id_customer', $DataGroupCustomer);
                                                })
                                                ->when($isInGroup == 0, function($q) use ($idCustomer) {
                                                    $q->where('sales_order_internal.id_customer', '=', $idCustomer);
                                                })
                                                ->where([
                                                        // ['sales_order_internal.id_customer', '=', $idCustomer],
                                                        ['sales_order_internal_detail.id_satuan', '=', $idSatuan],
                                                        ['sales_order_internal_detail.id_item', '=', $idProduct]
                                                    ]);

            $hargaBeliTerakhir = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                    ->select('id_po', 'id_item', 'id_satuan', DB::raw("harga_beli AS harga_beli_last"))
                                                    ->whereIn('purchase_order.tanggal_po', function($querySub) use ($idProduct, $idSatuan) {
                                                        $querySub->select(DB::raw("MAX(purchase_order.tanggal_po)"))->from("purchase_order")
                                                                ->leftJoin('purchase_order_detail', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                                                ->leftJoin('purchase_invoice', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                                ->whereNotIn('purchase_order.status_po', ['draft', 'cancel'])
                                                                ->whereNotIn('purchase_invoice.status_invoice', ['draft', 'cancel'])
                                                                ->where([
                                                                    ['purchase_order_detail.id_satuan', '=', $idSatuan],
                                                                    ['purchase_order_detail.id_item', '=', $idProduct]
                                                                ]);
                                                    })
                                                    ->where([
                                                        // ['purchase_order.id_customer', '=', $idCustomer],
                                                        ['purchase_order_detail.id_satuan', '=', $idSatuan],
                                                        ['purchase_order_detail.id_item', '=', $idProduct]
                                                    ]);

            $stokIn = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
                                        ->where([
                                                    ['transaksi', '=', 'in']
                                                ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan');

            $stokOut = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
                                        ->where([
                                            ['transaksi', '=', 'out']
                                        ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan');

            $dataProduct = Product::leftJoin('product_detail', 'product_detail.id_product', '=', 'product.id')
                                    ->leftJoinSub($hargaJualQuotation, 'hargaJualQuotation', function($hargaJualQuotation) {
                                        $hargaJualQuotation->on('product.id', '=', 'hargaJualQuotation.id_item');
                                        $hargaJualQuotation->on('product_detail.id_satuan', '=', 'hargaJualQuotation.id_satuan');
                                    })
                                    ->leftJoinSub($hargaJualTerakhir, 'hargaJualTerakhir', function($hargaJualTerakhir) {
                                        $hargaJualTerakhir->on('product.id', '=', 'hargaJualTerakhir.id_item');
                                        $hargaJualTerakhir->on('product_detail.id_satuan', '=', 'hargaJualTerakhir.id_satuan');
                                    })
                                    ->leftJoinSub($hargaBeliTerakhir, 'hargaBeliTerakhir', function($hargaBeliTerakhir) {
                                        $hargaBeliTerakhir->on('product.id', '=', 'hargaBeliTerakhir.id_item');
                                        $hargaBeliTerakhir->on('product_detail.id_satuan', '=', 'hargaBeliTerakhir.id_satuan');
                                    })
                                    ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                        $join_in->on('product.id', '=', 'stokIn.id_item');
                                        $join_in->on('product_detail.id_satuan', '=', 'stokIn.id_satuan');

                                    })
                                    ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                        $join_out->on('product.id', '=', 'stokOut.id_item');
                                        $join_out->on('product_detail.id_satuan', '=', 'stokOut.id_satuan');
                                    })
                                    ->leftJoin('product_unit', 'product_detail.id_satuan', 'product_unit.id')
                                    ->select(
                                        'product_detail.harga_jual',
                                        DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                        DB::raw("COALESCE(hargaJualQuotation.harga_jual_quotation,0) AS harga_jual_quotation"),
                                        DB::raw("COALESCE(hargaJualTerakhir.harga_jual_last,0) AS harga_jual_last"),
                                        DB::raw("COALESCE(hargaBeliTerakhir.harga_beli_last,0) AS harga_beli_last"),
                                        DB::raw("(CASE WHEN COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) < 0 THEN 'Stok Minus' WHEN COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) = 0 THEN 'Kosong' WHEN COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) <= product_detail.stok_minimum THEN 'Stok Menipis' WHEN COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) >= product_detail.stok_maksimum THEN 'Stok Melebihi Batas' END) AS status_stok")
                                    )
                                    ->where([
                                        ['product.id', '=', $idProduct],
                                        ['product_detail.id_satuan', '=', $idSatuan],
                                    ])
                                    ->orderBy('hargaJualTerakhir.id_so', 'desc')
                                    ->first();
        }
        else {
            $dataProduct = null;
        }



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

    public function RestoreSalesOrderDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idSo');
            $restore = SalesOrderInternalDetail::onlyTrashed()->where([['id_so', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
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
                                        'product.*',
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

    public function getProductHistory(Request $request)
    {
        $idCustomer = $request->input('id_customer');
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
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

            $dataProduct = Delivery::leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', 'delivery.id')
                                    ->leftJoin('sales_order_internal', 'delivery.id_so', '=', 'sales_order_internal.id')
                                    ->leftJoin('sales_order_internal_detail', function($join) {
                                        $join->on('sales_order_internal.id' , '=', 'sales_order_internal_detail.id_so');
                                        $join->on('delivery_detail.id_item', '=', 'sales_order_internal_detail.id_item');
                                        $join->on('delivery_detail.id_satuan', '=', 'sales_order_internal_detail.id_satuan');
                                    })
                                    ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order_internal.id')
                                    ->leftjoin('customer', 'sales_order_internal.id_customer', '=', 'customer.id')
                                    ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoin('customer_detail', 'customer_detail.id', '=', 'sales_order_internal.id_alamat')
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_unit.nama_satuan',
                                        'customer.nama_customer',
                                        'sales_order_internal.no_so',
                                        'sales_order_internal_detail.harga_jual',
                                        'delivery.tanggal_sj',
                                        'delivery.kode_pengiriman',
                                        'delivery_detail.qty_item',
                                        'sales_invoice.kode_invoice',
                                        'customer_detail.nama_outlet',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->where([
                                        ['customer.id', '=', $idCustomer],
                                        ['product.id', '=', $idProduct],
                                        ['product_unit.id', '=', $idSatuan]
                                    ])
                                    ->orderBy('sales_order_internal.tanggal_so', 'desc')
                                    ->groupBy('delivery.id_so')
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

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function getPreviousOrder(Request $request)
    {
        $idCustomer = $request->input('id_customer');
        $dataSalesOrder = "";

        if ($idCustomer != "") {
            $dataSalesOrder = SalesOrderInternal::where([
                                                    ['sales_order_internal.id_customer', '=', $idCustomer]
                                                ])
                                                ->whereNotIn('sales_order_internal.status_so', ['draft', 'batal'])
                                                ->orderBy('sales_order_internal.tanggal_so', 'desc')
                                                ->first();

        }

        return response()->json($dataSalesOrder);
    }

    public function StoreSalesOrderInternalDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idSo');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $hargaJual = $request->input('hargaJual');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $hargaJual = str_replace(",", ".", $hargaJual);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';
                $countItem = DB::table('sales_order_internal_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_so', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new SalesOrderInternalDetail();
                    $listItem->id_so = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_order = $qty;
                    $listItem->qty_item = $qty;
                    $listItem->qty_outstanding = $qty;
                    $listItem->harga_jual = $hargaJual;
                    $listItem->keterangan = $keterangan;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Order Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {
                //Legend
                // 'value1' => $detail->id_so,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,
                // 'value5' => $detail->qty_outstanding,
                // 'value6' => $detail->qty_order,
                // 'value7' => $detail->harga_jual

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'sales_order_internal'],
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
                    $listItem->module = 'sales_order_internal';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value6 = $qty;
                    $listItem->value4 = $qty;
                    $listItem->value5 = $qty;
                    $listItem->value7 = $hargaJual;
                    $listItem->value8 = $keterangan;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Order Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Order Detail',
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

    public function UpdateSalesOrderInternalDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idSo');
            $idItem = $request->input('idItem');
            $idDetail = $request->input('idDetail');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $hargaJual = $request->input('hargaJual');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            $hargaJual = str_replace(",", ".", $hargaJual);
            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';
                $listItem = SalesOrderInternalDetail::find($idDetail);
                $listItem->id_so = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_item = $qty;
                $listItem->qty_order = $qty;
                $listItem->qty_outstanding = $qty;
                $listItem->harga_jual = $hargaJual;
                $listItem->keterangan = $keterangan;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {
                //Legend
                // 'value1' => $detail->id_so,
                // 'value2' => $detail->id_item,
                // 'value3' => $detail->id_satuan,
                // 'value4' => $detail->qty_item,
                // 'value5' => $detail->qty_outstanding,
                // 'value6' => $detail->qty_order,
                // 'value7' => $detail->harga_jual
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->value6 = $qty;
                $listItem->value5 = $qty;
                $listItem->value7 = $hargaJual;
                $listItem->value8 = $keterangan;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'Sales Order Detail',
                'action' => 'Update',
                'desc' => 'Update Sales Order Detail',
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

    public function GetSalesOrderInternalDetail(Request $request)
    {
        $id = $request->input('idSalesOrderInternal');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $detail = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'sales_order_internal_detail.id',
                                            'sales_order_internal_detail.id_item',
                                            'sales_order_internal_detail.qty_item',
                                            'sales_order_internal_detail.qty_outstanding',
                                            'sales_order_internal_detail.harga_jual',
                                            'sales_order_internal_detail.keterangan',
                                            DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['sales_order_internal_detail.id_so', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_order_internal_detail.created_by', $user);
                                        })
                                        ->get();
        }
        else {
            //Legend
            // 'value1' => $detail->id_so,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,
            // 'value5' => $detail->qty_outstanding,
            // 'value6' => $detail->qty_order,
            // 'value7' => $detail->harga_jual
            // 'value7' => $detail->keterangan
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            DB::raw('COALESCE(temp_transaction.value7,0) * COALESCE(temp_transaction.value4) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_order_internal']
                                        ])
                                        ->get();
        }



        return response()->json($detail);

    }


    public function EditSalesOrderInternalDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {
            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $detail = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'sales_order_internal_detail.id',
                                            'sales_order_internal_detail.id_item',
                                            'sales_order_internal_detail.id_satuan',
                                            'sales_order_internal_detail.qty_item',
                                            'sales_order_internal_detail.harga_jual',
                                            'sales_order_internal_detail.keterangan',
                                            DB::raw('COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['sales_order_internal_detail.id', '=', $id]
                                        ])
                                        ->get();
        }
        else {
            //Legend
            // 'value1' => $detail->id_so,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,
            // 'value5' => $detail->qty_outstanding,
            // 'value6' => $detail->qty_order,
            // 'value7' => $detail->harga_jual
            // 'value7' => $detail->keterangan
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            DB::raw('COALESCE(temp_transaction.value7,0) * COALESCE(temp_transaction.value4) AS subtotal'),
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_order_internal']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteSalesOrderInternalDetail(Request $request)
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
                $delete = DB::table('sales_order_internal_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetSalesOrderInternalFooter(Request $request)
    {
        $id = $request->input('idSo');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = SalesOrderInternalDetail::leftJoin('product', 'sales_order_internal_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_order_internal_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(sales_order_internal_detail.qty_item) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(sales_order_internal_detail.harga_jual,0) * COALESCE(sales_order_internal_detail.qty_item,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['sales_order_internal_detail.id_so', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_order_internal_detail.created_by', $user);
                                        })
                                        ->groupBy('sales_order_internal_detail.id_so')
                                        ->first();
        }
        else {
            //Legend
            // 'value1' => $detail->id_so,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,
            // 'value5' => $detail->qty_outstanding,
            // 'value6' => $detail->qty_order,
            // 'value7' => $detail->harga_jual
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(temp_transaction.value4) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(temp_transaction.value7,0) * COALESCE(temp_transaction.value4,0)) AS subtotal')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_order_internal']
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
            'tanggal_so'=>'required',
            'tanggal_req'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tglSo = $request->input('tanggal_so');
        $flagPpn = $request->input('status_ppn');

        $bulanIndonesia = Carbon::parse($tglSo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSo);
        if (!$aksesTransaksi) {
            return redirect('/SalesOrderInternal')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglSo);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/SalesOrderInternal')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $poCustomer = $request->input('po_customer');
            $tglSo = $request->input('tanggal_so');
            $tglReq = $request->input('tanggal_req');
            $metodeBayar = $request->input('metode_bayar');
            $metodePengiriman = $request->input('metode_kirim');
            $persenDiskon = $request->input('persen_diskon');
            $flagPPn = $request->input('status_ppn');
            $dpp =  $request->input('dpp');
            $ppn = $request->input('ppn');
            $grandTotal = $request->input('gt');
            $jenisDiskon = $request->input('jenis_diskon');
            $persenDiskon = $request->input('disc_percent');
            $nominalDiskon = $request->input('disc_nominal');
            $durasiJt = $request->input('durasi_jt');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyItem = str_replace(",", ".", $qtyItem);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $grandTotal = str_replace(",", ".", $grandTotal);

            $blnPeriode = date("m", strtotime($tglSo));
            $thnPeriode = date("Y", strtotime($tglSo));
            $tahunPeriode = date("y", strtotime($tglSo));

            $countKode = DB::table('sales_order_internal')
                            ->select(DB::raw("MAX(RIGHT(no_so,2)) AS angka"))
                            // ->whereMonth('tanggal_so', $blnPeriode)
                            // ->whereYear('tanggal_so', $thnPeriode)
                            ->whereDate('tanggal_so', $tglSo)
                            ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSo)->format('ymd');

            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSo))));
            if ($counter < 10) {
                $nmrSo = "soi-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrSo = "soi-cv-".$kodeTgl.$counter;
            }

            $file = $request->file('file_po_customer');
            if ($file != "") {
                $ext = $file->getClientOriginalExtension();
                $namaFile = $nmrSo.".".$ext;
                $request->file('file_po_customer')->storeAs('sales_order_internal/', $namaFile, 'documents');
            }
            else {
                $namaFile = "";
            }



            $salesOrder = new SalesOrderInternal();
            $salesOrder->no_so = $nmrSo;
            $salesOrder->no_po_customer = $poCustomer;
            $salesOrder->id_customer = $idCustomer;
            $salesOrder->id_alamat = $idAlamat;
            $salesOrder->jumlah_total_so = $qtyItem;
            $salesOrder->outstanding_so = $qtyItem;
            $salesOrder->tanggal_so = $tglSo;
            $salesOrder->tanggal_request = $tglReq;
            $salesOrder->flag_ppn = $flagPPn;
            $salesOrder->nominal_dp = 0;
            $salesOrder->sisa_dp = 0;
            $salesOrder->nominal_so_dpp = $dpp;
            $salesOrder->nominal_so_ppn = $ppn;
            $salesOrder->nominal_so_ttl = $grandTotal;
            $salesOrder->jenis_diskon = $jenisDiskon;
            $salesOrder->nominal_diskon = $nominalDiskon;
            $salesOrder->persentase_diskon = $persenDiskon;
            $salesOrder->metode_pembayaran = $metodeBayar;
            $salesOrder->metode_kirim = $metodePengiriman;
            if ($metodePengiriman == "ekspedisi") {
                $salesOrder->jenis_kirim = $request->input('ekspedisi');
            }
            $salesOrder->durasi_jt = $durasiJt;
            $salesOrder->path_po = $namaFile;
            $salesOrder->status_so = 'draft';
            $salesOrder->id_ppn = $taxSettings->ppn_percentage_id;
            $salesOrder->created_by = $user;
            $salesOrder->save();

            $data = $salesOrder;

            $setDetail = DB::table('sales_order_internal_detail')
                            ->where([
                                        ['id_so', '=', 'DRAFT'],
                                        ['created_by', '=', Auth::user()->user_name]
                                    ])
                            ->update([
                                'id_so' => $salesOrder->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_so' => $salesOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                salesOrderInternalTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Sales Order',
                'action' => 'Simpan',
                'desc' => 'Simpan Sales Order',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('SalesOrderInternal.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_so).' Telah Disimpan!');
        }
        else {
            return redirect('/SalesOrderInternal')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'id_alamat'=>'required',
            'tanggal_so'=>'required',
            'tanggal_req'=>'required',
            'metode_bayar'=>'required'
        ]);

        $tglSo = $request->input('tanggal_so');
        $flagPpn = $request->input('status_ppn');

        $bulanIndonesia = Carbon::parse($tglSo)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSo);
        if (!$aksesTransaksi) {
            return redirect()->route('SalesOrderInternal.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglSo);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/SalesOrderInternal')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $tglSo = $request->input('tanggal_so');
            $poCustomer = $request->input('po_customer');
            $tglReq = $request->input('tanggal_req');
            $metodeBayar = $request->input('metode_bayar');
            $persenDiskon = $request->input('persen_diskon');
            $metodePengiriman = $request->input('metode_kirim');
            $flagPPn = $request->input('status_ppn');
            $dpp =  $request->input('dpp');
            $ppn = $request->input('ppn');
            $grandTotal = $request->input('gt');
            $jenisDiskon = $request->input('jenis_diskon');
            $persenDiskon = $request->input('disc_percent');
            $nominalDiskon = $request->input('disc_nominal');
            $durasiJt = $request->input('durasi_jt');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyItem = str_replace(",", ".", $qtyItem);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $grandTotal = str_replace(",", ".", $grandTotal);

            $blnPeriode = date("m", strtotime($tglSo));
            $thnPeriode = date("Y", strtotime($tglSo));

            $updateFile = $request->input('file_po_customer');

            $countKode = DB::table('sales_order_internal')
                            ->select(DB::raw("MAX(RIGHT(no_so,2)) AS angka"))
                            // ->whereMonth('tanggal_so', $blnPeriode)
                            // ->whereYear('tanggal_so', $thnPeriode)
                            ->whereDate('tanggal_so', $tglSo)
                            ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSo)->format('ymd');

            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSo))));
            if ($counter < 10) {
                $nmrSo = "soi-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrSo = "soi-cv-".$kodeTgl.$counter;
            }

            $salesOrder = SalesOrderInternal::find($id);
            if ($tglSo != $salesOrder->tanggal_so) {
                $salesOrder->no_so = $nmrSo;
            }
            $updateFile = $request->file('file_po_customer');
            if ($updateFile != "") {
                Storage::disk('documents')->delete('sales_order_internal/'.$updateFile);
                $ext = $updateFile->getClientOriginalExtension();
                $namaFile = $salesOrder->no_so.".".$ext;
                $request->file('file_po_customer')->storeAs('sales_order_internal/', $namaFile, 'documents');
                $updateFile = $namaFile;
                $salesOrder->path_po = $namaFile;
            }
            $salesOrder->id_customer = $idCustomer;
            $salesOrder->no_po_customer = $poCustomer;
            $salesOrder->id_alamat = $idAlamat;
            $salesOrder->jumlah_total_so = $qtyItem;
            $salesOrder->outstanding_so = $qtyItem;
            $salesOrder->tanggal_so = $tglSo;
            $salesOrder->tanggal_request = $tglReq;
            $salesOrder->flag_ppn = $flagPPn;
            $salesOrder->nominal_so_dpp = $dpp;
            $salesOrder->nominal_so_ppn = $ppn;
            $salesOrder->nominal_so_ttl = $grandTotal;
            $salesOrder->jenis_diskon = $jenisDiskon;
            $salesOrder->nominal_diskon = $nominalDiskon;
            $salesOrder->persentase_diskon = $persenDiskon;
            $salesOrder->metode_pembayaran = $metodeBayar;
            $salesOrder->metode_kirim = $metodePengiriman;
            if ($metodePengiriman == "ekspedisi") {
                $salesOrder->jenis_kirim = $request->input('ekspedisi');
            }
            $salesOrder->durasi_jt = $durasiJt;
            $salesOrder->id_ppn = $taxSettings->ppn_percentage_id;
            $salesOrder->updated_by = $user;
            $salesOrder->save();

            // $deletedDetail = SalesOrderInternalDetail::onlyTrashed()->where([['id_so', '=', $id]]);
            // $deletedDetail->forceDelete();

            $data = $salesOrder;

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'sales_order_internal'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            //Legend
            // 'value1' => $detail->id_so,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,
            // 'value5' => $detail->qty_outstanding,
            // 'value6' => $detail->qty_order,
            // 'value7' => $detail->harga_jual

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = SalesOrderInternalDetail::find($detail->id_detail);
                        $listItem->id_so = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->qty_order = $detail->value6;
                        $listItem->qty_outstanding = $detail->value5;
                        $listItem->harga_jual = $detail->value7;
                        $listItem->keterangan = $detail->value8;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new SalesOrderInternalDetail();
                        $listItem->id_so = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->qty_order = $detail->value6;
                        $listItem->qty_outstanding = $detail->value5;
                        $listItem->harga_jual = $detail->value7;
                        $listItem->keterangan = $detail->value8;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('sales_order_internal_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_order_internal'],
                                    ['value1', '=', $id]
                                ])->delete();


            if ($terms != "") {
                $delete = DB::table('sales_order_terms')->where('id_so', '=', $salesOrder->id)->delete();
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_so' => $salesOrder->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                salesOrderInternalTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Sales Order',
                'action' => 'Update',
                'desc' => 'Update Sales Order',
                'username' => Auth::user()->user_name
            ]);
        });
        if (is_null($exception)) {
            return redirect()->route('SalesOrderInternal.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_so).' Telah Diupdate!');
        }
        else {
            return redirect('/SalesOrderInternal')->with('error', $exception);
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
            $salesOrder = SalesOrderInternal::find($id);
            $data = $salesOrder;

            $cekSjPosted = Delivery::where([
                ['id_so', '=', $id],
                ['status_pengiriman', '!=', 'draft']
            ])
            ->count();

            if ($btnAction == "posting") {
                $salesOrder->nominal_dp = $dp;
                $salesOrder->sisa_dp = $dp;
                $salesOrder->status_so = "posted";
                $salesOrder->save();
                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Posting',
                    'desc' => 'Posting Sales Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($salesOrder->no_so).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "kirim") {
                $status = "kirim";
            }
            elseif ($btnAction == "revisi") {
                $salesOrder->status_so = "draft";
                $salesOrder->flag_revisi = '1';
                $salesOrder->updated_by = Auth::user()->user_name;
                $salesOrder->save();

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Sales Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Sales Order '.strtoupper($salesOrder->no_so).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "tutup") {
                if ($cekSjPosted != 0) {
                    $salesOrder->status_so = "close";
                    $salesOrder->updated_by = Auth::user()->user_name;
                    $salesOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Order',
                        'action' => 'Tutup',
                        'desc' => 'Tutup Sales Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Sales Order '.strtoupper($salesOrder->no_so).' Telah Ditutup!';
                    $status = 'success';
                }
                else {
                    $msg = 'Sales Order '.strtoupper($salesOrder->no_so).' Tidak dapat Ditutup karena belum terdapat Surat Jalan Pengiriman atas Sales Order '.strtoupper($salesOrder->no_so).' !';
                    $status = 'warning';
                }
            }
            elseif ($btnAction == "batal") {
                if ($cekSjPosted == 0) {
                    $salesOrder->status_so = "batal";
                    $salesOrder->updated_by = Auth::user()->user_name;
                    $salesOrder->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Order',
                        'action' => 'Batal',
                        'desc' => 'Batal Sales Order',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Sales Order '.strtoupper($salesOrder->no_so).' Telah Dibatalkan!';
                    $status = 'success';
                }
                else {
                    $msg = 'Sales Order '.strtoupper($salesOrder->no_so).' Tidak dapat Dibatalkan karena terdapat Surat Jalan Penerimaan atas Sales Order '.strtoupper($salesOrder->no_so).' !';
                    $status = 'warning';
                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('SalesOrderInternal.edit', [$id]);
            }
            elseif($status == "kirim") {
                Session::put('id_so', $id);
                Session::put('id_cust', $data->id_customer);
                Session::save();

                return redirect('Delivery/Add');
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
            $id = $request->input('idsalesOrder');
            $user = Auth::user()->user_name;
            $delete = SalesOrderInternal::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Sales Order',
                'action' => 'Delete',
                'desc' => 'Delete Sales Order',
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

    public function ResetSalesOrderInternalDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idSO');


            if ($id != "DRAFT") {
                // $detail = TempTransaction::where([
                //                             ['value1', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                                    ['module', '=', 'sales_order_internal'],
                                                    ['value1', '=', $id]
                                                ])
                                                ->update([
                                                    'action' => 'hapus',
                                                    'deleted_at' => now(),
                                                    'deleted_by' => Auth::user()->user_name
                                                ]);
            }
            else {
                $delete = DB::table('sales_order_internal_detail')->where('id_so', '=', $id)->delete();
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

    public function exportDataSalesOrder(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new SalesOrderExport($request), 'SalesOrder_'.$kodeTgl.'.xlsx');
    }

    public function SetSalesOrderInternalDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idSo');
            $idQuo = $request->input('idQuotation');
            if ($id == "") {
                $id = 'DRAFT';
            }

            $delete = DB::table('sales_order_internal_detail')->where('id_so', '=', $id)->delete();

            $detail = QuotationDetail::select(
                                            'quotation_detail.id_item',
                                            'quotation_detail.qty_item',
                                            'quotation_detail.harga_jual',
                                            'quotation_detail.keterangan'
                                        )
                                        ->where([
                                            ['quotation_detail.id_quotation', '=', $idQuo]
                                        ])
                                        ->get();
            $data = $detail;
            $listDetail = [];
            foreach ($detail As $detailQuo) {

                $dataDetail = [
                    'id_so' => $id,
                    'id_item' => $detailQuo->id_item,
                    'qty_item' => $detailQuo->qty_item,
                    'qty_outstanding' => $detailQuo->qty_item,
                    'qty_order' => $detailQuo->qty_item,
                    'harga_jual' => $detailQuo->harga_jual,
                    'keterangan' => $detailQuo->keterangan,
                    'created_at' => now(),
                    'created_by' => Auth::user()->user_name,
                ];
                array_push($listDetail, $dataDetail);
            }
            SalesOrderInternalDetail::insert($listDetail);
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }
}
