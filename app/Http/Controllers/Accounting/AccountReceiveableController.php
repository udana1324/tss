<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperAccounting;
use App\Models\Accounting\AccountReceiveable;
use App\Models\Accounting\AccountReceiveableBalance;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Accounting\AccountReceiveableCost;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Library\CompanyAccount;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerGroup;
use App\Models\Sales\ExpeditionCostDetail;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceTerms;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\TempTransaction;
use stdClass;

class AccountReceiveableController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/AccountReceiveable'],
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
                                                ['module.url', '=', '/AccountReceiveable'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('sales_order_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Receiveable',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Account Receiveable',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_receiveable.index', $data);
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
        // $jumlahTransaksi = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                                     ->select(
        //                                         'sales_order.id_customer',
        //                                         DB::raw("COUNT(sales_invoice.kode_invoice) AS countTransaksi"),
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_order.id_customer');

        // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
        //                                     ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
        //                                     ->select(
        //                                         'sales_invoice.id',
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN expedition_cost_detail.flag_tagih = 'Y'
        //                                                             THEN expedition_cost_detail.subtotal
        //                                                         ELSE 0
        //                                                     END) AS BiayaEkspedisi")
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_invoice.id');

        // $totalTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                             ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                 $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                             })
        //                             ->select(
        //                                         'sales_order.id_customer',
        //                                         //DB::raw("COUNT(sales_invoice.kode_invoice) AS countTotal"),
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN sales_invoice.flag_pembayaran = 1
        //                                                             THEN 0
        //                                                         ELSE 1
        //                                                     END) AS countTotal"),
        //                                         DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotal")
        //                                     )
        //                             ->where([
        //                                         ['sales_invoice.status_invoice', '=', 'posted'],
        //                                     ])
        //                             ->groupBy('sales_order.id_customer');

        // $totalTagihanJT = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                                 ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                     $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                                 })
        //                                 ->select(
        //                                             'sales_order.id_customer',
        //                                             DB::raw("COUNT(sales_invoice.kode_invoice) AS countTotalDue"),
        //                                             DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotalDue")
        //                                         )
        //                                 ->where([
        //                                             ['sales_invoice.status_invoice', '=', 'posted'],
        //                                             ['sales_invoice.flag_pembayaran', '=', '0'],
        //                                             ['sales_invoice.tanggal_jt', '<=', Carbon::now()->format('Y-m-d')]
        //                                         ])
        //                                 ->groupBy('sales_order.id_customer');

        // $totalSisaTagihan = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
        //                                         ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
        //                                         ->select(
        //                                                     'account_receiveable.id_customer',
        //                                                     DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
        //                                                 )
        //                                         ->where([
        //                                                     ['sales_invoice.status_invoice', '=', 'posted'],
        //                                                     ['sales_invoice.flag_pembayaran', '!=', '0']
        //                                                 ])
        //                                         ->groupBy('account_receiveable.id_customer');

        // $dataAr = Customer::leftJoinSub($totalTagihan, 'totalTagihan', function($totalTagihan) {
        //                             $totalTagihan->on('customer.id', '=', 'totalTagihan.id_customer');
        //                         })
        //                         ->leftJoinSub($totalTagihanJT, 'totalTagihanJT', function($totalTagihanJT) {
        //                             $totalTagihanJT->on('customer.id', '=', 'totalTagihanJT.id_customer');
        //                         })
        //                         ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                             $totalSisaTagihan->on('customer.id', '=', 'totalSisaTagihan.id_customer');
        //                         })
        //                         ->leftJoinSub($jumlahTransaksi, 'jumlahTransaksi', function($jumlahTransaksi) {
        //                             $jumlahTransaksi->on('customer.id', '=', 'jumlahTransaksi.id_customer');
        //                         })
        //                         ->select(
        //                                 'customer.id',
        //                                 'customer.kode_customer',
        //                                 'customer.nama_customer',
        //                                 'customer.limit_customer',
        //                                 DB::raw("COALESCE(jumlahTransaksi.countTransaksi, 0) AS countTransaksi"),
        //                                 DB::raw("COALESCE(totalTagihan.countTotal, 0) AS countTotal"),
        //                                 DB::raw("COALESCE(totalTagihanJT.countTotalDue, 0) AS countTotalDue"),
        //                                 DB::raw("(COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sumTotal"),
        //                                 DB::raw("COALESCE(totalTagihanJT.sumTotalDue, 0) AS sumTotalDue"),
        //                                 DB::raw("CASE
        //                                             WHEN (COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) > 0
        //                                                 THEN CASE
        //                                                     WHEN (COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) > customer.limit_customer AND customer.limit_customer > 0 THEN '4'
        //                                                     ELSE CASE
        //                                                             WHEN COALESCE(totalTagihanJT.sumTotalDue, 0) = 0 THEN '2'
        //                                                             WHEN COALESCE(totalTagihanJT.sumTotalDue, 0) > 0 THEN '3'
        //                                                         END
        //                                                     END
        //                                                 ELSE CASE
        //                                                         WHEN  COALESCE(totalTagihanJT.sumTotalDue, 0) = 0 AND (COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) = 0 THEN '1'
        //                                                     END
        //                                          END as status"))
        //                         ->where([
        //                             ['jumlahTransaksi.countTransaksi', '>', '0']
        //                         ])
        //                         ->orderByRaw('sumTotal desc')
        //                         ->orderBy('totalTagihan.countTotal', 'desc')
        //                         ->orderBy('totalTagihanJT.sumTotalDue', 'desc')
        //                         ->get();

        $dataAr = Customer::leftJoin('account_receiveable_balance', 'account_receiveable_balance.id_customer', 'customer.id')
                            ->select(
                                'customer.id',
                                'customer.nama_customer',
                                'customer.kode_customer',
                                DB::raw("
                                    COALESCE(SUM(account_receiveable_balance.nominal_outstanding), 0) as 'Totaltagihan',
                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                    COUNT(account_receiveable_balance.id_invoice) AS 'TotalInvoice',
                                    CASE
                                        WHEN SUM(account_receiveable_balance.nominal_outstanding) > customer.limit_customer AND customer.limit_customer != 0 THEN 4
                                        WHEN SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) > 0 THEN 3
                                        WHEN SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) = 0 THEN 2
                                        WHEN SUM(account_receiveable_balance.nominal_outstanding) = 0 THEN 1
                                    END as 'status'
                                ")
                            )
                            ->where([
                                ['customer.deleted_at', '=', null]
                            ])
                            ->groupBy('customer.id')
                            ->orderByRaw('Totaltagihan desc')
                            ->orderByRaw('TotalInvoice desc')
                            ->orderByRaw('TotalInvoiceJT desc')
                            ->get();

        return response()->json($dataAr);
    }

    public function indexGroup()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/AccountReceiveable/GroupPayment'],
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
                                                ['module.url', '=', '/AccountReceiveable/GroupPayment'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataGroup = CustomerGroup::distinct()->orderBy('nama_group', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataGroup'] = $dataGroup;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Receiveable',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Account Receiveable',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_receiveable.index_group', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndexGroup()
    {

        $dataAr = CustomerGroup::leftJoin('customer_group_detail', 'customer_group_detail.id_group', 'customer_group.id')
                            ->leftJoin('customer', 'customer_group_detail.id_customer', '=', 'customer.id')
                            ->leftJoin('account_receiveable_balance', 'account_receiveable_balance.id_customer', 'customer.id')
                            ->select(
                                'customer_group.id',
                                'customer_group.nama_group',
                                DB::raw("
                                    COALESCE(SUM(account_receiveable_balance.nominal_outstanding), 0) as 'Totaltagihan',
                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                    COUNT(account_receiveable_balance.id_invoice) AS 'TotalInvoice',
                                    CASE
                                        WHEN SUM(account_receiveable_balance.nominal_outstanding) > customer.limit_customer AND customer.limit_customer != 0 THEN 4
                                        WHEN SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) > 0 THEN 3
                                        WHEN SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) = 0 THEN 2
                                        WHEN SUM(account_receiveable_balance.nominal_outstanding) = 0 THEN 1
                                    END as 'status'
                                ")
                            )
                            ->where([
                                ['customer.deleted_at', '=', null],
                            ])
                            ->groupBy('customer_group.id')
                            ->orderByRaw('Totaltagihan desc')
                            ->orderByRaw('TotalInvoice desc')
                            ->orderByRaw('TotalInvoiceJT desc')
                            ->get();

        return response()->json($dataAr);
    }

    public function getDataTagihan(Request $request)
    {

        $idCustomer = $request->input('idCustomer');

        // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
        //                                     ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
        //                                     ->select(
        //                                         'sales_invoice.id',
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN expedition_cost_detail.flag_tagih = 'Y'
        //                                                             THEN expedition_cost_detail.subtotal
        //                                                         ELSE 0
        //                                                     END) AS BiayaEkspedisi")
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_invoice.id');

        $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                    ->select(
                                                                'sales_invoice.id',
                                                                DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                            )
                                                    ->where([
                                                    ])
                                                    ->groupBy('account_receiveable_cost.id_invoice');


        // $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                             // ->leftJoin('account_receiveable_detail', 'sales_invoice.id', 'account_receiveable_detail.id_invoice')
        //                             // ->leftJoin('account_receiveable', 'account_receiveable.id', 'account_receiveable_detail.id_ar')
        //                             ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
        //                                 $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
        //                             })
        //                             ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                 $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                             })
        //                             ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
        //                             ->select(
        //                                 'sales_invoice.id',
        //                                 'sales_invoice.kode_invoice',
        //                                 'sales_invoice.tanggal_invoice',
        //                                 'sales_invoice.tanggal_jt',
        //                                 DB::raw("COALESCE(sales_order.no_po_customer, '-') AS no_po_customer"),
        //                                 DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total'),
        //                                 'sales_invoice.flag_pembayaran',
        //                                 DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
        //                                 DB::raw("COALESCE(customer_detail.nama_outlet, '-') AS nama_outlet"),
        //                                 // DB::raw("COALESCE(account_receiveable.jenis_pembayaran, '-') AS jenis_pembayaran"),
        //                                 // 'account_receiveable.tanggal'
        //                             )
        //                             ->where([
        //                                 ['sales_order.id_customer', '=', $idCustomer],
        //                                 ['sales_invoice.flag_pembayaran', '=', 1]
        //                             ])
        //                             ->orderBy('sales_invoice.id', 'desc')
        //                             ->get();

        $dataTagihan = AccountReceiveableBalance::leftJoin('sales_invoice', 'account_receiveable_balance.id_invoice', 'sales_invoice.id')
                                                ->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                                ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                                    $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                                })
                                                ->select(
                                                    'sales_invoice.id',
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(sales_order.no_po_customer, '-') AS no_po_customer"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_invoice, 0) AS grand_total"),
                                                    'sales_invoice.flag_pembayaran',
                                                    DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                                    DB::raw("COALESCE(customer_detail.nama_outlet, '-') AS nama_outlet"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_invoice, 0) - COALESCE(account_receiveable_balance.nominal_outstanding, 0) AS nominal_bayar"),
                                                    DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['account_receiveable_balance.id_customer', '=', $idCustomer]
                                                ])
                                                ->orderByRaw('sales_invoice.tanggal_invoice desc')
                                                ->orderByRaw('sales_invoice.id desc')
                                                ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanLunas(Request $request)
    {

        $idCustomer = $request->input('idCustomer');

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

        $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                    ->select(
                                                                'sales_invoice.id',
                                                                DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                            )
                                                    ->where([
                                                    ])
                                                    ->groupBy('account_receiveable_cost.id_invoice');


        $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    // ->leftJoin('account_receiveable_detail', 'sales_invoice.id', 'account_receiveable_detail.id_invoice')
                                    // ->leftJoin('account_receiveable', 'account_receiveable.id', 'account_receiveable_detail.id_ar')
                                    ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                        $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                    })
                                    ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                        $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                    })
                                    ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'sales_invoice.id',
                                        'sales_invoice.kode_invoice',
                                        'sales_invoice.tanggal_invoice',
                                        'sales_invoice.tanggal_jt',
                                        DB::raw("COALESCE(sales_order.no_po_customer, '-') AS no_po_customer"),
                                        DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total'),
                                        'sales_invoice.flag_pembayaran',
                                        DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                        DB::raw("COALESCE(customer_detail.nama_outlet, '-') AS nama_outlet"),
                                        // DB::raw("COALESCE(account_receiveable.jenis_pembayaran, '-') AS jenis_pembayaran"),
                                        // 'account_receiveable.tanggal'
                                    )
                                    ->where([
                                        ['sales_order.id_customer', '=', $idCustomer],
                                        ['sales_invoice.flag_pembayaran', '=', 1]
                                    ])
                                    ->orderBy('sales_invoice.id', 'desc')
                                    ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanGroup(Request $request)
    {

        $idGroup = $request->input('idGroup');


        $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                    ->select(
                                                                'sales_invoice.id',
                                                                DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                            )
                                                    ->where([
                                                    ])
                                                    ->groupBy('account_receiveable_cost.id_invoice');

        $dataTagihan = AccountReceiveableBalance::leftJoin('sales_invoice', 'account_receiveable_balance.id_invoice', 'sales_invoice.id')
                                                ->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                                ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                                ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                                    $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                                })
                                                ->select(
                                                    'sales_invoice.id',
                                                    'customer.nama_customer',
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(sales_order.no_po_customer, '-') AS no_po_customer"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_invoice, 0) AS grand_total"),
                                                    'sales_invoice.flag_pembayaran',
                                                    DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                                    DB::raw("COALESCE(customer_detail.nama_outlet, '-') AS nama_outlet"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_invoice, 0) - COALESCE(account_receiveable_balance.nominal_outstanding, 0) AS nominal_bayar"),
                                                    DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['customer.deleted_at', '=', null]
                                                ])
                                                ->whereIn('customer.id', function($query) use ($idGroup) {
                                                    $query->select('id_customer')->from('customer_group_detail')->where('id_group', '=', $idGroup);
                                                })
                                                ->orderBy('customer.nama_customer', 'asc')
                                                ->orderByRaw('sales_invoice.tanggal_invoice desc')
                                                ->orderByRaw('sales_invoice.id desc')
                                                ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanLunasGroup(Request $request)
    {

        $idGroup = $request->input('idGroup');

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

        $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                    ->select(
                                                                'sales_invoice.id',
                                                                DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                            )
                                                    ->where([
                                                    ])
                                                    ->groupBy('account_receiveable_cost.id_invoice');


        $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    // ->leftJoin('account_receiveable_detail', 'sales_invoice.id', 'account_receiveable_detail.id_invoice')
                                    // ->leftJoin('account_receiveable', 'account_receiveable.id', 'account_receiveable_detail.id_ar')
                                    ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                        $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                    })
                                    ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                        $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                    })
                                    ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->select(
                                        'sales_invoice.id',
                                        'customer.nama_customer',
                                        'sales_invoice.kode_invoice',
                                        'sales_invoice.tanggal_invoice',
                                        'sales_invoice.tanggal_jt',
                                        DB::raw("COALESCE(sales_order.no_po_customer, '-') AS no_po_customer"),
                                        DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total'),
                                        'sales_invoice.flag_pembayaran',
                                        DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                        DB::raw("COALESCE(customer_detail.nama_outlet, '-') AS nama_outlet"),
                                        // DB::raw("COALESCE(account_receiveable.jenis_pembayaran, '-') AS jenis_pembayaran"),
                                        // 'account_receiveable.tanggal'
                                    )
                                    ->where([
                                        ['customer.deleted_at', '=', null],
                                        ['sales_invoice.flag_pembayaran', '=', 1]
                                    ])
                                    ->whereIn('customer.id', function($query) use ($idGroup) {
                                        $query->select('id_customer')->from('customer_group_detail')->where('id_group', '=', $idGroup);
                                    })
                                    ->orderBy('sales_invoice.id', 'desc')
                                    ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanCustomer(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
        //                                     ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
        //                                     ->select(
        //                                         'sales_invoice.id',
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN expedition_cost_detail.flag_tagih = 'Y'
        //                                                             THEN expedition_cost_detail.subtotal
        //                                                         ELSE 0
        //                                                     END) AS BiayaEkspedisi")
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_invoice.id');

        // $totalTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                             ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                 $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                             })
        //                             ->select(
        //                                         'sales_order.id_customer',
        //                                         // DB::raw("COUNT(sales_invoice.kode_invoice) AS countTotal"),
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN sales_invoice.flag_pembayaran = 1
        //                                                             THEN 0
        //                                                         ELSE 1
        //                                                     END) AS countTotal"),
        //                                         DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotal")
        //                                     )
        //                             ->where([
        //                                         ['sales_invoice.status_invoice', '=', 'posted']
        //                                     ])
        //                             ->groupBy('sales_order.id_customer');

        // $totalTagihanJT = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                                 ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                     $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                                 })
        //                                 ->select(
        //                                             'sales_order.id_customer',
        //                                             DB::raw("COUNT(sales_invoice.kode_invoice) AS countTotalDue"),
        //                                             DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotalDue")
        //                                         )
        //                                 ->where([
        //                                             ['sales_invoice.status_invoice', '=', 'posted'],
        //                                             ['sales_invoice.flag_pembayaran', '=', '0'],
        //                                             ['sales_invoice.tanggal_jt', '<=', Carbon::now()->format('Y-m-d')]
        //                                         ])
        //                                 ->groupBy('sales_order.id_customer');

        // $totalSisaTagihan = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
        //                                         ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
        //                                         ->select(
        //                                                     'account_receiveable.id_customer',
        //                                                     DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
        //                                                 )
        //                                         ->where([
        //                                                     ['sales_invoice.flag_pembayaran', '!=', '0']
        //                                                 ])
        //                                         ->groupBy('account_receiveable.id_customer');

        // $dataAr = Customer::leftJoinSub($totalTagihan, 'totalTagihan', function($totalTagihan) {
        //                             $totalTagihan->on('customer.id', '=', 'totalTagihan.id_customer');
        //                         })
        //                         ->leftJoinSub($totalTagihanJT, 'totalTagihanJT', function($totalTagihanJT) {
        //                             $totalTagihanJT->on('customer.id', '=', 'totalTagihanJT.id_customer');
        //                         })
        //                         ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                             $totalSisaTagihan->on('customer.id', '=', 'totalSisaTagihan.id_customer');
        //                         })
        //                         ->select(
        //                                 'customer.id',
        //                                 'customer.kode_customer',
        //                                 'customer.nama_customer',
        //                                 'customer.limit_customer',
        //                                 'totalTagihan.sumTotal',
        //                                 'totalSisaTagihan.sumBayar',
        //                                 DB::raw("COALESCE(totalTagihan.countTotal, 0) AS countTotal"),
        //                                 DB::raw("COALESCE(totalTagihanJT.countTotalDue, 0) AS countTotalDue"),
        //                                 DB::raw("(COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sumTotal"),
        //                                 DB::raw("COALESCE(totalTagihanJT.sumTotalDue, 0) AS sumTotalDue"))
        //                         ->where([
        //                             ['customer.id', '=', $idCustomer],
        //                             //['totalTagihan.countTotal', '>', '0']
        //                         ])
        //                         ->orderBy('customer.id', 'asc')
        //                         ->first();

        $dataAr = AccountReceiveableBalance::leftJoin('customer', 'account_receiveable_balance.id_customer', 'customer.id')
                                                ->select(
                                                    'customer.limit_customer',
                                                    DB::raw("
                                                        SUM(account_receiveable_balance.nominal_outstanding) as 'TotalTagihan',
                                                        SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                                        SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                                        COUNT(account_receiveable_balance.id_invoice) AS 'TotalInvoice'
                                                    ")
                                                )
                                                ->where([
                                                    ['customer.id', '=', $idCustomer]
                                                ])
                                                ->groupBy('account_receiveable_balance.id_customer')
                                                ->first();

        return response()->json($dataAr);
    }

    public function getDataTagihanCustomerGroup(Request $request)
    {
        $idGrup = $request->input('id_grup');

        $dataAr = AccountReceiveableBalance::leftJoin('customer', 'account_receiveable_balance.id_customer', 'customer.id')
                                            ->leftJoin('customer_group_detail', 'customer_group_detail.id_customer', 'customer.id')
                                            ->select(
                                                DB::raw("
                                                    SUM(account_receiveable_balance.nominal_outstanding) as 'TotalTagihan',
                                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                                    SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                                    COUNT(account_receiveable_balance.id_invoice) AS 'TotalInvoice'
                                                ")
                                            )
                                            ->where([
                                                ['customer.deleted_at', '=', null],
                                                ['customer_group_detail.id_group', '=', $idGrup]
                                            ])
                                            ->groupBy('customer_group_detail.id_group')
                                            ->first();

        return response()->json($dataAr);
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/AccountReceiveable'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::find($id);

                $dataAlamat = CustomerDetail::where([
                                                ['id_customer', '=', $id],
                                                ['default', '=', 'Y']
                                            ])
                                            ->first();

                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.id',
                                                    'company_account.nomor_rekening',
                                                    'company_account.atas_nama',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataAlamat'] = $dataAlamat != null ? $dataAlamat->alamat_customer.' '.$dataAlamat->kelurahan.' '.$dataAlamat->kecamatan.' '.$dataAlamat->kota : '-';
                $data['dataRekening'] = $dataRekening;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Receiveable',
                    'action' => 'Detail',
                    'desc' => 'Detail Account Receiveable',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_receiveable.detail', $data);
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detailGroup($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/AccountReceiveable/GroupPayment'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomerGroup = CustomerGroup::find($id);

                $dataRekening = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.id',
                                                    'company_account.nomor_rekening',
                                                    'company_account.atas_nama',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomerGroup'] = $dataCustomerGroup;
                $data['dataRekening'] = $dataRekening;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Receiveable Group',
                    'action' => 'Detail',
                    'desc' => 'Detail Account Receiveable Group',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_receiveable.detail_group', $data);
            }
            else {
                return redirect('/SalesInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getInvoiceData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
        //                                     ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
        //                                     ->select(
        //                                         'sales_invoice.id',
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN expedition_cost_detail.flag_tagih = 'Y'
        //                                                             THEN expedition_cost_detail.subtotal
        //                                                         ELSE 0
        //                                                     END) AS BiayaEkspedisi")
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_invoice.id');

        // $totalSisaTagihan = AccountReceiveableDetail::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
        //                                             ->select(
        //                                                         'sales_invoice.id',
        //                                                         DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
        //                                                     )
        //                                             ->where([
        //                                                         ['sales_invoice.flag_pembayaran', '!=', '1'],
        //                                                         ['account_receiveable_detail.id_invoice', '=', $idInvoice],
        //                                                     ])
        //                                             ->groupBy('account_receiveable_detail.id_invoice');


        // $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                             ->leftJoin('account_receiveable', 'account_receiveable.id_customer', 'sales_order.id_customer')
        //                             ->leftJoin('account_receiveable_detail', 'sales_invoice.id', 'account_receiveable_detail.id_invoice')
        //                             ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                                 $totalSisaTagihan->on('sales_invoice.id', '=', 'totalSisaTagihan.id');
        //                             })
        //                             ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                                 $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                             })
        //                             ->select(
        //                                         'sales_invoice.id',
        //                                         'sales_invoice.kode_invoice',
        //                                         'sales_invoice.tanggal_invoice',
        //                                         'sales_invoice.tanggal_jt',
        //                                         // 'sales_invoice.grand_total',
        //                                         DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total'),
        //                                         'sales_invoice.flag_pembayaran',
        //                                         DB::raw("COALESCE(account_receiveable.jenis_pembayaran, '-') AS jenis_pembayaran"),
        //                                         DB::raw("COALESCE(account_receiveable_detail.nominal_bayar, 0) AS nominal_bayar"),
        //                                         DB::raw("((COALESCE(sales_invoice.grand_total, 0) + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sisa_tagihan"),
        //                                     )
        //                             ->orderBy('sales_invoice.id', 'desc')
        //                             ->where([
        //                                 ['sales_invoice.id', '=', $idInvoice],
        //                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                             ])
        //                             ->get();

            $dataTagihan = AccountReceiveableBalance::leftJoin('sales_invoice', 'account_receiveable_balance.id_invoice', 'sales_invoice.id')
                                                ->select(
                                                    'sales_invoice.id',
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(account_receiveable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['account_receiveable_balance.id_invoice', '=', $idInvoice]
                                                ])
                                                ->first();

        return response()->json($dataTagihan);
    }

    public function getCostData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                        ->select(
                                                                    'sales_invoice.id',
                                                                    DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                                )
                                                        ->where([
                                                                    ['sales_invoice.id', '=', $idInvoice]
                                                                ])
                                                        ->groupBy('account_receiveable_cost.id_invoice');


        $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftJoin('account_receiveable', 'account_receiveable.id_customer', 'sales_order.id_customer')
                                    ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                        $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                    })
                                    ->select(
                                                'sales_invoice.id',
                                                'sales_invoice.kode_invoice',
                                                'sales_invoice.tanggal_invoice',
                                                'sales_invoice.tanggal_jt',
                                                DB::raw("(COALESCE(account_receiveable.nominal_potongan, 0) - COALESCE(totalPotonganTagihan.sumPotongan,0)) AS sisa_potongan"),
                                            )
                                    ->orderBy('sales_invoice.id', 'desc')
                                    ->where([
                                        ['sales_invoice.id', '=', $idInvoice],
                                        ['sales_invoice.status_invoice', '=', 'posted']
                                    ])
                                    ->get();

        return response()->json($dataTagihan);
    }

    public function getCostList(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $dataPotongan = AccountReceiveableCost::where([
                                                    ['id_invoice', '=', $idInvoice]
                                                ])
                                                ->get();

        return response()->json($dataPotongan);
    }

    public function getPaymentData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $dataPotongan = AccountReceiveableDetail::leftJoin('account_receiveable', 'account_receiveable_detail.id_ar', 'account_receiveable.id')
                                                ->leftJoin('company_account', 'account_receiveable.rekening_pembayaran', '=', 'company_account.id')
                                                ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'account_receiveable.id',
                                                    'account_receiveable.kode_ar',
                                                    'account_receiveable.jenis_pembayaran',
                                                    'account_receiveable.tanggal',
                                                    'account_receiveable.tanggal_jt_giro',
                                                    'account_receiveable_detail.nominal_bayar',
                                                    DB::raw("COALESCE(account_receiveable.keterangan, '-') AS keterangan"),
                                                    DB::raw("CASE WHEN account_receiveable.rekening_pembayaran = null then ' - '
                                                                   ELSE CONCAT(bank.nama_bank, ' - ', company_account.nomor_rekening, ' A/N ', company_account.atas_nama)
                                                            END AS rekening")
                                                )
                                                ->where([
                                                    ['id_invoice', '=', $idInvoice]
                                                ])
                                                ->get();

        return response()->json($dataPotongan);
    }

    public function StoreAccountReceiveable(Request $request)
    {
        $idCust = $request->input('idCustomer');
        $idGroup = $request->input('idGroup');
        $rekening = $request->input('Rekening');
        $jenisPembayaran = $request->input('JenisPembayaran');
        $tanggalBayar = $request->input('Tanggal');
        $tanggalJTGiro = $request->input('TanggalJTGiro');
        $keterangan = $request->input('Keterangan');
        $potongan = $request->input('Potongan');
        $nominal = $request->input('Nominal');
        $sisa = $request->input('Sisa');
        $idInvoice = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $nominal = str_replace(",", ".", $nominal);
        $potongan = str_replace(",", ".", $potongan);
        $sisa = str_replace(",", ".", $sisa);

        $blnPeriode = date("m", strtotime($tanggalBayar));
        $thnPeriode = date("Y", strtotime($tanggalBayar));

        $countKode = DB::table('account_receiveable')
                        ->select(DB::raw("MAX(RIGHT(kode_ar,2)) AS angka"))
                        // ->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tanggalBayar)
                        ->first();

        $count = $countKode->angka;
        $counter = $count + 1;

        $kodeTgl = Carbon::parse($tanggalBayar)->format('ymd');

        if ($counter < 10) {
            $kodeAr = "ar-cv-".$kodeTgl."0".$counter;
        }
        else {
            $kodeAr = "ar-cv-".$kodeTgl.$counter;
        }

        if ($idCust == "" || $idCust == null) {
            $dataInv = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->select(
                                        'customer.id'
                                    )
                                    ->first();

            if ($dataInv !=  null && $dataInv != "") {
                $idCust = $dataInv->id;
            }
            else {
                return response()->json("failed");
            }
        }

        $AccountReceiveable = new AccountReceiveable();
        $AccountReceiveable->kode_ar = $kodeAr;
        $AccountReceiveable->id_customer = $idCust;
        $AccountReceiveable->rekening_pembayaran = $rekening;
        $AccountReceiveable->jenis_pembayaran = $jenisPembayaran;
        $AccountReceiveable->keterangan = $keterangan;
        $AccountReceiveable->nominal = $nominal;
        $AccountReceiveable->tanggal = $tanggalBayar;
        if ($jenisPembayaran == "giro") {
            $AccountReceiveable->tanggal_jt_giro = $tanggalJTGiro;
        }
        $AccountReceiveable->flag_revisi = 0;
        if ($potongan > 0) {
            $AccountReceiveable->flag_potongan = 1;
            $AccountReceiveable->nominal_potongan = $potongan;
        }
        else {
            $AccountReceiveable->flag_potongan = 0;
            $AccountReceiveable->nominal_potongan = 0;
        }
        $AccountReceiveable->status = 'posted';
        $AccountReceiveable->created_by = $user;
        $AccountReceiveable->save();

        if ($AccountReceiveable) {

            $ARDetail = new AccountReceiveableDetail();
            $ARDetail->id_ar = $AccountReceiveable->id;
            $ARDetail->id_invoice = $idInvoice;
            $ARDetail->nominal_bayar = $nominal;
            $ARDetail->nominal_sisa = $sisa;
            $ARDetail->created_by = $user;
            $ARDetail->save();

            $salesInvoice = SalesInvoice::find($idInvoice);
            if ($sisa == 0) {
                $salesInvoice->flag_pembayaran = 1;
            }
            else {
                $salesInvoice->flag_pembayaran = 2;
            }
            $salesInvoice->save();
            HelperAccounting::PaymentARBalance($salesInvoice->id, $nominal);
            HelperAccounting::RemoveARBalance($salesInvoice->id, 'payment');

            if ($rekening != "") {
                $akun = 2;
                $akunRekening = CompanyAccount::find($rekening);
                $settings = GLAccountSettings::find(1);
                $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                $customer = Customer::find($idCust);
                $akunCustomer = $customer->id_account ?? 0;
                $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $nominal, $AccountReceiveable->id);
                $postJournal = HelperAccounting::PostJournal("bank_masuk", $AccountReceiveable->id, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $nominal, 'system');
            }
            else {
                $akun = 1;
                $settings = GLAccountSettings::find(1);
                $idAkunKas = $settings->id_account_kas ?? 0;
                $customer = Customer::find($idCust);
                $akunCustomer = $customer->id_account ?? 0;
                $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunKas, $idAkunPiutang, $tanggalBayar, $nominal, $AccountReceiveable->id);
                $postJournal = HelperAccounting::PostJournal("kas_masuk", $AccountReceiveable->id, $idAkunKas, $idAkunPiutang, $tanggalBayar, $nominal, 'system');
            }
        }

        $log = ActionLog::create([
            'module' => 'Account Receiveable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Receiveable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function StoreAccountReceiveableCost(Request $request)
    {
        $keterangan = $request->input('keterangan');
        $potongan = $request->input('potongan');
        $idInvoice = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $potongan = str_replace(",", ".", $potongan);

        $AccountReceiveableCost = new AccountReceiveableCost();
        $AccountReceiveableCost->id_invoice = $idInvoice;
        $AccountReceiveableCost->nominal = $potongan;
        $AccountReceiveableCost->keterangan = $keterangan;
        $AccountReceiveableCost->created_by = $user;
        $AccountReceiveableCost->save();

        $log = ActionLog::create([
            'module' => 'Account Receiveable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Receiveable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function setDataTagihanMass(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('idCustomer');
            $invoices = $request->input('invoices');

            if ($idCustomer != "") {
                $deleteTemp = DB::table('temp_transaction')
                                    ->where([
                                        ['module', '=', 'account_receiveable'],
                                        ['value1', '=', $idCustomer],
                                    ])
                                    ->whereIn('value2', $invoices)
                                    ->delete();

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

                $totalSisaTagihan = AccountReceiveableDetail::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
                                                            ->select(
                                                                        'sales_invoice.id',
                                                                        DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
                                                                    )
                                                            // ->where([
                                                            //             ['sales_invoice.flag_pembayaran', '!=', '1']
                                                            //         ])
                                                            ->groupBy('account_receiveable_detail.id_invoice');

                $totalPotonganTagihan = AccountReceiveableCost::leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_cost.id_invoice')
                                                            ->select(
                                                                        'sales_invoice.id',
                                                                        DB::raw("SUM(account_receiveable_cost.nominal) AS sumPotongan")
                                                                    )
                                                            // ->where([
                                                            //             ['sales_invoice.flag_pembayaran', '!=', '1']
                                                            //         ])
                                                            ->groupBy('account_receiveable_cost.id_invoice');


                $dataTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                //->leftJoin('account_receiveable', 'account_receiveable.id_customer', 'sales_order.id_customer')
                                                //->leftJoin('account_receiveable_detail', 'sales_invoice.id', 'account_receiveable_detail.id_invoice')
                                                ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
                                                    $totalSisaTagihan->on('sales_invoice.id', '=', 'totalSisaTagihan.id');
                                                })
                                                ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                                    $totalPotonganTagihan->on('sales_invoice.id', '=', 'totalPotonganTagihan.id');
                                                })

                                                ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                                    $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                                })

                                                ->select(
                                                            'sales_invoice.id',
                                                            'sales_invoice.kode_invoice',
                                                            'sales_invoice.tanggal_invoice',
                                                            'sales_invoice.tanggal_jt',

                                                            // 'sales_invoice.grand_total',
                                                            DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total'),

                                                            'sales_invoice.grand_total',

                                                            'sales_invoice.flag_pembayaran',
                                                            DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                                        // 'account_receiveable.flag_potongan',
                                                        // DB::raw("COALESCE(account_receiveable.jenis_pembayaran, '-') AS jenis_pembayaran"),
                                                            DB::raw("COALESCE(totalSisaTagihan.sumBayar, 0) AS nominal_bayar"),
                                                            DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),

                                                            DB::raw("((COALESCE(sales_invoice.grand_total, 0) + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) - COALESCE(totalSisaTagihan.sumBayar,0)) - COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sisa_tagihan"),

                                                            DB::raw("(COALESCE(sales_invoice.grand_total, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) - COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sisa_tagihan"),

                                                        )
                                                ->orderBy('sales_invoice.id', 'asc')
                                                ->where([
                                                    ['sales_order.id_customer', '=', $idCustomer],
                                                    ['sales_invoice.flag_pembayaran', '!=', '1']
                                                ])
                                                ->whereIn('sales_invoice.id', $invoices)
                                                ->get();

                if ($dataTagihan != "") {
                    $listTemp = [];
                    foreach ($dataTagihan as $detail) {
                        $dataTemps = [
                            'module' => 'account_receiveable',
                            'value1' => $detail->id_customer,
                            'value2' => $detail->id,
                            'value3' => $detail->tanggal_invoice,
                            'value4' => $detail->tanggal_jt,
                            'value5' => $detail->kode_invoice,
                            'value6' => $detail->grand_total,
                            'value7' => $detail->nominal_bayar,
                            'value8' => $detail->sisa_tagihan,
                            'value9' => 0,
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
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

    public function getDataTagihanMass(Request $request)
    {

        $idCustomer = $request->input('idCustomer');
        $idGroup = $request->input('idGroup');
        $invoices = $request->input('invoices');

        if ($idCustomer != "" || $idGroup != "") {

            $dataTagihan = TempTransaction::select(
                                                'temp_transaction.id',
                                                'temp_transaction.value2',
                                                'temp_transaction.value3',
                                                'temp_transaction.value4',
                                                'temp_transaction.value5',
                                                'temp_transaction.value6',
                                                'temp_transaction.value7',
                                                'temp_transaction.value8',
                                                'temp_transaction.value9',
                                            )
                                            ->where([
                                                ['temp_transaction.module', '=', 'account_receiveable']
                                            ])
                                            ->when($idCustomer != "", function($q) use ($idCustomer) {
                                                $q->where('temp_transaction.value1', $idCustomer);
                                            })
                                            ->when($idGroup != "", function($q) use ($idGroup) {
                                                $q->whereIn('temp_transaction.value1', function($query) use ($idGroup) {
                                                    $query->select('id_customer')->from('customer_group_detail')->where('id_group', '=', $idGroup);
                                                });
                                            })
                                            ->whereIn('value2', $invoices)
                                            ->get();
        }
        else {
            $dataTagihan = null;
        }

        return response()->json($dataTagihan);
    }

    public function GetDataMass(Request $request)
    {
        $ids = $request->input('idInvoice');
        $idCustomer = $request->input('idCustomer');
        $idGroup = $request->input('idGroup');

        $dataTagihan = TempTransaction::select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            'temp_transaction.value9',
                                        )
                                        ->where([
                                            ['temp_transaction.module', '=', 'account_receiveable']
                                        ])
                                        ->when($idCustomer != "", function($q) use ($idCustomer) {
                                            $q->where('temp_transaction.value1', $idCustomer);
                                        })
                                        ->when($idGroup != "", function($q) use ($idGroup) {
                                            $q->whereIn('temp_transaction.value1', function($query) use ($idGroup) {
                                                $query->select('id_customer')->from('customer_group_detail')->where('id_group', '=', $idGroup);
                                            });
                                        })
                                        ->whereIn('value2', $ids)
                                        ->orderBy('value3', 'asc')
                                        ->get();

        $data = new stdClass();
        $data->total_tagihan = collect($dataTagihan)->sum('value8') - collect($dataTagihan)->sum('value9');
        $data->total_invoice = collect($dataTagihan)->sum('value6');
        $data->jml_faktur = collect($dataTagihan)->count('value5');

        if ($data) {
            return response()->json($data);
        }
        else {
            return response()->json("null");
        }
    }

    public function AlocatePayment(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('idCustomer');
            $idGroup = $request->input('idGroup');
            $invoices = $request->input('invoices');
            $nominal = $request->input('nominal');

            if ($idCustomer != "" || $idGroup != "") {

                $dataTagihan = TempTransaction::select(
                                                'temp_transaction.id',
                                                'temp_transaction.value2',
                                                'temp_transaction.value3',
                                                'temp_transaction.value4',
                                                'temp_transaction.value5',
                                                'temp_transaction.value6',
                                                'temp_transaction.value7',
                                                'temp_transaction.value8',
                                                'temp_transaction.value9',
                                            )
                                            ->where([
                                                ['temp_transaction.module', '=', 'account_receiveable']
                                            ])
                                            ->when($idCustomer != "", function($q) use ($idCustomer) {
                                                $q->where('temp_transaction.value1', $idCustomer);
                                            })
                                            ->when($idGroup != "", function($q) use ($idGroup) {
                                                $q->whereIn('temp_transaction.value1', function($query) use ($idGroup) {
                                                    $query->select('id_customer')->from('customer_group_detail')->where('id_group', '=', $idGroup);
                                                });
                                            })
                                            ->whereIn('value2', $invoices)
                                            ->orderBy('value3', 'asc')
                                            ->get();

                if ($dataTagihan != "") {
                    $payment = $nominal;
                    foreach ($dataTagihan as $detail) {
                        if ($nominal != 0) {
                            $tagihan = TempTransaction::find($detail->id);
                            if($payment - $detail->value8 >= 0) {
                                $tagihan->value9 = $detail->value8;
                                $payment = $payment - $detail->value8;
                            }
                            else {
                                $tagihan->value9 = $payment;
                                $payment = $payment - $payment;
                            }
                            $tagihan->save();
                        }
                    }
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

    public function StoreAccountReceiveableMass(Request $request)
    {
        $idCust = $request->input('idCustomer');
        $rekening = $request->input('Rekening');
        $jenisPembayaran = $request->input('JenisPembayaran');
        $tanggalBayar = $request->input('Tanggal');
        $tanggalJTGiro = $request->input('TanggalJTGiro');
        $idSupp = $request->input('idCustomer');
        $rekening = $request->input('Rekening');
        $jenisPembayaran = $request->input('JenisPembayaran');
        $tanggalBayar = $request->input('Tanggal');
        $keterangan = $request->input('Keterangan');
        $nominal = $request->input('Nominal');
        $idInvoices = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $nominal = str_replace(",", ".", $nominal);

        $blnPeriode = date("m", strtotime($tanggalBayar));
        $thnPeriode = date("Y", strtotime($tanggalBayar));

        $countKode = DB::table('account_receiveable')
                        ->select(DB::raw("MAX(RIGHT(kode_ar,2)) AS angka"))
                        // ->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tanggalBayar)
                        ->first();

        $count = $countKode->angka;
        $counter = $count + 1;

        $kodeTgl = Carbon::parse($tanggalBayar)->format('ymd');

        if ($counter < 10) {
            $kodeAr = "ar-cv-".$kodeTgl."0".$counter;
        }
        else {
            $kodeAr = "ar-cv-".$kodeTgl.$counter;
        }

        $dataTagihan = TempTransaction::select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            'temp_transaction.value9',
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $idCust],
                                            ['temp_transaction.value1', '=', $idSupp],
                                            ['temp_transaction.module', '=', 'account_receiveable']
                                        ])
                                        ->whereIn('value2', $idInvoices)
                                        ->orderBy('value3', 'asc')
                                        ->get();

        $cekPembayaran = collect($dataTagihan)->sum('value9');
        $totalTagihan = collect($dataTagihan)->sum('value8');

        if ($cekPembayaran == 0) {
            return response()->json("failAlocate");
        }

        if ($nominal > $totalTagihan) {
            return response()->json("failOverPayment");
        }

        $AccountReceiveable = new AccountReceiveable();
        $AccountReceiveable->kode_ar = $kodeAr;
        $AccountReceiveable->id_customer = $idCust;
        $AccountReceiveable->rekening_pembayaran = $rekening;
        $AccountReceiveable->jenis_pembayaran = $jenisPembayaran;
        $AccountReceiveable->keterangan = $keterangan;
        $AccountReceiveable->nominal = $nominal;
        $AccountReceiveable->tanggal = $tanggalBayar;

        if ($jenisPembayaran == "giro") {
            $AccountReceiveable->tanggal_jt_giro = $tanggalJTGiro;
        }
        $AccountReceiveable->flag_revisi = 0;
        $AccountReceiveable->flag_potongan = 0;
        $AccountReceiveable->nominal_potongan = 0;
        $AccountReceiveable->status = 'posted';
        $AccountReceiveable->created_by = $user;
        $AccountReceiveable->save();

        if ($AccountReceiveable) {

            if ($dataTagihan != "") {
                foreach ($dataTagihan as $detail) {

                    $ARDetail = new AccountReceiveableDetail();
                    $ARDetail->id_ar = $AccountReceiveable->id;
                    $ARDetail->id_invoice = $detail->value2;
                    $ARDetail->nominal_bayar = $detail->value9;
                    $ARDetail->nominal_sisa = $detail->value8 - $detail->value9;
                    $ARDetail->created_by = $user;
                    $ARDetail->save();

                    $salesInvoice = SalesInvoice::find($detail->value2);
                    if ($detail->value8 - $detail->value9 == 0) {
                        $salesInvoice->flag_pembayaran = 1;
                    }
                    else {
                        $salesInvoice->flag_pembayaran = 2;
                    }
                    $salesInvoice->save();
                    HelperAccounting::PaymentARBalance($salesInvoice->id, $detail->value9);
                    HelperAccounting::RemoveARBalance($salesInvoice->id, 'payment');

                    if ($rekening != "") {
                        $akun = 2;
                        $akunRekening = CompanyAccount::find($rekening);
                        $settings = GLAccountSettings::find(1);
                        $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                        $customer = Customer::find($idCust);
                        $akunCustomer = $customer->id_account ?? 0;
                        $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                        $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                        if ($idAkunRekening != 0 && $idAkunPiutang != 0) {
                            $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $detail->value9, $AccountReceiveable->id);
                            $postJournal = HelperAccounting::PostJournal("bank_masuk", $AccountReceiveable->id, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $detail->value9, 'system');
                        }
                    }
                    else {
                        $akun = 1;
                        $settings = GLAccountSettings::find(1);
                        $idAkunKas = $settings->id_account_kas ?? 0;
                        $customer = Customer::find($idCust);
                        $akunCustomer = $customer->id_account ?? 0;
                        $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                        $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                        if ($idAkunKas != 0 && $idAkunPiutang != 0) {
                            $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunKas, $idAkunPiutang, $tanggalBayar, $detail->value9, $AccountReceiveable->id);
                            $postJournal = HelperAccounting::PostJournal("kas_masuk", $AccountReceiveable->id, $idAkunKas, $idAkunPiutang, $tanggalBayar, $detail->value9, 'system');
                        }

                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['temp_transaction.value1', '=', $idCust],
                                    ['temp_transaction.value1', '=', $idSupp],
                                    ['temp_transaction.module', '=', 'account_receiveable']
                                ])
                                ->whereIn('value2', $idInvoices)
                                ->delete();

        }

        $log = ActionLog::create([
            'module' => 'Account Receiveable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Receiveable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function StoreAccountReceiveableMassGroup(Request $request)
    {
        $idGroup = $request->input('idGroup');
        $rekening = $request->input('Rekening');
        $jenisPembayaran = $request->input('JenisPembayaran');
        $tanggalBayar = $request->input('Tanggal');
        $tanggalJTGiro = $request->input('TanggalJTGiro');
        $keterangan = $request->input('Keterangan');
        $nominal = $request->input('Nominal');
        $idInvoices = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $nominal = str_replace(",", ".", $nominal);

        $blnPeriode = date("m", strtotime($tanggalBayar));
        $thnPeriode = date("Y", strtotime($tanggalBayar));

        $dataTagihan = TempTransaction::select(
                                            'temp_transaction.id',
                                            'temp_transaction.value1',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            'temp_transaction.value9',
                                        )
                                        ->where([
                                            ['temp_transaction.module', '=', 'account_receiveable']
                                        ])
                                        ->whereIn('value2', $idInvoices)
                                        ->orderBy('value3', 'asc')
                                        ->get();

        foreach ($dataTagihan as $dataPayment) {

            $countKode = DB::table('account_receiveable')
                        ->select(DB::raw("MAX(RIGHT(kode_ar,2)) AS angka"))
                        // ->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tanggalBayar)
                        ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tanggalBayar)->format('ymd');

            if ($counter < 10) {
                $kodeAr = "ar-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeAr = "ar-cv-".$kodeTgl.$counter;
            }

            $AccountReceiveable = new AccountReceiveable();
            $AccountReceiveable->kode_ar = $kodeAr;
            $AccountReceiveable->id_customer = $dataPayment->value1;
            $AccountReceiveable->rekening_pembayaran = $rekening;
            $AccountReceiveable->jenis_pembayaran = $jenisPembayaran;
            $AccountReceiveable->keterangan = $keterangan;
            $AccountReceiveable->nominal = $dataPayment->value9;
            $AccountReceiveable->tanggal = $tanggalBayar;
            if ($jenisPembayaran == "giro") {
                $AccountReceiveable->tanggal_jt_giro = $tanggalJTGiro;
            }
            $AccountReceiveable->flag_revisi = 0;
            $AccountReceiveable->flag_potongan = 0;
            $AccountReceiveable->nominal_potongan = 0;
            $AccountReceiveable->status = 'posted';
            $AccountReceiveable->created_by = $user;
            $AccountReceiveable->save();

            $ARDetail = new AccountReceiveableDetail();
            $ARDetail->id_ar = $AccountReceiveable->id;
            $ARDetail->id_invoice = $dataPayment->value2;
            $ARDetail->nominal_bayar = $dataPayment->value9;
            $ARDetail->nominal_sisa = $dataPayment->value8 - $dataPayment->value9;
            $ARDetail->created_by = $user;
            $ARDetail->save();

            $salesInvoice = SalesInvoice::find($dataPayment->value2);
            if ($dataPayment->value8 - $dataPayment->value9 == 0) {
                $salesInvoice->flag_pembayaran = 1;
            }
            else {
                $salesInvoice->flag_pembayaran = 2;
            }
            $salesInvoice->save();
            HelperAccounting::PaymentARBalance($salesInvoice->id, $dataPayment->value9);
            HelperAccounting::RemoveARBalance($salesInvoice->id, 'payment');

            if ($rekening != "") {
                $akun = 2;
                $akunRekening = CompanyAccount::find($rekening);
                $settings = GLAccountSettings::find(1);
                $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                $customer = Customer::find($dataPayment->value1);
                $akunCustomer = $customer->id_account ?? 0;
                $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $dataPayment->value9, $AccountReceiveable->id);
                $postJournal = HelperAccounting::PostJournal("bank_masuk", $AccountReceiveable->id, $idAkunRekening, $idAkunPiutang, $tanggalBayar, $dataPayment->value9, 'system');
            }
            else {
                $akun = 1;
                $settings = GLAccountSettings::find(1);
                $idAkunKas = $settings->id_account_kas ?? 0;
                $customer = Customer::find($dataPayment->value1);
                $akunCustomer = $customer->id_account ?? 0;
                $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
                $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("piutang", $akun, $idAkunKas, $idAkunPiutang, $tanggalBayar, $dataPayment->value9, $AccountReceiveable->id);
                $postJournal = HelperAccounting::PostJournal("kas_masuk", $AccountReceiveable->id, $idAkunKas, $idAkunPiutang, $tanggalBayar, $dataPayment->value9, 'system');
            }

        }

        $deleteTemp = DB::table('temp_transaction')
                        ->where([
                            ['temp_transaction.module', '=', 'account_receiveable']
                        ])
                        ->whereIn('value2', $idInvoices)
                        ->delete();

        $log = ActionLog::create([
            'module' => 'Account Receiveable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Receiveable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function CancelPayment(Request $request)
    {
        $idAr = $request->input('idAr');
        $idInv = $request->input('idInv');
        $user = Auth::user()->user_name;

        $dataInv = SalesInvoice::find($idInv);

        $dataAr = AccountReceiveable::find($idAr);

        $detailAr = AccountReceiveableDetail::where([
            ['id_ar', '=', $idAr],
            ['id_invoice', '=', $idInv]
        ])
        ->first();

        if ($dataAr == null && $detailAr == null ) {
            return response()->json("failNotFound");
        }

        if ($detailAr->nominal_bayar == $dataAr->nominal) {
            $dataAr->deleted_by = $user;
            $dataAr->save();
            $dataAr->delete();

            $detail = AccountReceiveableDetail::find($detailAr->id);
            $detail->deleted_by = $user;
            $detail->save();
            $detail->delete();
        }
        else {
            $nominalUpdated = $dataAr->nominal - $detailAr->nominal_bayar;

            $dataAr->nominal = $nominalUpdated;
            $dataAr->save();

            $detail = AccountReceiveableDetail::find($detailAr->id);
            $detail->deleted_by = $user;
            $detail->save();

            $detail->delete();
        }

        if ($detailAr->nominal_bayar == $dataInv->nominal) {
            $dataInv->flag_pembayaran = 0;
            $dataInv->save();
        }
        else {
            $totalPembayaranInv = AccountReceiveableDetail::where([
                                                                ['id_invoice', '=', $idInv]
                                                            ])
                                                            ->sum('nominal_bayar');

            if ($totalPembayaranInv > 0) {
                $pembayaranTerakhir = AccountReceiveableDetail::where([
                                                                    ['id_invoice', '=', $idInv]
                                                                ])
                                                                ->orderBy('created_at', 'desc')
                                                                ->first();

                $pembayaranTerakhir->nominal_sisa = $dataInv->grand_total - $totalPembayaranInv;
                $pembayaranTerakhir->save();
                $dataInv->flag_pembayaran = 2;
            }
            else {
                $dataInv->flag_pembayaran = 0;
            }
            $dataInv->save();
        }

        HelperAccounting::InsertARBalance($idInv, 'cancel_payment');

        $log = ActionLog::create([
            'module' => 'Account Receiveable',
            'action' => 'Batal',
            'desc' => 'Batal Pembayaran Account Receiveable',
            'username' => $user
        ]);

        return response()->json("success");
    }
}
