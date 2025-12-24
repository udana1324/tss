<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperSalesInvoice;
use App\Classes\BusinessManagement\HelperSalesInvoiceCollection;
use App\Models\Library\Sales;
use App\Models\Accounting\TaxSettings;
use App\Models\Sales\ExpeditionCost;
use App\Models\Sales\ExpeditionCostDetail;
use App\Models\Library\CompanyAccount;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Sales\SalesOrderTerms;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use App\Models\Sales\SalesInvoiceTerms;
use App\Models\Sales\SalesInvoiceCollection;
use App\Models\Sales\SalesInvoiceCollectionDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class SalesInvoiceCollectionController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/SalesInvoiceCollection'],
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
                                                ['module.url', '=', '/SalesInvoiceCollection'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = SalesInvoiceCollection::distinct()->get('status');
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('sales_invoice_collection_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice_collection.index', $data);
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

        $invCollection = SalesInvoiceCollection::leftJoin('customer', 'sales_invoice_collection.id_customer', '=', 'customer.id')
                                                ->select(
                                                    'customer.nama_customer',
                                                    'sales_invoice_collection.id',
                                                    'sales_invoice_collection.kode_tf',
                                                    'sales_invoice_collection.nominal',
                                                    'sales_invoice_collection.tanggal',
                                                    'sales_invoice_collection.pic_penerima',
                                                    'sales_invoice_collection.nmr_tf',
                                                    'sales_invoice_collection.flag_revisi',
                                                    'sales_invoice_collection.flag_approved',
                                                    'sales_invoice_collection.status')
                                                ->when($periode != "", function($q) use ($periode) {
                                                    $q->whereMonth('sales_invoice_collection.tanggal', Carbon::parse($periode)->format('m'));
                                                    $q->whereYear('sales_invoice_collection.tanggal', Carbon::parse($periode)->format('Y'));
                                                })
                                                ->orderBy('sales_invoice_collection.id', 'desc')
                                                ->get();
        return response()->json($invCollection);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesInvoiceCollection'],
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
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->whereIn('sales_order.id', function($query){
                                            $query->select('id_so')->from('sales_invoice');
                                            $query->where([
                                                ['status_invoice', '=', 'posted'],
                                                ['flag_tf', '=', '0']
                                            ]);
                                        })
                                        ->get();

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

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataAccount'] = $CompanyAccount;
                $data['dataPreference'] = $dataPreference;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Buat',
                    'desc' => 'Buat Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('sales_invoice_collection_detail')
                            ->where([
                                ['id_tf', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.invoice_collection.add', $data);
            }
            else {
                return redirect('/SalesInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoiceCollection'],
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
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->whereIn('sales_order.id', function($query){
                                            $query->select('id_so')->from('sales_invoice');
                                            $query->where([
                                                ['status_invoice', '=', 'posted'],
                                                ['flag_tf', '=', '0']
                                            ]);
                                        })
                                        ->get();

                $dataCollection = SalesInvoiceCollection::leftJoin('customer_detail', 'sales_invoice_collection.id_alamat', '=', 'customer_detail.id')
                                                        ->select(
                                                            'sales_invoice_collection.id',
                                                            'sales_invoice_collection.kode_tf',
                                                            'sales_invoice_collection.tanggal',
                                                            'sales_invoice_collection.nominal',
                                                            'sales_invoice_collection.status',
                                                            'sales_invoice_collection.id_customer',
                                                            'sales_invoice_collection.id_alamat',
                                                            'sales_invoice_collection.id_rekening',
                                                            'customer_detail.alamat_customer',
                                                        )
                                                        ->where([
                                                            ['sales_invoice_collection.id', '=', $id],
                                                        ])
                                                        ->first();

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                if ($dataCollection->status != "draft") {
                    return redirect('/SalesInvoiceCollection')->with('warning', 'Tukar Faktur Invoice Penjualan tidak dapat diubah karena status Tukar Faktur bukan DRAFT!');
                }

                $restore = SalesInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
                $restore->restore();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataCollection'] = $dataCollection;
                $data['dataAccount'] = $CompanyAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice_collection.edit', $data);
            }
            else {
                return redirect('/SalesInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCollection = SalesInvoiceCollection::leftJoin('customer_detail', 'sales_invoice_collection.id_alamat', '=', 'customer_detail.id')
                                                        ->leftJoin('customer', 'sales_invoice_collection.id_customer', '=', 'customer.id')
                                                        ->leftJoin('company_account', 'company_account.id', '=', 'sales_invoice_collection.id_rekening')
                                                        ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                        ->select(
                                                            'sales_invoice_collection.id',
                                                            'sales_invoice_collection.kode_tf',
                                                            'sales_invoice_collection.tanggal',
                                                            'sales_invoice_collection.nominal',
                                                            'sales_invoice_collection.status',
                                                            'sales_invoice_collection.id_rekening',
                                                            'sales_invoice_collection.flag_approved',
                                                            'sales_invoice_collection.id_customer',
                                                            'sales_invoice_collection.pic_penerima',
                                                            'sales_invoice_collection.updated_by',
                                                            'customer_detail.alamat_customer',
                                                            'customer.nama_customer',
                                                            'company_account.atas_nama',
                                                            'company_account.nomor_rekening',
                                                            'bank.kode_bank',
                                                            'bank.nama_bank'
                                                        )
                                                        ->where([
                                                            ['sales_invoice_collection.id', '=', $id],
                                                        ])
                                                        ->first();

                $dataTerms = SalesInvoiceTerms::where('id_invoice', $id)->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCollection'] = $dataCollection;
                $data['dataTerms'] = $dataTerms;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Detail',
                    'desc' => 'Detail Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.invoice_collection.detail', $data);
            }
            else {
                return redirect('/SalesInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SalesInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesInvoiceCollection = SalesInvoiceCollection::leftJoin('customer', 'sales_invoice_collection.id_customer', '=', 'customer.id')
                                                                    ->select(
                                                                        'customer.kode_customer',
                                                                        'customer.nama_customer',
                                                                        'customer.npwp_customer',
                                                                        'customer.telp_customer',
                                                                        'customer.fax_customer',
                                                                        'customer.email_customer',
                                                                        'customer.kategori_customer',
                                                                        'customer.sales',
                                                                        'sales_invoice_collection.*'
                                                                    )
                                                                    ->where([
                                                                        ['sales_invoice_collection.id', '=', $id],
                                                                    ])
                                                                    ->first();

                $BiayaEkspedisi = ExpeditionCostDetail::select(
                                                        DB::raw('COALESCE(SUM(expedition_cost_detail.subtotal), 0) AS Biaya')
                                                    )
                                                    ->where([
                                                        ['expedition_cost_detail.flag_tagih', '=', "Y"]
                                                    ])
                                                    ->whereIn('expedition_cost_detail.id_SJ', function($subQuery) use ($id) {
                                                        $subQuery->select('id_sj')->from('sales_invoice_detail')
                                                        ->whereIn('sales_invoice_detail.id_invoice', function($sub2) use ($id) {
                                                           $sub2->select('id_invoice')->from('sales_invoice_collection_detail')
                                                                ->where('id_tf', $id);
                                                        });
                                                    })
                                                    ->first();

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

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->where([
                                                    ['company_account.id', '=', $dataSalesInvoiceCollection->id_rekening]
                                                ])
                                                ->first();

                $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
                                            ->select(
                                                'sales_invoice.id',
                                                DB::raw("SUM(CASE
                                                                WHEN expedition_cost_detail.flag_tagih = 'Y'
                                                                    THEN expedition_cost_detail.subtotal
                                                                ELSE 0
                                                            END) AS BiayaEkspedisi")
                                            )
                                            ->where([
                                                        ['sales_invoice.status_invoice', '=', 'posted']
                                                    ])
                                            ->groupBy('sales_invoice.id');


                $detailSalesInvoiceCollection = SalesInvoiceCollectionDetail::leftJoin('sales_invoice', 'sales_invoice_collection_detail.id_invoice', '=', 'sales_invoice.id')
                                                                            ->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                                                            ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                                                                $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                                                            })
                                                                            ->select(
                                                                                'sales_invoice_collection_detail.id',
                                                                                'sales_invoice.kode_invoice',
                                                                                'sales_invoice.tanggal_invoice',
                                                                                'sales_invoice.tanggal_jt',
                                                                                //'sales_invoice.grand_total',
                                                                                DB::raw("(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total"),
                                                                                'sales_order.no_so',
                                                                                'sales_order.no_po_customer',
                                                                                'customer_detail.nama_outlet'
                                                                                )
                                                                            ->where([
                                                                                    ['sales_invoice_collection_detail.id_tf', '=', $id]
                                                                                ])
                                                                            ->get();

                $dataSales = Sales::find($dataSalesInvoiceCollection->sales);
                $dataAlamat = CustomerDetail::find($dataSalesInvoiceCollection->id_alamat);

                $data['dataSalesInvoiceCollection'] = $dataSalesInvoiceCollection;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesInvoiceCollection'] = $detailSalesInvoiceCollection;
                $data['dataSales'] = $dataSales;
                $data['CompanyAccount'] = $CompanyAccount;
                $data['BiayaEkspedisi'] = $BiayaEkspedisi;

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesInvoiceCollection::cetakPdfInvCollection($data);
                $namaFile = str_replace("/","_",$dataSalesInvoiceCollection->kode_tf);

                $fpdf->Output('I', strtoupper($namaFile).".pdf");
                exit;
            }
            else {
                return redirect('/SalesInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakKwitansi($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SalesInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSalesInvoiceCollection = SalesInvoiceCollection::leftJoin('customer', 'sales_invoice_collection.id_customer', '=', 'customer.id')
                                                                    ->select(
                                                                        'customer.kode_customer',
                                                                        'customer.nama_customer',
                                                                        'customer.npwp_customer',
                                                                        'customer.telp_customer',
                                                                        'customer.fax_customer',
                                                                        'customer.email_customer',
                                                                        'customer.kategori_customer',
                                                                        'customer.sales',
                                                                        'sales_invoice_collection.id_alamat',
                                                                        'sales_invoice_collection.*'
                                                                    )
                                                                    ->where([
                                                                        ['sales_invoice_collection.id', '=', $id],
                                                                    ])
                                                                    ->first();

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

                $detailSalesInvoiceCollection = SalesInvoiceCollectionDetail::leftJoin('sales_invoice', 'sales_invoice_collection_detail.id_invoice', '=', 'sales_invoice.id')
                                                                            ->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                                                            ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                                                            ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                                                            ->select(
                                                                                'sales_invoice_collection_detail.id',
                                                                                'sales_invoice.kode_invoice',
                                                                                'sales_invoice.tanggal_invoice',
                                                                                'sales_invoice.tanggal_jt',
                                                                                'sales_invoice.grand_total',
                                                                                'sales_order.no_so',
                                                                                'sales_order.no_po_customer',
                                                                                'sales_order.nominal_dp',
                                                                                'customer_detail.nama_outlet',
                                                                                'delivery.kode_pengiriman'
                                                                                )
                                                                            ->where([
                                                                                    ['sales_invoice_collection_detail.id_tf', '=', $id]
                                                                                ])
                                                                            ->get();

                $dataSales = Sales::find($dataSalesInvoiceCollection->sales);
                $dataAlamat = CustomerDetail::find($dataSalesInvoiceCollection->id_alamat);

                $data['dataSalesInvoiceCollection'] = $dataSalesInvoiceCollection;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesInvoiceCollection'] = $detailSalesInvoiceCollection;
                $data['dataSales'] = $dataSales;

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperSalesInvoiceCollection::cetakPdfKwitansi($data);

                $fpdf->Output('I', strtoupper($dataSalesInvoiceCollection->kode_tf).".pdf");
                exit;
            }
            else {
                return redirect('/SalesInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function preview(Request $request, Fpdf $fpdf)
    {
        $id = $request->input('idInvoice');

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
                                                    'sales_order.no_po_customer',
                                                    'sales_order.id_alamat',
                                                    'sales_order.metode_pembayaran',
                                                    'sales_order.metode_kirim',
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
                $dataAlamat = CustomerDetail::where([
                                    ['id_customer', '=', $dataSalesInvoice->id_customer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

                if ($dataAlamat == null) {
                    $dataAlamat = CustomerDetail::find($dataSalesInvoice->id_alamat);
                }
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;
                $data['dataSalesInvoice'] = $dataSalesInvoice;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailSalesInvoice'] = $detailSalesInvoice;
                $data['dataSales'] = $dataSales;
                $data['shipDate'] = $shipDate;
                $data['CompanyAccount'] = $CompanyAccount;
                $data['dataBiayaEkspedisi'] = $dataBiayaEkspedisi;

            $log = ActionLog::create([
                'module' => 'Sales Invoice',
                'action' => 'Preview',
                'desc' => 'Preview Sales Invoice',
                'username' => Auth::user()->user_name
            ]);

            $fpdf = HelperSalesInvoice::cetakPdfInv($data);

            $fpdf->Output('F', "preview/preview_invoice.pdf");

        return response()->json("success");
    }

    public function confirm(Request $request)
    {
        $data = new stdClass();
        $penerima = $request->input('namaPenerima');
        $nmr = $request->input('nmr');
        if ($penerima == "" || $penerima == null) {
            return response()->json("false");
        }
        $exception = DB::transaction(function () use ($request, &$data, $penerima, $nmr) {
            $id = $request->input('idCollection');

            $dataCollection = SalesInvoiceCollection::find($id);
            $dataCollection->flag_approved = '1';
            $dataCollection->pic_penerima = $nmr;
            $dataCollection->nmr_tf = $penerima;
            $dataCollection->updated_by = Auth::user()->user_name;
            $dataCollection->save();
            $data = $dataCollection;

        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function getInvoice(Request $request)
    {
        $idCustomer = $request->input('idCustomer');

        $dataInv = SalesInvoice::select(
                                    'sales_invoice.id',
                                    'sales_invoice.kode_invoice',
                                    'sales_invoice.tanggal_invoice',
                                    'sales_invoice.tanggal_jt',
                                    'sales_invoice.grand_total',
                                )
                                ->where([
                                    ['sales_invoice.flag_tf', '=', '0'],
                                    ['sales_invoice.status_invoice', '=', 'posted']
                                ])
                                ->whereIn('sales_invoice.id_so', function($query) use ($idCustomer) {
                                    $query->select('sales_order.id')->from('sales_order');
                                    $query->where([
                                        ['sales_order.id_customer', '=', $idCustomer]
                                    ]);
                                })
                                ->orderBy('sales_invoice.id', 'asc')
                                ->get();

        return response()->json($dataInv);
    }

    public function getInvoiceData(Request $request)
    {
        $idInvoice = $request->input('idInvoice');

        $dataInv = SalesInvoice::where('id', $idInvoice)->get();

        return response()->json($dataInv);
    }

    public function getDefaultAddress(Request $request)
    {
        $idCustomer = $request->input('idCustomer');

        $npwp = CustomerDetail::where([
                                    ['id_customer', '=', $idCustomer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

        $kantor = CustomerDetail::where([
                                    ['id_customer', '=', $idCustomer],
                                    ['jenis_alamat', '=', 'Kantor']
                                ])
                                ->first();

        $defaultAddress = CustomerDetail::where([
                                ['id_customer', '=', $idCustomer]
                            ])
                            ->first();

            return response()->json($defaultAddress);

        if ($npwp == null && $kantor == null) {

            return response()->json($defaultAddress);
        }
        else {
            if ($npwp != null) {
                return response()->json($npwp);
            }
            if ($kantor != null) {
                return response()->json($npwp);
            }
            else {
                return response()->json($defaultAddress);
            }
        }
    }

    public function GetDate(Request $request)
    {
        $id = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $detail = SalesInvoice::select(
                                DB::raw('MAX(sales_invoice.tanggal_invoice) AS lastDate'),
                            )
                            ->whereIn('sales_invoice.id', function($subQuery) use ($id, $user) {
                                $subQuery->select('id_invoice')->from('sales_invoice_collection_detail')
                                ->where('id_tf', $id)
                                ->when($id == "DRAFT", function($q) use ($user) {
                                    $q->where('sales_invoice_collection_detail.created_by', $user);
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

    public function getCustomerAddress(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $customerAddress = CustomerDetail::where([
                                            ['id_customer', '=', $idCustomer]
                                        ])
                                        ->get();

        return response()->json($customerAddress);
    }

    public function StoreInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idCollection');
            $idInv = $request->input('idInv');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            $countItem = DB::table('sales_invoice_collection_detail')->select(DB::raw("COUNT(*) AS angka"))->where([['id_invoice', '=' , $idInv]])->first();
            $count = $countItem->angka;

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listItem = new SalesInvoiceCollectionDetail();
                $listItem->id_tf = $id;
                $listItem->id_invoice = $idInv;
                $listItem->created_by = $user;
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection Detail',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Sales Invoice Collection Detail',
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

    public function SetCollectionDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idCollection');
            $idCustomer = $request->input('idCustomer');
            if ($id == "") {
                $id = 'DRAFT';
            }
            $delete = DB::table('sales_invoice_collection_detail')->where('id_tf', '=', $id)->delete();

            $dataInv = SalesInvoice::select(
                                            'sales_invoice.id'
                                        )
                                        ->where([
                                            ['sales_invoice.flag_tf', '=', '0'],
                                            ['sales_invoice.status_invoice', '=', 'posted']
                                        ])
                                        ->whereIn('sales_invoice.id_so', function($query) use ($idCustomer) {
                                            $query->select('sales_order.id')->from('sales_order');
                                            $query->where([
                                                ['sales_order.id_customer', '=', $idCustomer]
                                            ]);
                                        })
                                        ->get();

            $data = $dataInv;
            $listDetail = [];
            foreach ($dataInv As $detail) {

                $dataDetail = [
                    'id_tf' => $id,
                    'id_invoice' => $detail->id,
                    'created_at' => now(),
                    'created_by' => Auth::user()->user_name,
                ];
                array_push($listDetail, $dataDetail);
            }
            SalesInvoiceCollectionDetail::insert($listDetail);
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
        $id = $request->input('idCollection');
        $user = Auth::user()->user_name;

        if ($id == "") {
            $id = 'DRAFT';
        }

        $detail = SalesInvoiceCollectionDetail::leftJoin('sales_invoice', 'sales_invoice_collection_detail.id_invoice', '=', 'sales_invoice.id')
                                                ->select(
                                                    'sales_invoice_collection_detail.id',
                                                    'sales_invoice_collection_detail.id_invoice',
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'sales_invoice.grand_total'
                                                )
                                                ->when($id == "DRAFT", function($q) use ($user) {
                                                    $q->where('sales_invoice_collection_detail.created_by', $user);
                                                })
                                                ->where([
                                                    ['sales_invoice_collection_detail.id_tf', '=', $id],
                                                ])
                                                ->get();

        return response()->json($detail);
    }

    public function DeleteInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');
            $massDelete = $request->input('massDelete');

            if ($mode != "") {
                if ($massDelete == "Yes") {
                    DB::table('sales_invoice_collection_detail')
                            ->whereIn('id', $id)
                            ->update([
                                'deleted_at' => now(),
                                'deleted_by' => Auth::user()->user_name
                            ]);
                }
                else {
                    $detail = SalesInvoiceCollectionDetail::find($id);
                    $detail->deleted_by = Auth::user()->user_name;
                    $detail->save();

                    $detail->delete();
                }

            }
            else {
                if ($massDelete == "Yes") {
                    DB::table('sales_invoice_collection_detail')
                            ->whereIn('id', $id)
                            ->delete();
                }
                else {
                    $delete = DB::table('sales_invoice_collection_detail')->where('id', '=', $id)->delete();
                }
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function RestoreInvoiceDetail(Request $request)
    {

        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idTf');
            $restore = SalesInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceFooter(Request $request)
    {
        $id = $request->input('idTf');
        $user = Auth::user()->user_name;

        if ($id == "") {
            $id = 'DRAFT';
        }


        $detail = SalesInvoiceCollectionDetail::leftJoin('sales_invoice', 'sales_invoice_collection_detail.id_invoice', '=', 'sales_invoice.id')
                                        ->select(
                                            DB::raw('COALESCE(SUM(sales_invoice.grand_total),0) AS nominalTf'),
                                        )
                                        ->where([
                                            ['sales_invoice_collection_detail.id_tf', '=', $id]
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('sales_invoice_collection_detail.created_by', $user);
                                        })
                                        ->groupBy('sales_invoice_collection_detail.id_tf')
                                        ->first();

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
            'tanggal_tf'=>'required',
        ]);

        $tglTf = $request->input('tanggal_tf');

        $bulanIndonesia = Carbon::parse($tglTf)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglTf);
        if (!$aksesTransaksi) {
            return redirect('/SalesInvoiceCollection')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $tgl = $request->input('tanggal_tf');
            $rekPerusahaan = $request->input('company_account');
            $nominal = $request->input('nominal');
            $user = Auth::user()->user_name;

            $nominal = str_replace(",", ".", $nominal);

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('sales_invoice_collection')
                            ->select(DB::raw("MAX(RIGHT(kode_tf,2)) AS angka"))
                            // ->whereMonth('tanggal', $blnPeriode)
                            // ->whereYear('tanggal', $thnPeriode)
                            ->whereDate('tanggal', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            if ($counter < 10) {
                $kodeTf = "ttf-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeTf = "ttf-cv-".$kodeTgl.$counter;
            }

            $sales = new SalesInvoiceCollection();
            $sales->kode_tf = $kodeTf;
            $sales->id_customer = $idCustomer;
            $sales->id_alamat = $idAlamat;
            $sales->id_rekening = $rekPerusahaan;
            $sales->nominal = $nominal;
            $sales->flag_revisi = '0';
            $sales->flag_approved = '0';
            $sales->tanggal = $tgl;
            $sales->status = 'draft';
            $sales->id_ppn  = $taxSettings->ppn_percentage_id;
            $sales->created_by = $user;
            $sales->save();

            $data = $sales;

            $setDetail = DB::table('sales_invoice_collection_detail')
                            ->where([
                                        ['id_tf', '=', 'DRAFT'],
                                        ['created_by', '=', $user],
                                    ])
                            ->update([
                                'id_tf' => $sales->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Sales Invoice Collection',
                'action' => 'Simpan',
                'desc' => 'Simpan Sales Invoice Collection',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('SalesInvoiceCollection.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_tf).' Telah Disimpan!');
        }
        else {
            return redirect('/SalesInvoiceCollection')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'tanggal_tf'=>'required',
        ]);

        $tglTf = $request->input('tanggal_tf');

        $bulanIndonesia = Carbon::parse($tglTf)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglTf);
        if (!$aksesTransaksi) {
            return redirect()->route('SalesInvoiceCollection.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $tgl = $request->input('tanggal_tf');
            $rekPerusahaan = $request->input('company_account');
            $nominal = $request->input('nominal');
            $user = Auth::user()->user_name;

            $nominal = str_replace(",", ".", $nominal);

            $countKode = DB::table('sales_invoice_collection')
                            ->select(DB::raw("MAX(RIGHT(kode_tf,2)) AS angka"))
                            // ->whereMonth('tanggal', $blnPeriode)
                            // ->whereYear('tanggal', $thnPeriode)
                            ->whereDate('tanggal', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            if ($counter < 10) {
                $kodeTf = "ttf-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeTf = "ttf-cv-".$kodeTgl.$counter;
            }

            $salesCollection = SalesInvoiceCollection::find($id);
            if ($tgl != $salesCollection->tanggal) {
                $salesCollection->kode_tf = $kodeTf;
            }
            $salesCollection->id_customer = $idCustomer;
            $salesCollection->id_alamat = $idAlamat;
            $salesCollection->id_rekening = $rekPerusahaan;
            $salesCollection->nominal = $nominal;
            $salesCollection->tanggal = $tgl;
            $salesCollection->id_ppn  = $taxSettings->ppn_percentage_id;
            $salesCollection->updated_by = $user;
            $salesCollection->save();

            $data = $salesCollection;

            $deletedDetail = SalesInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
            $deletedDetail->forceDelete();

            $log = ActionLog::create([
                'module' => 'Sales Invoice Collection',
                'action' => 'Update',
                'desc' => 'Update Sales Invoice Collection',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('SalesInvoiceCollection.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_tf).' Telah Diubah!');
        }
        else {
            return redirect('/SalesInvoiceCollection')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $salesCollection = SalesInvoiceCollection::find($id);

            if ($btnAction == "posting") {

                $updateInv = HelperSalesInvoiceCollection::UpdateInvoice($id, 1);
                // $updateInvoice = DB::table('sales_invoice')
                //                         ->whereIn('sales_invoice.id', function($subQuery) use ($id) {
                //                             $subQuery->select('id_invoice')->from('sales_invoice_collection_detail')
                //                             ->where('id_tf', $id);
                //                         })
                //                         ->update([
                //                             'flag_tf' => '1',
                //                             'updated_by' => Auth::user()->user_name,
                //                         ]);
                if ($updateInv == 'ok') {
                    $salesCollection->status = "posted";
                    $salesCollection->updated_by = Auth::user()->user_name;
                    $salesCollection->save();

                    $msg = 'Data '.strtoupper($salesCollection->kode_tf).' Telah Diposting!';
                    $status = 'success';

                    DB::commit();
                }
                else {
                    DB::rollBack();

                    $msg = 'Data '.strtoupper($salesCollection->kode_tf).' Gagal Diposting!';
                    $status = 'Danger';
                }


                $log = ActionLog::create([
                    'module' => 'Sales Invoice Collection',
                    'action' => 'Posting',
                    'desc' => 'Posting Sales Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($salesCollection->status == "posted" && $salesCollection->flag_approved == 0) {


                    $updateInv = HelperSalesInvoiceCollection::UpdateInvoice($id, 0);

                    // $updateInvoice = DB::table('sales_invoice')
                    //                     ->whereIn('sales_invoice.id', function($subQuery) use ($id) {
                    //                         $subQuery->select('id_invoice')->from('sales_invoice_collection_detail')
                    //                         ->where('id_tf', $id);
                    //                     })
                    //                     ->update([
                    //                         'flag_tf' => '0',
                    //                         'updated_by' => Auth::user()->user_name,
                    //                     ]);

                    if ($updateInv == 'ok') {
                        $salesCollection->status = "draft";
                        $salesCollection->flag_revisi = '1';
                        $salesCollection->updated_by = Auth::user()->user_name;
                        $salesCollection->save();

                        $msg = 'Tukar Faktur '.strtoupper($salesCollection->kode_tf).' Telah Direvisi!';
                        $status = 'success';

                        DB::commit();
                    }
                    else {
                        DB::rollBack();

                        $msg = 'Data '.strtoupper($salesCollection->kode_tf).' Gagal Direvisi!';
                        $status = 'Danger';
                    }

                    $log = ActionLog::create([
                        'module' => 'Sales Invoice Collection',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Sales Invoice Collection',
                        'username' => Auth::user()->user_name
                    ]);
                }
                else {
                    $msg = 'Tukar Faktur '.strtoupper($salesCollection->kode_tf).' Tidak dapat Direvisi karena Tukar Faktur Telah di Approve !';
                    $status = 'warning';
                }
            }

        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('SalesInvoiceCollection.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }
}
