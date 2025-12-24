<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperAccounting;
use App\Classes\BusinessManagement\HelperSalesInvoice;
use App\Classes\BusinessManagement\HelperSalesTaxInvoice;
use App\Exports\SalesInvoiceExport;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\CompanyAccount;
use App\Models\Library\Sales;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Sales\ExpeditionCost;
use App\Models\Sales\ExpeditionCostDetail;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use App\Models\Sales\SalesInvoiceTerms;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Sales\SalesOrderTerms;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesInvoice'],
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
                                                ['module.url', '=', '/SalesInvoice'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = SalesInvoice::distinct()->get('status_invoice');
                $dataCustomer = Customer::distinct()->get();
                $delete = DB::table('sales_invoice_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice.index', $data);
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

        // $listSJ = Delivery::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
        //                     ->select('sales_invoice_detail.id_invoice',
        //                         DB::raw("GROUP_CONCAT(delivery.kode_pengiriman SEPARATOR ',') as list_sj")
        //                     )
        //                     ->groupBy('sales_invoice_detail.id_invoice');

        $salesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')

                            // ->leftJoinSub($listSJ, 'listSJ', function($listSJ) {
                            //     $listSJ->on('sales_invoice.id', '=', 'listSJ.id_invoice');
                            // })
                            ->select(
                                'customer.nama_customer',
                                DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                // 'listSJ.list_sj',
                                'sales_order.no_so',
                                'sales_order.id_customer',
                                'sales_order.metode_pembayaran',
                                'sales_order.no_po_customer',
                                'sales_order.nominal_dp',
                                'sales_invoice.id',
                                'sales_invoice.kode_invoice',
                                'sales_invoice.dpp',
                                'sales_invoice.ppn',
                                'sales_invoice.grand_total',
                                'sales_invoice.ttl_qty',
                                'sales_invoice.tanggal_invoice',
                                'sales_invoice.tanggal_jt',
                                'sales_invoice.durasi_jt',
                                'sales_invoice.flag_revisi',
                                'sales_invoice.flag_tf',
                                'sales_invoice.flag_pembayaran',
                                DB::raw("CASE WHEN sales_invoice.flag_revisi = 1 THEN 1
                                              ELSE sales_invoice.flag_pembayaran = 0
                                        END AS flag_pembayaran_filter"),
                                DB::raw("CASE WHEN sales_invoice.flag_pembayaran = 1 THEN 1
                                              WHEN sales_invoice.flag_pembayaran = 2 THEN 2
                                              WHEN sales_invoice.flag_pembayaran = 0 THEN 3
                                        END AS flag_pembayaran_filter"),
                                'sales_invoice.status_invoice')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($periode)->format('m'));
                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('sales_invoice.id', 'desc')
                            ->get();
        return response()->json($salesInvoice);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::distinct()
                                        ->join('sales_order', 'sales_order.id_customer', 'customer.id')
                                        ->join('delivery', 'delivery.id_so', 'sales_order.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->where([
                                            ['delivery.status_pengiriman', '=', 'posted'],
                                            ['delivery.flag_invoiced', '=', '0']
                                        ])
                                        ->get();

                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $dataPreference = Preference::select(
                                                'preference.*'
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataAccount'] = $CompanyAccount;
                $data['dataPreference'] = $dataPreference;

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Buat',
                    'desc' => 'Buat Sales Invoice',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('sales_invoice_detail')
                            ->where([
                                ['id_invoice', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.invoice.add', $data);
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::distinct()
                                        ->join('sales_order', 'sales_order.id_customer', 'customer.id')
                                        ->join('delivery', 'delivery.id_so', 'sales_order.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->where([
                                            ['delivery.status_pengiriman', '=', 'posted'],
                                            ['delivery.flag_invoiced', '=', '0']
                                        ])
                                        ->get();

                $dataInv = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'sales_invoice.id',
                                        'sales_invoice.kode_invoice',
                                        'sales_invoice.id_so',
                                        'sales_order.id_alamat',
                                        'sales_invoice.tanggal_invoice',
                                        'sales_invoice.dp',
                                        'sales_invoice.durasi_jt',
                                        'sales_invoice.tanggal_jt',
                                        'sales_invoice.flag_terms_so',
                                        'sales_invoice.flag_ppn',
                                        'sales_invoice.flag_revisi',
                                        'sales_invoice.status_invoice',
                                        'sales_invoice.id_rekening',
                                        'sales_order.id_customer',
                                        'sales_order.id_alamat',
                                        'sales_order.jenis_diskon',
                                        DB::raw("CASE WHEN sales_order.jenis_diskon = 'P' THEN sales_order.persentase_diskon ELSE sales_order.nominal_diskon END AS value_diskon"),
                                        'sales_order.metode_pembayaran',
                                        'sales_order.sisa_dp',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['sales_invoice.id', '=', $id],
                                    ])
                                    ->first();

                $dataAlamat = CustomerDetail::where([
                                    ['id_customer', '=', $dataInv->id_customer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

                if ($dataAlamat == null) {
                    $dataAlamat = CustomerDetail::find($dataInv->id_alamat);
                }

                if ($dataInv->status_invoice != "draft") {
                    return redirect('/SalesInvoice')->with('warning', 'Invoice Penjualan tidak dapat diubah karena status Invoice bukan DRAFT!');
                }

                // $restore = SalesInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_invoice'],
                                    ['value1', '=', $id]
                                ])->delete();

                $dataDetail = SalesInvoiceDetail::where([
                                                    ['id_invoice', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'sales_invoice',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_invoice,
                            'value2' => $detail->id_sj,
                            'value3' => $detail->qty_sj,
                            'value4' => $detail->subtotal_sj
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();

                if ($dataInv->flag_terms_so == 0) {
                    $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataInv->id_so)->get();
                }

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataInv'] = $dataInv;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataTerms'] = $dataTerms;
                $data['dataAccount'] = $CompanyAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice.edit', $data);
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataInv = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('company_account', 'company_account.id', '=', 'sales_invoice.id_rekening')
                                    ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                    ->leftJoin('sales_invoice_collection_detail', 'sales_invoice_collection_detail.id_invoice', '=', 'sales_invoice.id')
                                    ->leftJoin('sales_invoice_collection', 'sales_invoice_collection_detail.id_tf', '=', 'sales_invoice_collection.id')
                                    ->select(
                                        'sales_invoice.id',
                                        'sales_invoice.kode_invoice',
                                        'sales_invoice.id_so',
                                        'sales_invoice.tanggal_invoice',
                                        'sales_invoice.durasi_jt',
                                        'sales_invoice.tanggal_jt',
                                        'sales_invoice.flag_terms_so',
                                        'sales_invoice.flag_ppn',
                                        'sales_invoice.status_invoice',
                                        'sales_invoice.flag_revisi',
                                        'sales_invoice.dp',
                                        'sales_invoice.id_ppn',
                                        'sales_invoice.flag_tf',
                                        'sales_invoice.flag_pembayaran',
                                        'sales_order.id_alamat',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.sisa_dp',
                                        'sales_order.jenis_diskon',
                                        'sales_order.nominal_diskon',
                                        'sales_order.persentase_diskon',
                                        'sales_order.metode_pembayaran',
                                        'customer_detail.alamat_customer',
                                        'customer.nama_customer',
                                        'company_account.atas_nama',
                                        'company_account.nomor_rekening',
                                        'bank.kode_bank',
                                        'bank.nama_bank',
                                        'sales_invoice_collection.kode_tf'
                                    )
                                    ->where([
                                        ['sales_invoice.id', '=', $id],
                                    ])
                                    ->first();

                $dataAlamat = CustomerDetail::where([
                                    ['id_customer', '=', $dataInv->id_customer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

                if ($dataAlamat == null) {
                    $dataAlamat = CustomerDetail::find($dataInv->id_alamat);
                }

                if ($dataInv->flag_terms_so == 0) {
                    $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataInv->id_so)->get();
                }

                $dataPreference = Preference::select(
                                                'preference.nama_pt',
                                                DB::raw("CONCAT(alamat_pt, ', ', COALESCE(kelurahan_pt, '-'), ', ', kecamatan_pt, ', ', kota_pt) AS alamat")
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();

                $taxSettings = TaxSettingsPPN::find($dataInv->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataInv'] = $dataInv;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Detail',
                    'desc' => 'Detail Sales Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice.detail', $data);
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                                ->select(
                                                    'customer.kode_customer',
                                                    'customer.nama_customer',
                                                    'customer.npwp_customer',
                                                    'customer.telp_customer',
                                                    'customer.fax_customer',
                                                    'customer.email_customer',
                                                    'customer.kategori_customer',
                                                    'customer.sales',
                                                    'sales_order.no_so',
                                                    'sales_order.id_customer',
                                                    'sales_order.no_po_customer',
                                                    'sales_order.id_alamat',
                                                    'sales_order.id_customer',
                                                    'sales_order.metode_pembayaran',
                                                    'sales_order.metode_kirim',
                                                    'sales_order.jenis_diskon',
                                                    'sales_order.nominal_diskon',
                                                    'sales_order.persentase_diskon',
                                                    'sales_order.nominal_dp',
                                                    DB::raw("sales_order.persentase_diskon/100 *  sales_invoice.dpp AS diskon"),
                                                    'sales_order.durasi_jt',
                                                    'sales_invoice.*'
                                                )
                                                ->where([
                                                    ['sales_invoice.id', '=', $id],
                                                ])
                                                ->first();
                if ($dataSalesInvoice->flag_terms_so == 0) {
                    $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataSalesInvoice->id_so)->get();
                }
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
                                            ->where('flag_inv_sale', 'Y')
                                            ->first();

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->where([
                                                    ['company_account.id', '=', $dataSalesInvoice->id_rekening]
                                                ])
                                                ->first();

                $idSo = $dataSalesInvoice->id_so;
                $detailSalesInvoice = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('sales_order_detail',function($qJoin) use ($idSo) {
                                                            $qJoin->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item')
                                                            ->where('sales_order_detail.id_so', $idSo);
                                                        })
                                                        ->leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                        ->leftJoin('expedition_cost_detail', 'sales_invoice_detail.id_sj', 'expedition_cost_detail.id_sj')
                                                        ->select(
                                                            'delivery_detail.id',
                                                            'delivery_detail.id_item',
                                                            'delivery_detail.qty_item',
                                                            'sales_order_detail.harga_jual',
                                                            DB::raw('COALESCE(sales_order_detail.harga_jual,0) * COALESCE(delivery_detail.qty_item) AS subtotal'),
                                                            'product.kode_item',
                                                            'product.nama_item',
                                                            'product.jenis_item',
                                                            'product_unit.nama_satuan'
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $id]
                                                        ])
                                                        ->get();
                $dataBiayaEkspedisi = ExpeditionCostDetail::select(
                                                            DB::raw('COALESCE(SUM(expedition_cost_detail.subtotal), 0) AS BiayaEkspedisi')
                                                        )
                                                        ->where([
                                                            ['expedition_cost_detail.flag_tagih', '=', "Y"]
                                                        ])
                                                        ->whereIn('expedition_cost_detail.id_SJ', function($subQuery) use ($id) {
                                                            $subQuery->select('id_sj')->from('sales_invoice_detail')
                                                            ->where('id_invoice', $id);
                                                        })
                                                        ->first();

                $shipDate = Delivery::select(
                                DB::raw('MAX(delivery.tanggal_sj) AS lastDate'), 'delivery.kode_pengiriman'
                            )
                            ->whereIn('delivery.id', function($subQuery) use ($id) {
                                $subQuery->select('id_sj')->from('sales_invoice_detail')
                                ->where('id_invoice', $id);
                            })
                            ->first();

                $dataSales = Sales::find($dataSalesInvoice->sales);
                $dataAlamat = CustomerDetail::find($dataSalesInvoice->id_alamat);
                $dataAlamatPenagihan = CustomerDetail::where([
                    ['id_customer', '=', $dataSalesInvoice->id_customer],
                    ['jenis_alamat', '=', 'NPWP']
                ])
                ->first();
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataSalesInvoice'] = $dataSalesInvoice;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataAlamatPenagihan'] = $dataAlamatPenagihan;
                $data['detailSalesInvoice'] = $detailSalesInvoice;
                $data['dataSales'] = $dataSales;
                $data['shipDate'] = $shipDate;
                $data['CompanyAccount'] = $CompanyAccount;
                $data['dataBiayaEkspedisi'] = $dataBiayaEkspedisi;

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Sales Invoice',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesInvoice::cetakPdfInv($data);

                $fpdf->Output('I', strtoupper($dataSalesInvoice->kode_invoice).".pdf");
                exit;
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getSalesOrder(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $dataSo = SalesOrder::distinct()
                                ->join('delivery', 'delivery.id_so', '=', 'sales_order.id')
                                ->select(
                                    'sales_order.id',
                                    'sales_order.no_so',
                                )
                                ->where([
                                    ['sales_order.id_customer', '=', $idCustomer],
                                    ['delivery.status_pengiriman', '=', 'posted'],
                                    //['delivery.flag_terkirim', '=', '1'],
                                    ['delivery.flag_invoiced', '=', '0']
                                ])
                                ->orderBy('sales_order.id', 'asc')
                                ->get();

        return response()->json($dataSo);
    }

    public function getSalesOrderData(Request $request)
    {
        $idSo = $request->input('idSalesOrder');

        $dataSo = SalesOrder::where('id', $idSo)->get();

        return response()->json($dataSo);
    }

    public function getDelivery(Request $request)
    {
        $idSalesOrder = $request->input('idSalesOrder');

        $datadelivery = Delivery::where([
                                    ['id_so', '=', $idSalesOrder],
                                    ['flag_invoiced', '=', '0']
                                ])
                                ->orderBy('id', 'asc')
                                ->get();

        return response()->json($datadelivery);
    }

    public function getDefaultAddress(Request $request)
    {
        $idSalesOrder = $request->input('idSalesOrder');
        $idAlamat = SalesOrder::find($idSalesOrder);

        $defaultAddress = CustomerDetail::where([
                                    ['id_customer', '=', $idAlamat->id_customer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->get();

        if (count($defaultAddress) == 0) {
            $defaultAddress = CustomerDetail::where([
                                    ['id', '=', $idAlamat->id_alamat]
                                ])
                                ->get();
        }

        return response()->json($defaultAddress);
    }

    public function getDataDelivery(Request $request)
    {
        $idDelivery = $request->input('idDelivery');
        $dlv = Delivery::find($idDelivery);
        $idSo = $dlv->id_so;

        $detailDlv = DeliveryDetail::leftJoin('sales_order_detail', 'sales_order_detail.id_item', 'delivery_detail.id_item')
                                    ->select(
                                        'delivery_detail.id_pengiriman',
                                        DB::raw('SUM(delivery_detail.qty_item * sales_order_detail.harga_jual) AS subtotalDlv')
                                    )
                                    ->whereIn('delivery_detail.id_pengiriman', function($subQuery) use ($idSo) {
                                        $subQuery->select('id')->from('delivery')
                                        ->where('id_so', $idSo);
                                    })
                                    ->where([
                                        ['sales_order_detail.id_so', '=', $idSo],
                                    ])
                                    ->groupBy('delivery_detail.id_pengiriman');

        $dataDlv = Delivery::leftJoinSub($detailDlv, 'detailDlv', function($detailDlv) {
                                $detailDlv->on('delivery.id', '=', 'detailDlv.id_pengiriman');
                            })
                            ->select(
                                'delivery.id',
                                'delivery.kode_pengiriman',
                                'delivery.tanggal_sj',
                                'delivery.tanggal_kirim',
                                'delivery.jumlah_total_sj',
                                'detailDlv.subtotalDlv',
                            )
                            ->where([
                                ['delivery.id', '=', $idDelivery],
                            ])
                            ->get();


        return response()->json($dataDlv);
    }

    public function StoreInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idInvoice');
            $idDlv = $request->input('idDlv');
            $qty = $request->input('qtyDlv');
            $subtotal = $request->input('subtotalDlv');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);
            $subtotal = str_replace(",", ".", $subtotal);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = SalesInvoiceDetail::select(DB::raw("COUNT(*) AS angka"))
                                                ->where([
                                                    ['id_invoice', '=' , $id],
                                                    ['id_sj', '=', $idDlv]
                                                ])
                                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new SalesInvoiceDetail();
                    $listItem->id_invoice = $id;
                    $listItem->id_sj = $idDlv;
                    $listItem->qty_sj = $qty;
                    $listItem->subtotal_sj = $subtotal;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Invoice Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Invoice Detail',
                        'username' => Auth::user()->user_name
                    ]);


                    $data = "success";
                }
            }
            else {
                //Legend
                // 'value1' => $detail->id_invoice,
                // 'value2' => $detail->id_sj,
                // 'value3' => $detail->qty_sj,
                // 'value4' => $detail->subtotal_sj,

                $countItem = TempTransaction::select(DB::raw("COUNT(*) AS angka"))
                                            ->where([
                                                ['module', '=', 'sales_invoice'],
                                                ['value1', '=' , $id],
                                                ['value2', '=', $idDlv]
                                            ])
                                            ->first();

                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'sales_invoice';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idDlv;
                    $listItem->value3 = $qty;
                    $listItem->value4 = $subtotal;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Sales Invoice Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Sales Invoice Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function SetInvoiceDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idInvoice');
            $idSo = $request->input('idSalesOrder');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != 'DRAFT') {
                $update = DB::table('temp_transaction')
                            ->where([
                                ['value1', '=', $id],
                                ['module', '=', "sales_invoice"]
                            ])
                            ->update([
                                'action' => "hapus",
                                'deleted_by' => Auth::user()->user_name,
                                'deleted_at' => now()
                            ]);

                $detailDlv = DeliveryDetail::leftJoin('sales_order_detail', 'sales_order_detail.id_item', 'delivery_detail.id_item')
                                            ->select(
                                                'delivery_detail.id_pengiriman',
                                                DB::raw('SUM(delivery_detail.qty_item * sales_order_detail.harga_jual) AS subtotalDlv')
                                            )
                                            ->whereIn('delivery_detail.id_pengiriman', function($subQuery) use ($idSo) {
                                                $subQuery->select('id')->from('delivery')
                                                ->where('id_so', $idSo);
                                            })
                                            ->where([
                                                ['sales_order_detail.id_so', '=', $idSo],
                                            ])
                                            ->groupBy('delivery_detail.id_pengiriman');

                $dataDlv = Delivery::leftJoinSub($detailDlv, 'detailDlv', function($detailDlv) {
                                        $detailDlv->on('delivery.id', '=', 'detailDlv.id_pengiriman');
                                    })
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.tanggal_sj',
                                        'delivery.tanggal_kirim',
                                        'delivery.jumlah_total_sj',
                                        'detailDlv.subtotalDlv',
                                    )
                                    ->where([
                                        ['delivery.id_so', '=', $idSo],
                                        ['delivery.status_pengiriman', '=', 'posted'],
                                        ['delivery.flag_invoiced', '=', '0'],
                                        //['delivery.flag_terkirim', '=', '1'],
                                    ])
                                    ->get();

                $data = $dataDlv;

                $listDetail = [];
                foreach ($dataDlv As $detail) {

                    $dataDetail = [
                        'module' => "sales_invoice",
                        'value1' => $id,
                        'value2' => $detail->id,
                        'value3' => $detail->jumlah_total_sj,
                        'value4' => $detail->subtotalDlv,
                        'action' => "tambah",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                TempTransaction::insert($listDetail);
            }
            else {
                $delete = DB::table('sales_invoice_detail')
                            ->where('id_invoice', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('sales_invoice_detail.created_by', $user);
                            })
                            ->delete();

                $detailDlv = DeliveryDetail::leftJoin('sales_order_detail', 'sales_order_detail.id_item', 'delivery_detail.id_item')
                                            ->select(
                                                'delivery_detail.id_pengiriman',
                                                DB::raw('SUM(delivery_detail.qty_item * sales_order_detail.harga_jual) AS subtotalDlv')
                                            )
                                            ->whereIn('delivery_detail.id_pengiriman', function($subQuery) use ($idSo) {
                                                $subQuery->select('id')->from('delivery')
                                                ->where('id_so', $idSo);
                                            })
                                            ->where([
                                                ['sales_order_detail.id_so', '=', $idSo],
                                            ])
                                            ->groupBy('delivery_detail.id_pengiriman');

                $dataDlv = Delivery::leftJoinSub($detailDlv, 'detailDlv', function($detailDlv) {
                                        $detailDlv->on('delivery.id', '=', 'detailDlv.id_pengiriman');
                                    })
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.tanggal_sj',
                                        'delivery.tanggal_kirim',
                                        'delivery.jumlah_total_sj',
                                        'detailDlv.subtotalDlv',
                                    )
                                    ->where([
                                        ['delivery.id_so', '=', $idSo],
                                        ['delivery.status_pengiriman', '=', 'posted'],
                                        ['delivery.flag_invoiced', '=', '0'],
                                        //['delivery.flag_terkirim', '=', '1'],
                                    ])
                                    ->get();

                $data = $dataDlv;

                $listDetail = [];
                foreach ($dataDlv As $detail) {

                    $dataDetail = [
                        'id_invoice' => $id,
                        'id_sj' => $detail->id,
                        'qty_sj' => $detail->jumlah_total_sj,
                        'subtotal_sj' => $detail->subtotalDlv,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                SalesInvoiceDetail::insert($listDetail);
            }

        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceDetail(Request $request)
    {
        $id = $request->input('idInvoice');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = SalesInvoiceDetail::leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                        ->select(
                                            'sales_invoice_detail.id',
                                            'sales_invoice_detail.id_sj',
                                            'sales_invoice_detail.qty_sj',
                                            'sales_invoice_detail.subtotal_sj',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_sj',
                                            'delivery.tanggal_kirim'
                                        )
                                        ->where([
                                            ['sales_invoice_detail.id_invoice', '=', $id],
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_invoice_detail.created_by', $user);
                                        })
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('delivery', 'temp_transaction.value2', '=', 'delivery.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_sj',
                                            'delivery.tanggal_kirim'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_invoice']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function GetDeliveryDetail(Request $request)
    {
        $idSj = $request->input('idDelivery');
        $idSo = $request->input('idSo');

        $dataDetail = SalesOrderDetail::leftJoin('sales_order', 'sales_order_detail.id_so', '=', 'sales_order.id')
                                        ->select('sales_order_detail.id_item', 'sales_order_detail.id_satuan', 'sales_order_detail.harga_jual')
                                        ->where([
                                                ['sales_order.id', '=', $idSo]
                                        ]);

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $detail = DeliveryDetail::leftJoin('delivery', 'delivery_detail.id_pengiriman', '=', 'delivery.id')
                                ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                ->leftJoinSub($dataDetail, 'dataDetail', function($dataDetail) {
                                    $dataDetail->on('delivery_detail.id_item', '=', 'dataDetail.id_item');
                                    $dataDetail->on('delivery_detail.id_satuan', '=', 'dataDetail.id_satuan');
                                })
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'product.kode_item',
                                    'product.nama_item',
                                    'product_unit.nama_satuan',
                                    'dataDetail.harga_jual',
                                    'delivery.id',
                                    'delivery_detail.qty_item',
                                    'delivery.kode_pengiriman',
                                    DB::raw("delivery_detail.qty_item * dataDetail.harga_jual AS subtotal_sj"),
                                    'dataSpek.value_spesifikasi'
                                )
                                ->where([
                                    ['delivery.id', '=', $idSj],
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function DeleteInvoiceDetail(Request $request)
    {
        $data = "";
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
                $delete = DB::table('sales_invoice_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceFooter(Request $request)
    {
        $id = $request->input('idInvoice');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {

            $detail = SalesInvoiceDetail::select(
                                                DB::raw('COALESCE(SUM(sales_invoice_detail.qty_sj),0) AS qtyInv'),
                                                DB::raw('COALESCE(SUM(sales_invoice_detail.subtotal_sj),0) AS subtotalInv'),
                                            )
                                            ->where([
                                                ['sales_invoice_detail.id_invoice', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('sales_invoice_detail.created_by', $user);
                                            })
                                            ->groupBy('sales_invoice_detail.id_invoice')
                                            ->first();
        }
        else {
            $detail = TempTransaction::select(
                                            DB::raw('COALESCE(SUM(temp_transaction.value3),0) AS qtyInv'),
                                            DB::raw('COALESCE(SUM(temp_transaction.value4),0) AS subtotalInv'),
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'sales_invoice']
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

    public function GetDate(Request $request)
    {
        $id = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $detail = Delivery::select(
                                DB::raw('MAX(delivery.tanggal_sj) AS lastDate'),
                            )
                            ->whereIn('delivery.id', function($subQuery) use ($id, $user) {
                                $subQuery->select('id_sj')->from('sales_invoice_detail')
                                ->where('id_invoice', $id)
                                ->when($id == "DRAFT", function($q) use ($user) {
                                    $q->where('sales_invoice_detail.created_by', $user);
                                });
                            })
                            ->first();

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
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

    public function RestoreInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idInv');
            $restore = SalesInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
            $restore->restore();

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getTermsByOpt(Request $request)
    {
        $id = $request->input('idInvoice');
        $idSo = $request->input('idSalesOrder');
        $flag = $request->input('flagTerms');

        if ($flag == 0) {
            $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();
        }
        else {
            $dataTerms = SalesOrderTerms::where('id_so', $idSo)->get();
        }

        return response()->json($dataTerms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer'=>'required',
            'SalesOrder'=>'required',
            'tanggal_inv'=>'required',
        ]);

        $tglInv = $request->input('tanggal_inv');
        $flagPpn = $request->input('stat_ppn');

        $bulanIndonesia = Carbon::parse($tglInv)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglInv);
        if (!$aksesTransaksi) {
            return redirect('/SalesInvoice')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglInv);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/SalesInvoice')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $idSalesOrder = $request->input('SalesOrder');
            $tglInv = $request->input('tanggal_inv');
            $tenor = $request->input('durasiJT');
            $tglJt = $request->input('tgl_jt');
            $qty = $request->input('qtyTtl');
            $dp = $request->input('dp');
            $dpp = $request->input('dpp');
            $ppn = $request->input('ppn');
            $gt = $request->input('gt');
            $flagPPn = $request->input('stat_ppn');
            $flagTerms = $request->input('terms_usage');
            $rekPerusahaan = $request->input('company_account');
            $user = Auth::user()->user_name;

            if ($flagTerms == "use") {
                $flagTermsUsage = 1;
            }
            else {
                $flagTermsUsage = 0;
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $qty = str_replace(",", ".", $qty);
            $dp = str_replace(",", ".", $dp);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $gt = str_replace(",", ".", $gt);

            $blnPeriode = date("m", strtotime($tglInv));
            $thnPeriode = date("Y", strtotime($tglInv));
            $tahunPeriode = date("y", strtotime($tglInv));

            $countKode = DB::table('sales_invoice')
                            ->select(DB::raw("MAX(RIGHT(kode_invoice,2)) AS angka"))
                            // ->whereMonth('tanggal_invoice', $blnPeriode)
                            // ->whereYear('tanggal_invoice', $thnPeriode)
                            ->whereDate('tanggal_invoice', $tglInv)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglInv)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglInv))));

            if ($counter < 10) {
                $kodeInv = "fkj-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeInv = "fkj-cv-".$kodeTgl.$counter;
            }

            $sales = new SalesInvoice();
            $sales->kode_invoice = $kodeInv;
            $sales->id_so = $idSalesOrder;
            $sales->id_rekening = $rekPerusahaan;
            $sales->dp = $dp;
            $sales->dpp = $dpp;
            $sales->ppn = $ppn;
            $sales->grand_total = $gt;
            $sales->ttl_qty = $qty;
            $sales->flag_terms_so = $flagTermsUsage;
            $sales->flag_ppn = $flagPPn;
            $sales->flag_revisi = '0';
            $sales->flag_tf = '0';
            $sales->flag_pembayaran = '0';
            $sales->tanggal_invoice = $tglInv;
            $sales->durasi_jt = $tenor;
            $sales->tanggal_jt = $tglJt;
            $sales->status_invoice = 'draft';
            $sales->id_ppn = $taxSettings->ppn_percentage_id;
            $sales->created_by = $user;
            $sales->save();

            $data = $sales;

            $setDetail = DB::table('sales_invoice_detail')
                            ->where([
                                        ['id_invoice', '=', 'DRAFT'],
                                        ['created_by', '=', $user],
                                    ])
                            ->update([
                                'id_invoice' => $sales->id,
                                'updated_by' => $user
                            ]);

            if ($flagTermsUsage == 0) {
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_invoice' => $sales->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    SalesInvoiceTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Sales Invoice',
                'action' => 'Simpan',
                'desc' => 'Simpan Sales Invoice',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('SalesInvoice.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_invoice).' Telah Disimpan!');
        }
        else {
            return redirect('/SalesInvoice')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'SalesOrder'=>'required',
            'tanggal_inv'=>'required',
        ]);

        $tglInv = $request->input('tanggal_inv');
        $flagPpn = $request->input('stat_ppn');

        $bulanIndonesia = Carbon::parse($tglInv)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglInv);
        if (!$aksesTransaksi) {
            return redirect()->route('SalesInvoice.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglInv);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/SalesInvoice')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $idSalesOrder = $request->input('SalesOrder');
            $tglInv = $request->input('tanggal_inv');
            $tenor = $request->input('durasiJT');
            $tglJt = $request->input('tgl_jt');
            $qty = $request->input('qtyTtl');
            $dp = $request->input('dp');
            $dpp = $request->input('dpp');
            $ppn = $request->input('ppn');
            $gt = $request->input('gt');
            $flagPPn = $request->input('stat_ppn');
            $flagTerms = $request->input('terms_usage');
            $rekPerusahaan = $request->input('company_account');
            $user = Auth::user()->user_name;

            if ($flagTerms == "termsSo") {
                $flagTermsUsage = $idSalesOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $qty = str_replace(",", ".", $qty);
            $dpp = str_replace(",", ".", $dpp);
            $dp = str_replace(",", ".", $dp);
            $ppn = str_replace(",", ".", $ppn);
            $gt = str_replace(",", ".", $gt);

            $countKode = DB::table('sales_invoice')
                            ->select(DB::raw("MAX(RIGHT(kode_invoice,2)) AS angka"))
                            // ->whereMonth('tanggal_invoice', $blnPeriode)
                            // ->whereYear('tanggal_invoice', $thnPeriode)
                            ->whereDate('tanggal_invoice', $tglInv)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglInv)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglInv))));

            if ($counter < 10) {
                $kodeInv = "fkj-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeInv = "fkj-cv-".$kodeTgl.$counter;
            }

            $sales = SalesInvoice::find($id);
            if ($tglInv != $sales->tanggal_invoice) {
                $sales->kode_invoice = $kodeInv;
            }
            $sales->id_so = $idSalesOrder;
            $sales->id_rekening = $rekPerusahaan;
            $sales->dp = $dp;
            $sales->dpp = $dpp;
            $sales->ppn = $ppn;
            $sales->grand_total = $gt;
            $sales->ttl_qty = $qty;
            $sales->flag_terms_so = $flagTermsUsage;
            $sales->flag_ppn = $flagPPn;
            $sales->tanggal_invoice = $tglInv;
            $sales->durasi_jt = $tenor;
            $sales->tanggal_jt = $tglJt;
            $sales->status_invoice = 'draft';
            $sales->id_ppn = $taxSettings->ppn_percentage_id;
            $sales->updated_by = $user;
            $sales->save();

            $data = $sales;

            // $deletedDetail = SalesInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'sales_invoice'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = SalesInvoiceDetail::find($detail->id_detail);
                        $listItem->id_invoice = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->qty_sj = $detail->value3;
                        $listItem->subtotal_sj = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new SalesInvoiceDetail();
                        $listItem->id_invoice = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->qty_sj = $detail->value3;
                        $listItem->subtotal_sj = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('sales_invoice_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'sales_invoice'],
                                    ['value1', '=', $id]
                                ])->delete();

            if ($flagTermsUsage == "0") {
                $delete = DB::table('sales_invoice_terms')->where('id_invoice', '=', $id)->delete();
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_invoice' => $sales->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    SalesInvoiceTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Sales Invoice',
                'action' => 'Update',
                'desc' => 'Update Sales Invoice',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('SalesInvoice.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_invoice).' Telah Diubah!');
        }
        else {
            return redirect('/SalesInvoice')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id) {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $sales = SalesInvoice::find($id);

            if ($btnAction == "posting") {

                $cekSj = HelperSalesInvoice::CheckSJ($id);
                $cekSJ2 = explode("|", $cekSj);

                if ($cekSJ2[0] == "failedInvoiced") {
                    $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Diposting karena terdapat surat jalan ('.strtoupper($cekSJ2[1]).') yang sudah dibuat di invoice lain!';
                    $status = "warning";
                }
                elseif ($cekSJ2[0] == "failedDraft") {
                    $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Diposting karena terdapat surat jalan ('.strtoupper($cekSJ2[1]).') yang masih berstatus draft!';
                    $status = "warning";
                }
                else {
                    try {
                        $salesOrder = SalesOrder::find($sales->id_so);
                        $salesOrder->sisa_dp = $salesOrder->sisa_dp - $sales->dp;
                        $salesOrder->save();

                        // $updatedelivery = DB::table('delivery')
                        //                         ->whereIn('delivery.id', function($subQuery) use ($id) {
                        //                             $subQuery->select('id_sj')->from('sales_invoice_detail')
                        //                             ->where('id_invoice', $id);
                        //                         })
                        //                         ->update([
                        //                             'flag_invoiced' => '1',
                        //                             //'updated_by' => Auth::user()->user_name,
                        //                         ]);


                        $log = ActionLog::create([
                            'module' => 'Sales Invoice',
                            'action' => 'Posting',
                            'desc' => 'Posting Sales Invoice',
                            'username' => Auth::user()->user_name
                        ]);

                        $result = "";

                        $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                        if ($taxSettings->enable_tax == "Y" && $taxSettings->auto_generate_tax_invoice == "Y" && $sales->flag_ppn != "N" & $sales->flag_fp == 0) {
                            $dataTaxInvoice = array();

                            $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                            ->select(
                                                                DB::raw("sales_order.persentase_diskon/100 *  sales_invoice.dpp AS diskon"),
                                                                'sales_invoice.*'
                                                            )
                                                            ->where([
                                                                ['sales_invoice.id', '=', $id],
                                                                ['sales_invoice.flag_fp', '=', 0]
                                                            ])
                                                            ->first();

                            $idSo = $dataSalesInvoice->id_so;
                            $detailSalesInvoice = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                                    ->leftJoin('sales_order_detail',function($qJoin) use ($idSo) {
                                                                        $qJoin->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item')
                                                                        ->where('sales_order_detail.id_so', $idSo);
                                                                    })
                                                                    ->leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                                    ->leftJoin('product_unit', 'product.satuan_item', 'product_unit.id')
                                                                    ->select(
                                                                        'delivery_detail.id_item',
                                                                        'delivery_detail.qty_item',
                                                                        'sales_order_detail.harga_jual',
                                                                        )
                                                                    ->where([
                                                                            ['sales_invoice_detail.id_invoice', '=', $id]
                                                                        ])
                                                                    ->get();

                            $dataTaxInvoice['dataSalesInvoice'] = $dataSalesInvoice;
                            $dataTaxInvoice['detailSalesInvoice'] = $detailSalesInvoice;

                            $result = HelperSalesTaxInvoice::AutoGenerateTaxInvoice($dataTaxInvoice, 0, null);

                        }

                        if ($result == "success") {
                            $sales->flag_fp = 1;
                        }

                        $updateSJ = HelperSalesInvoice::UpdateSJ($id, 1);

                        if ($updateSJ == 'ok') {
                            $sales->status_invoice = "posted";
                            $sales->save();

                            $ar = HelperAccounting::InsertARBalance($sales->id, 'posting');

                            $settings = GLAccountSettings::find(1);
                            $dataSales = SalesOrder::find($sales->id_so);
                            $dataCustomer = Customer::find($dataSales->id_customer);
                            $idAkun = "";
                            $idTransaksi = "";

                            if ($dataCustomer !=  null) {
                                $idAkun = $dataCustomer->id_account ?? $settings->id_account_piutang;
                            }
                            else {
                                $idAkun = $settings->id_account_piutang;
                            }

                            $postJournal = HelperAccounting::PostJournal("sales_invoice", $sales->id, $idAkun, $settings->id_account_penjualan, $sales->tanggal_invoice, $sales->grand_total, 'system');

                            if ($postJournal['error'] == "") {
                                $sales->flag_entry = 1;
                            }

                            $sales->save();

                            $msg = 'Data '.strtoupper($sales->kode_invoice).' Telah Diposting!';
                            $status = 'success';

                            DB::commit();
                        }
                        else {
                            DB::rollBack();

                            $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Diposting!';
                            $status = 'Danger';
                        }


                    }
                    catch (\Exception $e) {
                        DB::rollBack();

                        $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Diposting!';
                        $status = 'danger';
                    }
                }
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                try {
                    if ($sales->status_invoice == "posted" && $sales->flag_tf == 0) {
                        $removeAr = HelperAccounting::RemoveARBalance($sales->id, "revisi");

                        if ($removeAr['error'] != 'success') {
                            $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Direvisi karena sudah terdapat pembayaran untuk invoice!';
                            $status = "warning";
                            DB::rollBack();
                        }
                        else {
                            $updateSJ = HelperSalesInvoice::UpdateSJ($id, 0);

                            if ($updateSJ == 'ok') {

                                $removeJournal = HelperAccounting::RemoveJournal("sales_invoice", $sales->id);
                                if ($removeJournal == "success") {
                                    $sales->flag_entry = 0;
                                }

                                $sales->status_invoice = "draft";
                                $sales->flag_revisi = '1';
                                $sales->updated_by = Auth::user()->user_name;
                                $sales->save();

                                $log = ActionLog::create([
                                    'module' => 'Sales Invoice',
                                    'action' => 'Revisi',
                                    'desc' => 'Revisi Sales Invoice',
                                    'username' => Auth::user()->user_name
                                ]);

                                $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Telah Direvisi!';
                                $status = "success";
                                DB::commit();
                            }
                            else {
                                DB::rollBack();

                                $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Direvisi!';
                                $status = 'danger';
                            }
                            // $updatedelivery = DB::table('delivery')
                            //                     ->whereIn('delivery.id', function($subQuery) use ($id) {
                            //                         $subQuery->select('id_sj')->from('sales_invoice_detail')
                            //                         ->where('id_invoice', $id);
                            //                     })
                            //                     ->update([
                            //                         'flag_invoiced' => '0',
                            //                         //'updated_by' => Auth::user()->user_name,
                            //                     ]);
                        }
                    }
                    else {
                        $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Direvisi karena Invoice Penjualan sudah melewati proses Tukar Faktur!';
                        $status = "warning";
                        DB::rollBack();
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();

                    $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Direvisi!';
                    $status = 'danger';
                }
            }
            elseif ($btnAction == "batalSO") {
                $result = HelperSalesInvoice::CancelInvoice($id);

                if ($result['error'] == "success") {
                    $removeAr = HelperAccounting::RemoveARBalance($sales->id, "revisi");
                    $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Telah Dibatalkan sampai dengan Sales Order!';
                    $status = "success";
                }
                elseif ($result['error'] == "failSJ") {
                    $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Dibatalkan karena masih terdapat Surat Jalan Aktif!';
                    $status = "warning";
                }
                else {
                    $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Dibatalkan!';
                    $status = "warning";
                }

                $log = ActionLog::create([
                    'module' => 'Sales Invoice',
                    'action' => 'Batal SO',
                    'desc' => 'Pembatalan Sales Invoice Ke SO',
                    'username' => Auth::user()->user_name
                ]);
            }
            elseif ($btnAction == "batalINV") {
                try {
                    if ($sales->status_invoice == "posted" && $sales->flag_tf == 0) {
                        $removeAr = HelperAccounting::RemoveARBalance($sales->id, "revisi");

                        if ($removeAr['error'] != 'success') {
                            $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat dibatalkan karena sudah terdapat pembayaran untuk invoice!';
                            $status = "warning";
                            DB::rollBack();
                        }
                        else {
                            $updateSJ = HelperSalesInvoice::UpdateSJ($id, 0);

                            if ($updateSJ == 'ok') {
                                $sales->status_invoice = "batal";
                                $sales->updated_by = Auth::user()->user_name;
                                $sales->save();

                                $log = ActionLog::create([
                                    'module' => 'Sales Invoice',
                                    'action' => 'Batal',
                                    'desc' => 'Batal Sales Invoice',
                                    'username' => Auth::user()->user_name
                                ]);

                                $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Telah Dibatalkan!';
                                $status = "success";
                                DB::commit();
                            }
                            else {
                                DB::rollBack();

                                $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Dibatalkan!';
                                $status = 'danger';
                            }
                        }
                    }
                    else {
                        $msg = 'Sales Invoice '.strtoupper($sales->kode_invoice).' Tidak dapat Dibatalkan karena Invoice sudah melewati proses Tukar Faktur!';
                        $status = "warning";
                        DB::rollBack();
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();

                    $msg = 'Data '.strtoupper($sales->kode_invoice).' Gagal Direvisi!';
                    $status = 'danger';
                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('SalesInvoice.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetSalesInvoiceDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idInv');


            if ($id != "DRAFT") {
                // $detail = SalesInvoiceDetail::where([
                //                             ['id_invoice', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'sales_invoice'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('sales_invoice_detail')->where('id_invoice', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function exportDataSalesInvoice(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new SalesInvoiceExport($request), 'SalesInvoice_'.$kodeTgl.'.xlsx');
    }
}
