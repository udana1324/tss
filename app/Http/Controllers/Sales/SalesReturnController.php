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
use App\Models\Product\ProductUnit;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\SalesReturn;
use App\Models\Sales\SalesReturnDetail;
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

class SalesReturnController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesReturn'],
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
                                                ['module.url', '=', '/SalesReturn'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = SalesReturn::distinct()->get('status_retur');
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('sales_return_detail')->where('deleted_at', '!=', null)->delete();
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

                return view('pages.sales.sales_return.index', $data);
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

        $salesOrder = SalesReturn::leftJoin('customer', 'sales_return.id_customer', '=', 'customer.id')
                            ->select(
                                'customer.nama_customer',
                                'sales_return.id',
                                'sales_return.kode_retur',
                                'sales_return.nota_retur',
                                'sales_return.jumlah_total_retur',
                                'sales_return.nominal_retur',
                                'sales_return.tanggal_retur',
                                'sales_return.flag_revisi',
                                'sales_return.status_retur')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('sales_return.tanggal_retur', Carbon::parse($periode)->format('m'));
                                $q->whereYear('sales_return.tanggal_retur', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('sales_return.id', 'desc')
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
                                            ['module.url', '=', '/SalesReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();

                $idRetur = Session::get('id_retur');
                $idCust = Session::get('id_cust');
                if ($idCust == "" && $idRetur == "") {
                    $mode = "tambah";
                }
                else {
                    $mode = "nota";
                }
                Session::forget('id_so');
                Session::forget('id_cust');

                $parentMenu = Module::find($hakAkses->parent);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['idRetur'] = $idRetur;
                $data['idCust'] = $idCust;
                $data['mode'] = $mode;

                $log = ActionLog::create([
                    'module' => 'Sales Retur',
                    'action' => 'Buat',
                    'desc' => 'Buat Sales Retur',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('sales_return_detail')
                            ->where([
                                ['id_retur', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.sales_return.add', $data);
            }
            else {
                return redirect('/SalesReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataRetur = SalesReturn::find($id);

                if ($dataRetur->status_retur != "draft") {
                    return redirect('/SalesReturn')->with('warning', 'Retur tidak dapat diubah karena status Retur bukan DRAFT!');
                }

                // $restore = SalesOrderDetail::onlyTrashed()->where([['id_so', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_return'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = SalesReturnDetail::where([
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
                            'module' => 'sales_return',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_retur,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->id_index,
                            'value5' => $detail->qty_item,
                            'value6' => $detail->harga_retur,
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
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Sales Return',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_return.edit', $data);
            }
            else {
                return redirect('/SalesReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesReturn'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataRetur = SalesReturn::leftJoin('sales_return_item', 'sales_return.id_retur', '=', 'sales_return_item.id')
                                        ->select(
                                            DB::raw('sales_return_item.kode_retur as kode_retur_item'),
                                            'sales_return.*'
                                        )
                                        ->where([
                                            ['sales_return.id', '=', $id]
                                        ])
                                        ->first();
                $dataCustomer = Customer::find($dataRetur->id_customer);

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataRetur->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataRetur'] = $dataRetur;

                $log = ActionLog::create([
                    'module' => 'Sales Return',
                    'action' => 'Detil',
                    'desc' => 'Detil Sales Return',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.sales_return.detail', $data);
            }
            else {
                return redirect('/SalesReturn')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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

    public function getCustomerReturn(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $dataProduct = SalesReturnItem::select('sales_return_item.id', 'sales_return_item.kode_retur')
                                        ->where([
                                            ['sales_return_item.id_customer', '=', $idCustomer]
                                        ])
                                        ->orderBy('sales_return_item.kode_retur', 'asc')
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

            $dataProduct = SalesReturnItemDetail::select('id_item', 'id_satuan', 'qty_item')
                                                ->where([
                                                    ['sales_return_item_detail.id_customer', '=', $idCustomer],
                                                    ['sales_return_item_detail.id_item', '=', $idProduct],
                                                    ['sales_return_item_detail.id_satuan', '=', $idSatuan],
                                                    ['sales_return_item_detail.id_index', '=', $idIndex],
                                                ])
                                                ->first();

            $dataProductRetur = $dataProduct->qty_item;
        }
        else {
            $dataProductRetur = "";
        }

        return response()->json($dataProductRetur);
    }

    public function GetSalesReturnDetail(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        $detailData = [];
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

            $detail = SalesReturnDetail::leftJoin('product', 'sales_return_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_return_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'sales_return_detail.id',
                                            'sales_return_detail.id_item',
                                            'sales_return_detail.id_satuan',
                                            'sales_return_detail.id_index',
                                            'sales_return_detail.qty_item',
                                            'sales_return_detail.harga_retur',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['sales_return_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_return_detail.created_by', $user);
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
                    'harga_retur' => $data->harga_retur,
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
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_return']
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
                    'harga_retur' => $data->value6,
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

    public function DeleteSalesReturnDetail(Request $request)
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
                $delete = DB::table('sales_return_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetSalesReturnFooter(Request $request)
    {
        $id = $request->input('idReturn');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {
            $detail = SalesReturnDetail::leftJoin('product', 'sales_return_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'sales_return_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            DB::raw('SUM(sales_return_detail.qty_item) AS qtyItem'),
                                            DB::raw('SUM(COALESCE(sales_return_detail.harga_retur,0) * COALESCE(sales_return_detail.qty_item)) AS subtotal'),
                                        )
                                        ->where([
                                            ['sales_return_detail.id_retur', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_return_detail.created_by', $user);
                                        })
                                        ->groupBy('sales_return_detail.id_retur')
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
                                            DB::raw('SUM(COALESCE(temp_transaction.value5,0) * COALESCE(temp_transaction.value6)) AS subtotal'),
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_return']
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
            return redirect('/SalesReturn')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idRetur = $request->input('data_retur');
            $tglRetur = $request->input('tanggal_retur');
            $notaRetur = $request->input('nota_retur');
            $qtyItem = $request->input('qtyTtl');
            $total = $request->input('gt');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));
            $tahunPeriode = date("y", strtotime($tglRetur));

            $countKode = DB::table('sales_return')
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
                $kodeRetur = "rs-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeRetur = "rs-cv-".$kodeTgl.$counter;
            }

            $SalesReturn = new SalesReturn();
            $SalesReturn->kode_retur = $kodeRetur;
            $SalesReturn->nota_retur = $notaRetur;
            $SalesReturn->id_customer = $idCustomer;
            $SalesReturn->id_retur = $idRetur;
            $SalesReturn->jumlah_total_retur = $qtyItem;
            $SalesReturn->nominal_retur = $total;
            $SalesReturn->tanggal_retur = $tglRetur;
            $SalesReturn->keterangan = $keterangan;
            $SalesReturn->status_retur = 'draft';
            $SalesReturn->flag_revisi = 0;
            $SalesReturn->id_ppn = $taxSettings->ppn_percentage_id;
            $SalesReturn->created_by = $user;
            $SalesReturn->save();

            $data = $SalesReturn;

            $setDetail = DB::table('sales_return_detail')
                            ->where([
                                        ['id_retur', '=', 'DRAFT'],
                                        ['created_by', '=', Auth::user()->user_name]
                                    ])
                            ->update([
                                'id_retur' => $SalesReturn->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Sales Return',
                'action' => 'Simpan',
                'desc' => 'Simpan Sales Return',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('SalesReturn.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_so).' Telah Disimpan!');
        }
        else {
            return redirect('/SalesReturn')->with('error', $exception);
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
            return redirect('/SalesReturn')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {

            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idRetur = $request->input('data_retur');
            $tglRetur = $request->input('tanggal_retur');
            $notaRetur = $request->input('nota_retur');
            $qtyItem = $request->input('qtyTtl');
            $total = $request->input('gt');
            $user = Auth::user()->user_name;

            $keterangan = $request->input('keterangan');
            $qtyItem = str_replace(",", ".", $qtyItem);

            $blnPeriode = date("m", strtotime($tglRetur));
            $thnPeriode = date("Y", strtotime($tglRetur));

            $updateFile = $request->input('file_po_customer');



            $SalesReturn = SalesReturn::find($id);
            $SalesReturn->nota_retur = $notaRetur;
            $SalesReturn->id_customer = $idCustomer;
            $SalesReturn->id_retur = $idRetur;
            $SalesReturn->jumlah_total_retur = $qtyItem;
            $SalesReturn->nominal_retur = $qtyItem;
            $SalesReturn->tanggal_retur = $tglRetur;
            $SalesReturn->keterangan = $keterangan;
            $SalesReturn->id_ppn = $taxSettings->ppn_percentage_id;
            $SalesReturn->updated_by = $user;
            $SalesReturn->save();

            // $deletedDetail = SalesOrderDetail::onlyTrashed()->where([['id_so', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                ['module', '=', 'sales_return'],
                                ['value1', '=', $id],
                                ['action', '!=' , null]
                            ])
                            ->get();
            $data = $SalesReturn;

            //Legend
            // 'value1' => $detail->id_retur,
            // 'value2' => $detail->id_item,
            // 'value3' => $detail->id_satuan,
            // 'value4' => $detail->qty_item,

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = SalesReturnDetail::find($detail->id_detail);
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->harga_retur = $detail->value6;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new SalesReturnDetail();
                        $listItem->id_retur = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->id_index = $detail->value4;
                        $listItem->qty_item = $detail->value5;
                        $listItem->harga_retur = $detail->value6;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('sales_return_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $log = ActionLog::create([
                'module' => 'Sales Return',
                'action' => 'Update',
                'desc' => 'Update Sales Return',
                'username' => Auth::user()->user_name
            ]);
        });
        if (is_null($exception)) {
            return redirect()->route('SalesReturn.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_retur).' Telah Diupdate!');
        }
        else {
            return redirect('/SalesReturn')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $salesReturn = SalesReturn::find($id);
            $data = $salesReturn;

            if ($btnAction == "posting") {

                $returItem = SalesReturnItem::find($salesReturn->id_retur);
                $returItem->flag_nota = 1;
                $returItem->save();

                $salesReturn->status_retur = "posted";
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'Sales Return',
                    'action' => 'Posting',
                    'desc' => 'Posting Sales Return',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($salesReturn->kode_retur).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "kirim") {
                $status = "kirim";
            }
            elseif ($btnAction == "revisi") {
                $returItem = SalesReturnItem::find($salesReturn->id_retur);
                $returItem->flag_nota = 0;
                $returItem->save();

                $salesReturn->status_retur = "draft";
                $salesReturn->flag_revisi = '1';
                $salesReturn->updated_by = Auth::user()->user_name;
                $salesReturn->save();

                $log = ActionLog::create([
                    'module' => 'Sales Return',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Sales Return',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Sales Return '.strtoupper($salesReturn->kode_retur).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $salesReturn->status_retur = "batal";
                $salesReturn->updated_by = Auth::user()->user_name;
                $salesReturn->save();

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
                return redirect()->route('SalesReturn.edit', [$id]);
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
            $delete = SalesReturn::find($id);
            $delete->deleted_by = $user;
            $delete->save();
            $delete->delete();

            $log = ActionLog::create([
                'module' => 'Sales Return',
                'action' => 'Delete',
                'desc' => 'Delete Sales Return',
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

    public function ResetSalesReturnDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idReturn');
            $mode = $request->input('mode');


            if ($id != "DRAFT") {
                // $detail = SalesReturnDetail::where([
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
                                    ['module', '=', 'sales_return'],
                                    ['value1', '=', $id]
                                ])->delete();
            }
            else {
                $delete = DB::table('sales_return_detail')->where('id_retur', '=', $id)->delete();
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

    public function SetSalesReturnDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idSalesReturn');
            $idRetur = $request->input('idReturn');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != "DRAFT") {
                $update = DB::table('temp_transaction')
                            ->where([
                                ['value1', '=', $id],
                                ['module', '=', "sales_return"]
                            ])
                            ->update([
                                'action' => "hapus",
                                'deleted_by' => Auth::user()->user_name,
                                'deleted_at' => now()
                            ]);


                $detail = SalesReturnItemDetail::leftJoin('sales_return_item', 'sales_return_item_detail.id_retur', '=', 'sales_return_item.id')
                                                ->select(
                                                    'sales_return_item_detail.id_item',
                                                    'sales_return_item_detail.id_satuan',
                                                    'sales_return_item_detail.id_index',
                                                    'sales_return_item_detail.qty_item',
                                                    'sales_return_item.id_customer'
                                                )
                                                ->where([
                                                    ['sales_return_item_detail.id_retur', '=', $idRetur]
                                                ])
                                                ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailRetur) {

                    $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                        ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
                                                        ->whereIn('sales_order.tanggal_so', function($querySub) use ($detailRetur) {
                                                            $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
                                                                    ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                                    ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                                    ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
                                                                    ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                                    ->where([
                                                                        ['sales_order.id_customer', '=', $detailRetur->id_customer],
                                                                        ['sales_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                                        ['sales_order_detail.id_item', '=', $detailRetur->id_item]
                                                                    ]);
                                                        })
                                                        ->where([
                                                            ['sales_order.id_customer', '=', $detailRetur->id_customer],
                                                            ['sales_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                            ['sales_order_detail.id_item', '=', $detailRetur->id_item]
                                                        ])
                                                        ->first();

                    $dataDetail = [
                        'module' => "sales_return",
                        'value1' => $id,
                        'value2' => $detailRetur->id_item,
                        'value3' => $detailRetur->id_satuan,
                        'value4' => $detailRetur->id_index,
                        'value5' => $detailRetur->qty_item,
                        'value6' => $hargaJualTerakhir->harga_jual_last,
                        'action' => "tambah",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                TempTransaction::insert($listDetail);
            }
            else {
                $delete = DB::table('sales_return_detail')
                            ->where('id_retur', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('sales_return_detail.created_by', $user);
                            })
                            ->delete();

                $detail = SalesReturnItemDetail::leftJoin('sales_return_item', 'sales_return_item_detail.id_retur', '=', 'sales_return_item.id')
                                                ->select(
                                                    'sales_return_item_detail.id_item',
                                                    'sales_return_item_detail.id_satuan',
                                                    'sales_return_item_detail.id_index',
                                                    'sales_return_item_detail.qty_item',
                                                    'sales_return_item.id_customer'
                                                )
                                                ->where([
                                                    ['sales_return_item_detail.id_retur', '=', $idRetur]
                                                ])
                                                ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailRetur) {

                    $hargaJualTerakhir = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                        ->select('id_item', 'id_satuan', DB::raw("harga_jual AS harga_jual_last"))
                                                        ->whereIn('sales_order.tanggal_so', function($querySub) use ($detailRetur) {
                                                            $querySub->select(DB::raw("MAX(sales_order.tanggal_so)"))->from("sales_order")
                                                                    ->leftJoin('sales_order_detail', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                                                    ->leftJoin('sales_invoice', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                                    ->whereNotIn('sales_order.status_so', ['draft', 'cancel'])
                                                                    ->whereNotIn('sales_invoice.status_invoice', ['draft', 'cancel'])
                                                                    ->where([
                                                                        ['sales_order.id_customer', '=', $detailRetur->id_customer],
                                                                        ['sales_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                                        ['sales_order_detail.id_item', '=', $detailRetur->id_item]
                                                                    ]);
                                                        })
                                                        ->where([
                                                            ['sales_order.id_customer', '=', $detailRetur->id_customer],
                                                            ['sales_order_detail.id_satuan', '=', $detailRetur->id_satuan],
                                                            ['sales_order_detail.id_item', '=', $detailRetur->id_item]
                                                        ])
                                                        ->first();

                    $dataDetail = [
                        'id_retur' => $id,
                        'id_item' => $detailRetur->id_item,
                        'id_satuan' => $detailRetur->id_satuan,
                        'id_index' => $detailRetur->id_index,
                        'qty_item' => $detailRetur->qty_item,
                        'harga_retur' => $hargaJualTerakhir->harga_jual_last,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                SalesReturnDetail::insert($listDetail);
            }


        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }
}
