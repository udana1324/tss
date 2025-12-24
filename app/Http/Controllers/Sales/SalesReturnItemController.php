<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
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
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Sales\SalesOrderTerms;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationDetail;
use App\Models\Sales\Delivery;
use App\Models\Library\ExpeditionBranch;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperSalesOrder;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\Sales;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Product\ProductUnit;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\SalesReturn;
use App\Models\Sales\SalesReturnItem;
use App\Models\Sales\SalesReturnItemDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class SalesReturnItemController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesReturnItem'],
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
                                                ['module.url', '=', '/SalesReturnItem'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = SalesReturnItem::distinct()->get('status_retur');
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('sales_return_item_detail')->where('deleted_at', '!=', null)->delete();
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Sales Return',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_return_item.index', $data);
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

        $salesOrder = SalesReturnItem::leftJoin('customer', 'sales_return_item.id_customer', '=', 'customer.id')
                            ->select(
                                'customer.nama_customer',
                                'sales_return_item.id',
                                'sales_return_item.kode_retur',
                                'sales_return_item.no_dokumen_retur',
                                'sales_return_item.jumlah_total_retur',
                                'sales_return_item.tanggal_retur',
                                'sales_return_item.flag_revisi',
                                'sales_return_item.status_retur')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('sales_return_item.tanggal_retur', Carbon::parse($periode)->format('m'));
                                $q->whereYear('sales_return_item.tanggal_retur', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('sales_return_item.id', 'desc')
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
                                            ['module.url', '=', '/SalesReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();

                $parentMenu = Module::find($hakAkses->parent);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

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

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['merk'] = ProductBrand::all();
                $data['kategori'] = ProductCategory::all();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['listIndex'] = $list;

                $log = ActionLog::create([
                    'module' => 'Sales Retur',
                    'action' => 'Buat',
                    'desc' => 'Buat Sales Retur',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('sales_return_item_detail')
                            ->where([
                                ['id_retur', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.sales_return_item.add', $data);
            }
            else {
                return redirect('/SalesReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataRetur = SalesReturnItem::find($id);

                if ($dataRetur->status_retur != "draft") {
                    return redirect('/SalesReturnItem')->with('warning', 'Retur tidak dapat diubah karena status Retur bukan DRAFT!');
                }

                // $restore = SalesOrderDetail::onlyTrashed()->where([['id_so', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_return_item'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = SalesReturnItemDetail::where([
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
                            'module' => 'sales_return_item',
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
                $data['dataCustomer'] = $dataCustomer;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Sales Return Item',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Return Item',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_return_item.edit', $data);
            }
            else {
                return redirect('/SalesReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesReturnItem'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataRetur = SalesReturnItem::find($id);
                $dataCustomer = Customer::find($dataRetur->id_customer);

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataRetur->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Sales Return Item',
                    'action' => 'Detil',
                    'desc' => 'Detil Sales Return Item',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_return_item.detail', $data);
            }
            else {
                return redirect('/SalesReturnItem')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order.*'
                                            )
                                            ->where([
                                                ['sales_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderTerms::where('id_so', $id)->get();
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
                $detailSalesOrder = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_detail.id',
                                                                'sales_order_detail.id_item',
                                                                'sales_order_detail.qty_item',
                                                                'sales_order_detail.harga_jual',
                                                                DB::raw('COALESCE(sales_order_detail.harga_jual,0) * COALESCE(sales_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_detail.id_so', '=', $id]
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
                return redirect('/SalesOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order.*'
                                            )
                                            ->where([
                                                ['sales_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderTerms::where('id_so', $id)->get();
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
                $detailSalesOrder = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_detail.id',
                                                                'sales_order_detail.id_item',
                                                                'sales_order_detail.qty_item',
                                                                'sales_order_detail.harga_jual',
                                                                DB::raw('COALESCE(sales_order_detail.harga_jual,0) * COALESCE(sales_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::find(1);

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

                return view('pages.sales.sales_order.print', $data);
            }
            else {
                return redirect('/SalesOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order.*'
                                            )
                                            ->where([
                                                ['sales_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderTerms::where('id_so', $id)->get();
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
                $detailSalesOrder = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_detail.id',
                                                                'sales_order_detail.id_item',
                                                                'sales_order_detail.qty_item',
                                                                'sales_order_detail.harga_jual',
                                                                DB::raw('COALESCE(sales_order_detail.harga_jual,0) * COALESCE(sales_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::find(1);

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
                return redirect('/SalesOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesOrder'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('expedition_branch', 'sales_order.jenis_kirim', '=', 'expedition_branch.id')
                                            ->select(
                                                'customer.kode_customer',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.telp_customer',
                                                'customer.fax_customer',
                                                'customer.email_customer',
                                                'customer.kategori_customer',
                                                'customer.sales',
                                                'sales_order.*'
                                            )
                                            ->where([
                                                ['sales_order.id', '=', $id],
                                            ])
                                            ->first();
                $dataTerms = SalesOrderTerms::where('id_so', $id)->get();
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
                $detailSalesOrder = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                                                            ->select(
                                                                'sales_order_detail.id',
                                                                'sales_order_detail.id_item',
                                                                'sales_order_detail.qty_item',
                                                                'sales_order_detail.harga_jual',
                                                                DB::raw('COALESCE(sales_order_detail.harga_jual,0) * COALESCE(sales_order_detail.qty_item) AS subtotal'),
                                                                'product.kode_item',
                                                                'product.jenis_item',
                                                                'product.nama_item',
                                                                'product_unit.nama_satuan'
                                                            )
                                                            ->where([
                                                                ['sales_order_detail.id_so', '=', $id]
                                                            ])
                                                            ->get();
                $dataSales = Sales::find($dataSalesOrder->sales);
                $dataAlamat = CustomerDetail::find($dataSalesOrder->id_alamat);
                $taxSettings = TaxSettings::find(1);

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
                    'desc' => 'Cetak Invoice Pelunasan',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesOrder::cetakPdfInvPelunasan($data);
                $no_inv = str_replace("so", "PINV", $dataSalesOrder->no_so);

                $fpdf->Output('I', strtoupper($no_inv).".pdf");
                exit;
            }
            else {
                return redirect('/SalesOrder')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getProductCustomer(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $dataProduct = CustomerProduct::leftJoin('product', 'customer_product.id_item', 'product.id')
                                        ->where([
                                            ['customer_product.id_customer', '=', $idCustomer],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->select('product.id', 'product.nama_item')
                                        ->whereIn('customer_product.id_item', function($subQuery) use ($idCustomer) {
                                            $subQuery->select('delivery_detail.id_item')->from('delivery_detail')->distinct()
                                            ->leftJoin('delivery', 'delivery.id', '=', 'delivery_detail.id_pengiriman')
                                            ->whereIn('delivery.id', function($sub2) use ($idCustomer) {
                                                $sub2->select('delivery.id')->from('sales_invoice_detail')
                                                ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'sales_invoice_detail.id_invoice')
                                                ->where('sales_invoice.status_invoice', 'posted')
                                                ->whereIn('sales_invoice.id_so', function($sub3) use ($idCustomer) {
                                                    $sub3->select('sales_order.id')->from('sales_order')
                                                    ->where('sales_order.id_customer', $idCustomer);
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
        $idIndex = $request->input('id_index');
        $idCustomer = $request->input('id_customer');

        if ($idProduct != "" && $idSatuan != "") {

            $dataRetur = SalesReturnItemDetail::select('id_item', 'id_satuan', DB::raw('COALESCE(SUM(qty_item),0) AS returned_item'))
                                        ->leftJoin('sales_return_item', 'sales_return_item.id', '=', 'sales_return_item_detail.id_retur')
                                        ->where([
                                                    ['sales_return_item.id_customer', '=', $idCustomer],
                                                    ['sales_return_item_detail.id_item', '=', $idProduct],
                                                    ['sales_return_item_detail.id_satuan', '=', $idSatuan],
                                                    ['sales_return_item.status_retur', '=', 'posted']
                                                ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->first();

            $dataProduct = StockTransaction::select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS sold_item'))
                                        ->leftJoin('delivery', 'delivery.kode_pengiriman', '=', 'stock_transaction.kode_transaksi')
                                        ->leftJoin('sales_order', 'sales_order.id', 'delivery.id_so')
                                        ->where([
                                            ['stock_transaction.transaksi', '=', 'out'],
                                            ['stock_transaction.jenis_transaksi', '=', 'pengiriman'],
                                            ['sales_order.id_customer', '=', $idCustomer],
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
            $restore = SalesOrderDetail::onlyTrashed()->where([['id_so', '=', $id]]);
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
            $dataProduct = Product::leftJoin('product_brand', 'product.merk_item', 'product_brand.id')
                                    ->leftJoin('product_category', 'product.kategori_item', 'product_category.id')
                                    ->select(
                                        'product.id',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_brand.nama_merk',
                                        'product_category.nama_kategori',
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

        if ($idCustomer != "" && $idProduct != "") {
            $dataProduct = Delivery::leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', 'delivery.id')
                                    ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('sales_order_detail', function($join) {
                                        $join->on('sales_order.id' , '=', 'sales_order_detail.id_so');
                                        $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                        $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                    })
                                    ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftjoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                    ->leftJoin('customer_detail', 'customer_detail.id', '=', 'sales_order.id_alamat')
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
                                        'sales_invoice.kode_invoice',
                                        'customer_detail.nama_outlet'
                                    )
                                    ->where([
                                        ['customer.id', '=', $idCustomer],
                                        ['product.id', '=', $idProduct],
                                        ['product_unit.id', '=', $idSatuan],
                                        ['delivery.status_pengiriman', '=', 'posted']
                                    ])
                                    ->orderBy('sales_order.tanggal_so', 'desc')
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
            $dataSalesOrder = SalesOrder::where([
                                                    ['sales_order.id_customer', '=', $idCustomer]
                                                ])
                                                ->whereNotIn('sales_order.status_so', ['draft', 'batal'])
                                                ->orderBy('sales_order.tanggal_so', 'desc')
                                                ->first();

        }

        return response()->json($dataSalesOrder);
    }

    public function StoreSalesReturnItemDetail(Request $request)
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
                $countItem = DB::table('sales_return_item_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_retur', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan],
                                    ['id_index', '=', $idIndex]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new SalesReturnItemDetail();
                    $listItem->id_retur = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->id_index = $idIndex;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Return Item Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Return Item Detail',
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
                                    ['module', '=', 'sales_return_item'],
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
                    $listItem->module = 'sales_return_item';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $idIndex;
                    $listItem->value5 = $qty;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Return Item Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Return Item Detail',
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

    public function UpdateSalesReturnItemDetail(Request $request)
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
                $listItem = SalesReturnItemDetail::find($idDetail);
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
                $listItem->value4 = $idIndex;
                $listItem->value5 = $qty;
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

    public function GetSalesReturnItemDetail(Request $request)
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

            $detail = SalesReturnItemDetail::leftJoin('product', 'sales_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_return_item_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'sales_return_item_detail.id',
                                            'sales_return_item_detail.id_item',
                                            'sales_return_item_detail.id_satuan',
                                            'sales_return_item_detail.id_index',
                                            'sales_return_item_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['sales_return_item_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_return_item_detail.created_by', $user);
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
                    'id_satuan' => $data->id_satuan,
                    'id_index' => $data->id_index,
                    'qty_item' => $data->qty_item,
                    'kode_item' => $data->kode_item,
                    'nama_item' => $data->nama_item,
                    'nama_satuan' => $data->nama_satuan,
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
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_return_item']
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
                    'txt_index' => $txtIndex,
                ];
                array_push($detailData, $dataAlloc);
            }
        }


        return response()->json($detailData);
    }

    public function EditSalesReturnItemDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $idCust = $request->input('idCust');
        $mode = $request->input('mode');

        if ($mode == "") {
            $detail = SalesReturnItemDetail::leftJoin('product', 'sales_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_return_item_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'sales_return_item_detail.id',
                                            'sales_return_item_detail.id_item',
                                            'sales_return_item_detail.id_satuan',
                                            'sales_return_item_detail.id_index',
                                            'sales_return_item_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['sales_return_item_detail.id', '=', $id]
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
                                            ['temp_transaction.module', '=', 'sales_return_item']
                                        ])
                                        ->first();

        }

        $dataRetur = SalesReturnItemDetail::select('id_item', 'id_satuan', DB::raw('COALESCE(SUM(qty_item),0) AS returned_item'))
                                        ->leftJoin('sales_return_item', 'sales_return_item.id', '=', 'sales_return_item_detail.id_retur')
                                        ->when($mode == "", function($q) use ($idCust, $detail) {
                                            $q->where([
                                                ['sales_return_item.id_customer', '=', $idCust],
                                                ['sales_return_item_detail.id_item', '=', $detail->id_item],
                                                ['sales_return_item_detail.id_satuan', '=', $detail->id_satuan],
                                                ['sales_return_item.status_retur', '=', 'posted']
                                            ]);
                                        })
                                        ->when($mode == "edit", function($q) use ($idCust, $detail) {
                                            $q->where([
                                                ['sales_return_item.id_customer', '=', $idCust],
                                                ['sales_return_item_detail.id_item', '=', $detail->value2],
                                                ['sales_return_item_detail.id_satuan', '=', $detail->value3],
                                                ['sales_return_item.status_retur', '=', 'posted']
                                            ]);
                                        })
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->first();

        $dataProduct = StockTransaction::leftJoin('delivery', 'delivery.kode_pengiriman', '=', 'stock_transaction.kode_transaksi')
                                        ->leftJoin('sales_order', 'sales_order.id', 'delivery.id_so')
                                        ->select('id_item', 'id_satuan', DB::raw('SUM(qty_item) AS sold_item'))
                                        ->when($mode == "", function($q) use ($idCust, $detail) {
                                            $q->where([
                                                ['stock_transaction.transaksi', '=', 'out'],
                                                ['stock_transaction.jenis_transaksi', '=', 'pengiriman'],
                                                ['sales_order.id_customer', '=', $idCust],
                                                ['stock_transaction.id_item', '=', $detail->id_item],
                                                ['stock_transaction.id_satuan', '=', $detail->id_satuan],
                                            ]);
                                        })
                                        ->when($mode == "edit", function($q) use ($idCust, $detail) {
                                            $q->where([
                                                ['stock_transaction.transaksi', '=', 'out'],
                                                ['stock_transaction.jenis_transaksi', '=', 'pengiriman'],
                                                ['sales_order.id_customer', '=', $idCust],
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

    public function DeleteSalesReturnItemDetail(Request $request)
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
                $delete = DB::table('sales_return_item_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetSalesReturnItemFooter(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = SalesReturnItemDetail::leftJoin('product', 'sales_return_item_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_return_item_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(sales_return_item_detail.qty_item) AS qtyItem'),
                                        )
                                        ->where([
                                            ['sales_return_item_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_return_item_detail.created_by', $user);
                                        })
                                        ->groupBy('sales_return_item_detail.id_retur')
                                        ->first();
        }
        else {
            //Legend
            // 'value1' => $detail->id_so,
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
                                            ['temp_transaction.module', '=', 'sales_return_item']
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
            'tanggal_retur'=>'required',
        ]);

        $tglRetur = $request->input('tanggal_retur');

        $bulanIndonesia = Carbon::parse($tglRetur)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglRetur);
        if (!$aksesTransaksi) {
            return redirect('/SalesReturnItem')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $noDokumen = $request->input('nmr_sj_retur');
            $tglRetur = $request->input('tanggal_retur');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));
            $tahunPeriode = date("y", strtotime($tglRetur));

            $countKode = DB::table('sales_return_item')
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
                $kodeRetur = "rsi-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeRetur = "rsi-cv-".$kodeTgl.$counter;
            }

            $SalesReturnItem = new SalesReturnItem();
            $SalesReturnItem->kode_retur = $kodeRetur;
            $SalesReturnItem->no_dokumen_retur = $noDokumen;
            $SalesReturnItem->id_customer = $idCustomer;
            $SalesReturnItem->jumlah_total_retur = $qtyItem;
            $SalesReturnItem->tanggal_retur = $tglRetur;
            $SalesReturnItem->keterangan = $keterangan;
            $SalesReturnItem->status_retur = 'draft';
            $SalesReturnItem->flag_revisi = 0;
            $SalesReturnItem->id_ppn = $taxSettings->ppn_percentage_id;
            $SalesReturnItem->created_by = $user;
            $SalesReturnItem->save();

            $data = $SalesReturnItem;

            $setDetail = DB::table('sales_return_item_detail')
                            ->where([
                                        ['id_retur', '=', 'DRAFT'],
                                        ['created_by', '=', Auth::user()->user_name]
                                    ])
                            ->update([
                                'id_retur' => $SalesReturnItem->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Sales Return Item',
                'action' => 'Simpan',
                'desc' => 'Simpan Sales Return Item',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('SalesReturnItem.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_so).' Telah Disimpan!');
        }
        else {
            return redirect('/SalesReturnItem')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'tanggal_retur'=>'required',
        ]);

        $tglRetur = $request->input('tanggal_retur');

        $bulanIndonesia = Carbon::parse($tglRetur)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglRetur);
        if (!$aksesTransaksi) {
            return redirect('/SalesReturnItem')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $noDokumen = $request->input('nmr_sj_retur');
            $tglRetur = $request->input('tanggal_retur');
            $qtyItem = $request->input('qtyTtl');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));

            $updateFile = $request->input('file_po_customer');



            $SalesReturnItem = SalesReturnItem::find($id);
            $SalesReturnItem->no_dokumen_retur = $noDokumen;
            $SalesReturnItem->id_customer = $idCustomer;
            $SalesReturnItem->jumlah_total_retur = $qtyItem;
            $SalesReturnItem->tanggal_retur = $tglRetur;
            $SalesReturnItem->keterangan = $keterangan;
            $SalesReturnItem->id_ppn = $taxSettings->ppn_percentage_id;
            $SalesReturnItem->updated_by = $user;
            $SalesReturnItem->save();

            // $deletedDetail = SalesOrderDetail::onlyTrashed()->where([['id_so', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                ['module', '=', 'sales_return_item'],
                                ['value1', '=', $id],
                                ['action', '!=' , null]
                            ])
                            ->get();
            $data = $SalesReturnItem;

            //Legend
            // 'value1' => $detail->id_retur,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = SalesReturnItemDetail::find($detail->id_detail);
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new SalesReturnItemDetail();
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('sales_return_item_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $log = ActionLog::create([
                'module' => 'Sales Return Item',
                'action' => 'Update',
                'desc' => 'Update Sales Return Item',
                'username' => Auth::user()->user_name
            ]);
        });
        if (is_null($exception)) {
            return redirect()->route('SalesReturnItem.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_retur).' Telah Diupdate!');
        }
        else {
            return redirect('/SalesReturnItem')->with('error', $exception);
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
            $salesReturn = SalesReturnItem::find($id);
            $data = $salesReturn;

            $cekRetur = SalesReturn::where([
                                            ['sales_return.id_retur', '=', $id],
                                            ['sales_return.status_retur', '!=', 'draft']
                                        ])
                                        ->count();

            if ($btnAction == "posting") {
                $detailRetur = SalesReturnItemDetail::leftJoin('product', 'sales_return_item_detail.id_item', '=', 'product.id')
                                                    ->leftJoin('product_unit', 'sales_return_item_detail.id_satuan', 'product_unit.id')
                                                    ->select(
                                                        'sales_return_item_detail.id',
                                                        'sales_return_item_detail.id_item',
                                                        'sales_return_item_detail.id_satuan',
                                                        'sales_return_item_detail.id_index',
                                                        'sales_return_item_detail.qty_item',
                                                        'product.kode_item',
                                                        'product.nama_item',
                                                        'product_unit.nama_satuan'
                                                    )
                                                    ->where([
                                                        ['sales_return_item_detail.id_retur', '=', $id]
                                                    ])
                                                    ->get();

                $transaksi = [];
                foreach ($detailRetur As $detail) {
                    $dataDetail = [
                        'kode_transaksi' => $salesReturn->kode_retur,
                        'id_item' => $detail->id_item,
                        'id_satuan' => $detail->id_satuan,
                        'id_index' => $detail->id_index,
                        'qty_item' => $detail->qty_item,
                        'tgl_transaksi' => $salesReturn->tanggal_retur,
                        'jenis_transaksi' => "retur_penjualan",
                        'transaksi' => "in",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transaksi, $dataDetail);
                }
                StockTransaction::insert($transaksi);

                $salesReturn->status_retur = "posted";
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'sales Return Item',
                    'action' => 'Posting',
                    'desc' => 'Posting sales Return Item',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($salesReturn->kode_retur).' Telah Diposting!';
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
                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $salesReturn->kode_retur)->delete();
                    $salesReturn->status_retur = "draft";
                    $salesReturn->flag_revisi = '1';
                    $salesReturn->updated_by = Auth::user()->user_name;
                    $salesReturn->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Return Item',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Sales Return Item',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Sales Return Item '.strtoupper($salesReturn->kode_retur).' Telah Direvisi!';
                    $status = 'success';
                }
                else {
                    $msg = 'Pembuatan Nota Retur '.strtoupper($salesReturn->kode_retur).' Tidak dapat Direvisi karena Retur: '.strtoupper($salesReturn->kode_retur).' Telah diproses !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "batal") {
                $salesReturn->status_retur = "batal";
                $salesReturn->updated_by = Auth::user()->user_name;
                $salesReturn->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $salesReturn->kode_retur)->delete();

                $log = ActionLog::create([
                    'module' => 'Sales Order',
                    'action' => 'Batal',
                    'desc' => 'Batal Sales Order',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Sales Order '.strtoupper($salesReturn->kode_retur).' Telah Dibatalkan!';
                $status = 'success';
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('SalesReturnItem.edit', [$id]);
            }
            elseif ($status == "nota") {
                Session::put('id_retur', $id);
                Session::put('id_cust', $data->id_customer);
                Session::save();

                return redirect('SalesReturn/Add');
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
            $delete = SalesReturnItem::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Sales Return Item',
                'action' => 'Delete',
                'desc' => 'Delete Sales Return Item',
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

    public function ResetSalesReturnItemDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idReturn');
            $mode = $request->input('mode');


            if ($id != "DRAFT") {
                // $detail = SalesReturnItemDetail::where([
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
                                    ['module', '=', 'sales_return_item'],
                                    ['value1', '=', $id]
                                ])->delete();
            }
            else {
                $delete = DB::table('sales_return_item_detail')->where('id_retur', '=', $id)->delete();
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
