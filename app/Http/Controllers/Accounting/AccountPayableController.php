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
use App\Models\Accounting\AccountPayable;
use App\Models\Accounting\AccountPayableBalance;
use App\Models\Accounting\AccountPayableDetail;
use App\Models\Accounting\AccountPayableCost;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Library\CompanyAccount;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\PurchaseInvoiceTerms;
use App\Models\Setting\Module;
use App\Models\TempTransaction;
use Illuminate\Support\Carbon;
use stdClass;

class AccountPayableController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/AccountPayable'],
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
                                                ['module.url', '=', '/AccountPayable'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('purchase_order_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Payable',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Account Payable',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_payable.index', $data);
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
        // $jumlahTransaksi = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                     ->select(
        //                                         'purchase_order.id_supplier',
        //                                         DB::raw("COUNT(purchase_invoice.kode_invoice) AS countTransaksi"),
        //                                     )
        //                                     ->where([
        //                                                 ['purchase_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('purchase_order.id_supplier');

        // $totalTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                 ->select(
        //                                             'purchase_order.id_supplier',
        //                                             // DB::raw("COUNT(purchase_invoice.kode_invoice) AS countTotal"),
        //                                             DB::raw("SUM(CASE
        //                                                         WHEN purchase_invoice.flag_pembayaran = 1
        //                                                             THEN 0
        //                                                         ELSE 1
        //                                                     END) AS countTotal"),
        //                                             DB::raw("SUM(purchase_invoice.grand_total) AS sumTotal")
        //                                         )
        //                                 ->where([
        //                                             ['purchase_invoice.status_invoice', '=', 'posted']
        //                                         ])
        //                                 ->groupBy('purchase_order.id_supplier');

        // $totalTagihanJT = PurchaseInvoice::join('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                 ->select(
        //                                     'purchase_order.id_supplier',
        //                                     DB::raw("COUNT(purchase_invoice.kode_invoice) AS countTotalDue"),
        //                                     DB::raw("SUM(purchase_invoice.grand_total) AS sumTotalDue")
        //                                 )
        //                                 ->where([
        //                                     ['purchase_invoice.flag_pembayaran', '=', '0'],
        //                                     ['purchase_invoice.tanggal_jt', '<=', Carbon::now()->format('Y-m-d')],
        //                                     ['purchase_invoice.status_invoice', '=', 'posted']
        //                                 ])
        //                                 ->groupBy('purchase_order.id_supplier');

        // $totalSisaTagihan = AccountPayable::leftJoin('account_payable_detail', 'account_payable.id', '=', 'account_payable_detail.id_ap')
        //                                     ->leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
        //                                     ->select(
        //                                             'account_payable.id_supplier',
        //                                             DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
        //                                         )
        //                                     ->where([
        //                                         ['purchase_invoice.flag_pembayaran', '!=', '0'],
        //                                         ['purchase_invoice.status_invoice', '=', 'posted']
        //                                     ])
        //                                     ->groupBy('account_payable.id_supplier');

        // $dataAp = Supplier::leftJoinSub($totalTagihan, 'totalTagihan', function($totalTagihan) {
        //                         $totalTagihan->on('supplier.id', '=', 'totalTagihan.id_supplier');
        //                     })
        //                     ->leftJoinSub($totalTagihanJT, 'totalTagihanJT', function($totalTagihanJT) {
        //                         $totalTagihanJT->on('supplier.id', '=', 'totalTagihanJT.id_supplier');
        //                     })
        //                     ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                         $totalSisaTagihan->on('supplier.id', '=', 'totalSisaTagihan.id_supplier');
        //                     })
        //                     ->leftJoinSub($jumlahTransaksi, 'jumlahTransaksi', function($jumlahTransaksi) {
        //                         $jumlahTransaksi->on('supplier.id', '=', 'jumlahTransaksi.id_supplier');
        //                     })
        //                     ->select(
        //                         'supplier.id',
        //                         'supplier.kode_supplier',
        //                         'supplier.nama_supplier',
        //                         DB::raw("COALESCE(jumlahTransaksi.countTransaksi, 0) AS countTransaksi"),
        //                         DB::raw("COALESCE(totalTagihan.countTotal, 0) AS countTotal"),
        //                         DB::raw("COALESCE(totalTagihanJT.countTotalDue, 0) AS countTotalDue"),
        //                         DB::raw("(COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sumTotal"),
        //                         DB::raw("COALESCE(totalTagihanJT.sumTotalDue, 0) AS sumTotalDue")
        //                     )
        //                     ->where([
        //                             ['jumlahTransaksi.countTransaksi', '>', '0']
        //                     ])
        //                     ->orderBy('supplier.id', 'asc')
        //                     ->get();

        $dataAp = Supplier::leftJoin('account_payable_balance', 'account_payable_balance.id_supplier', 'supplier.id')
                                                ->select(
                                                    'supplier.id',
                                                    'supplier.nama_supplier',
                                                    'supplier.kode_supplier',
                                                    DB::raw("
                                                        COALESCE(SUM(account_payable_balance.nominal_outstanding), 0) as 'TotalTagihan',
                                                        SUM(CASE WHEN account_payable_balance.tanggal_jt < NOW() THEN account_payable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                                        SUM(CASE WHEN account_payable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                                        COUNT(account_payable_balance.id_invoice) AS 'TotalInvoice'
                                                    ")
                                                )
                                                ->where([
                                                    ['supplier.deleted_at', '=', null]
                                                ])
                                                ->groupBy('supplier.id')
                                                ->orderByRaw('Totaltagihan desc')
                                                ->orderByRaw('TotalInvoice desc')
                                                ->orderByRaw('TotalInvoiceJT desc')
                                                ->get();

        return response()->json($dataAp);
    }

    public function getDataTagihan(Request $request)
    {

        $idSupplier = $request->input('idSupplier');

        // $totalSisaTagihan = AccountPayableDetail::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
        //                                             ->select(
        //                                                         'purchase_invoice.id',
        //                                                         DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
        //                                                     )
        //                                             // ->where([
        //                                             //             ['purchase_invoice.flag_pembayaran', '!=', '1']
        //                                             //         ])
        //                                             ->groupBy('account_payable_detail.id_invoice');

        $totalPotonganTagihan = AccountPayableCost::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_cost.id_invoice')
                                                    ->select(
                                                                'purchase_invoice.id',
                                                                DB::raw("SUM(account_payable_cost.nominal) AS sumPotongan")
                                                            )
                                                    // ->where([
                                                    //             ['purchase_invoice.flag_pembayaran', '!=', '1']
                                                    //         ])
                                                    ->groupBy('account_payable_cost.id_invoice');

        // $listSJ = PurchaseInvoiceDetail::leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
        //                                 ->select('purchase_invoice_detail.id_invoice',
        //                                      DB::raw("GROUP_CONCAT(receiving.no_sj_supplier SEPARATOR ',') as list_sjs")
        //                                 )
        //                                 ->groupBy('purchase_invoice_detail.id_invoice');


        // $dataTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                 //->leftJoin('account_payable', 'account_payable.id_supplier', 'purchase_order.id_supplier')
        //                                 //->leftJoin('account_payable_detail', 'purchase_invoice.id', 'account_payable_detail.id_invoice')
        //                                 ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                                     $totalSisaTagihan->on('purchase_invoice.id', '=', 'totalSisaTagihan.id');
        //                                 })
        //                                 ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
        //                                     $totalPotonganTagihan->on('purchase_invoice.id', '=', 'totalPotonganTagihan.id');
        //                                 })
        //                                 ->leftJoinSub($listSJ, 'listSJ', function($listSJ) {
        //                                     $listSJ->on('purchase_invoice.id', '=', 'listSJ.id_invoice');
        //                                 })
        //                                 ->select(
        //                                             'purchase_invoice.id',
        //                                             'purchase_invoice.kode_invoice',
        //                                             'purchase_invoice.tanggal_invoice',
        //                                             'purchase_invoice.tanggal_jt',
        //                                             'purchase_invoice.grand_total',
        //                                             'purchase_invoice.flag_pembayaran',
        //                                             DB::raw("COALESCE(listSJ.list_sjs, '-') AS list_sjs"),
        //                                             DB::raw("DATE_FORMAT(purchase_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
        //                                            // 'account_payable.flag_potongan',
        //                                            // DB::raw("COALESCE(account_payable.jenis_pembayaran, '-') AS jenis_pembayaran"),
        //                                             DB::raw("COALESCE(totalSisaTagihan.sumBayar, 0) AS nominal_bayar"),
        //                                             DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),
        //                                             DB::raw("(COALESCE(purchase_invoice.grand_total, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) - COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sisa_tagihan"),
        //                                         )
        //                                 ->orderBy('purchase_invoice.id', 'desc')
        //                                 ->where([
        //                                     ['purchase_order.id_supplier', '=', $idSupplier],
        //                                     ['purchase_invoice.status_invoice', '=', 'posted'],
        //                                     // ['purchase_invoice.flag_pembayaran', '!=', '1']
        //                                 ])
        //                                 ->get();

        $dataTagihan = AccountPayableBalance::leftJoin('purchase_invoice', 'account_payable_balance.id_invoice', 'purchase_invoice.id')
                                                ->leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                                    $totalPotonganTagihan->on('purchase_invoice.id', '=', 'totalPotonganTagihan.id');
                                                })
                                                ->select(
                                                    'purchase_invoice.id',
                                                    'purchase_invoice.kode_invoice',
                                                    'purchase_invoice.tanggal_invoice',
                                                    'purchase_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(account_payable_balance.nominal_invoice, 0) AS grand_total"),
                                                    'purchase_invoice.flag_pembayaran',
                                                    DB::raw("DATE_FORMAT(purchase_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                                                    DB::raw("COALESCE(account_payable_balance.nominal_invoice, 0) - COALESCE(account_payable_balance.nominal_outstanding, 0) AS nominal_bayar"),
                                                    DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),
                                                    DB::raw("COALESCE(account_payable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['account_payable_balance.id_supplier', '=', $idSupplier]
                                                ])
                                                ->orderByRaw('purchase_invoice.tanggal_invoice desc')
                                                ->orderByRaw('purchase_invoice.id desc')
                                                ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanLunas(Request $request)
    {

        $idSupplier = $request->input('idSupplier');

        $totalPotonganTagihan = AccountPayableCost::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_cost.id_invoice')
                                                    ->select(
                                                                'purchase_invoice.id',
                                                                DB::raw("SUM(account_payable_cost.nominal) AS sumPotongan")
                                                            )
                                                    ->where([
                                                    ])
                                                    ->groupBy('account_payable_cost.id_invoice');


        $dataTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                        $totalPotonganTagihan->on('purchase_invoice.id', '=', 'totalPotonganTagihan.id');
                                    })
                                    ->select(
                                        'purchase_invoice.id',
                                        'purchase_invoice.kode_invoice',
                                        'purchase_invoice.tanggal_invoice',
                                        'purchase_invoice.tanggal_jt',
                                        'purchase_invoice.grand_total',
                                        'purchase_invoice.flag_pembayaran',
                                        DB::raw("DATE_FORMAT(purchase_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice")
                                    )
                                    ->where([
                                        ['purchase_order.id_supplier', '=', $idSupplier],
                                        ['purchase_invoice.flag_pembayaran', '=', 1]
                                    ])
                                    ->orderBy('purchase_invoice.id', 'desc')
                                    ->get();

        return response()->json($dataTagihan);
    }

    public function getDataTagihanSupplier(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        // $totalTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                 ->select(
        //                                             'purchase_order.id_supplier',
        //                                             DB::raw("COUNT(purchase_invoice.kode_invoice) AS countTotal"),
        //                                             DB::raw("SUM(purchase_invoice.grand_total) AS sumTotal")
        //                                         )
        //                                 ->where([
        //                                         ['purchase_invoice.status_invoice', '=', 'posted']
        //                                     ])
        //                                 ->groupBy('purchase_order.id_supplier');

        // $totalTagihanJT = PurchaseInvoice::join('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                                 ->select(
        //                                             'purchase_order.id_supplier',
        //                                             DB::raw("COUNT(purchase_invoice.kode_invoice) AS countTotalDue"),
        //                                             DB::raw("SUM(purchase_invoice.grand_total) AS sumTotalDue")
        //                                         )
        //                                 ->where([
        //                                             ['purchase_invoice.status_invoice', '=', 'posted'],
        //                                             ['purchase_invoice.flag_pembayaran', '=', '0'],
        //                                             ['purchase_invoice.tanggal_jt', '<=', Carbon::now()->format('Y-m-d')]
        //                                         ])
        //                                 ->groupBy('purchase_order.id_supplier');

        // $totalSisaTagihan = AccountPayable::leftJoin('account_payable_detail', 'account_payable.id', '=', 'account_payable_detail.id_ap')
        //                                     ->leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
        //                                     ->select(
        //                                                 'account_payable.id_supplier',
        //                                                 DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
        //                                             )
        //                                     ->where([
        //                                                 ['purchase_invoice.flag_pembayaran', '!=', '0']
        //                                             ])
        //                                     ->groupBy('account_payable.id_supplier');

        // $dataAp = Supplier::leftJoinSub($totalTagihan, 'totalTagihan', function($totalTagihan) {
        //                     $totalTagihan->on('supplier.id', '=', 'totalTagihan.id_supplier');
        //                 })
        //                 ->leftJoinSub($totalTagihanJT, 'totalTagihanJT', function($totalTagihanJT) {
        //                     $totalTagihanJT->on('supplier.id', '=', 'totalTagihanJT.id_supplier');
        //                 })
        //                 ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                     $totalSisaTagihan->on('supplier.id', '=', 'totalSisaTagihan.id_supplier');
        //                 })
        //                 ->select(
        //                         'supplier.id',
        //                         'supplier.kode_supplier',
        //                         'supplier.nama_supplier',
        //                         DB::raw("COALESCE(totalTagihan.countTotal, 0) AS countTotal"),
        //                         DB::raw("COALESCE(totalTagihanJT.countTotalDue, 0) AS countTotalDue"),
        //                         DB::raw("(COALESCE(totalTagihan.sumTotal, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sumTotal"),
        //                         DB::raw("COALESCE(totalTagihanJT.sumTotalDue, 0) AS sumTotalDue"))
        //                 ->where([
        //                     ['supplier.id', '=', $idSupplier],
        //                     ['totalTagihan.countTotal', '>', '0']
        //                 ])
        //                 ->orderBy('supplier.id', 'asc')
        //                 ->first();

        $dataAp = AccountPayableBalance::leftJoin('supplier', 'account_payable_balance.id_supplier', 'supplier.id')
                                                ->select(
                                                    DB::raw("
                                                        SUM(account_payable_balance.nominal_outstanding) as 'TotalTagihan',
                                                        SUM(CASE WHEN account_payable_balance.tanggal_jt < NOW() THEN account_payable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                                        SUM(CASE WHEN account_payable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                                        COUNT(account_payable_balance.id_invoice) AS 'TotalInvoice'
                                                    ")
                                                )
                                                ->where([
                                                    ['supplier.id', '=', $idSupplier]
                                                ])
                                                ->groupBy('account_payable_balance.id_supplier')
                                                ->first();

        return response()->json($dataAp);
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/AccountPayable'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::find($id);

                $dataAlamat = SupplierDetail::where([
                                                ['id_supplier', '=', $id],
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
                $data['dataSupplier'] = $dataSupplier;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataRekening'] = $dataRekening;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Payable',
                    'action' => 'Detail',
                    'desc' => 'Detail Account Payable',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.account_payable.detail', $data);
            }
            else {
                return redirect('/PurchaseInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getInvoiceData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        // $totalSisaTagihan = AccountPayableDetail::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
        //                                             ->select(
        //                                                         'purchase_invoice.id',
        //                                                         DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
        //                                                     )
        //                                             ->where([
        //                                                         ['purchase_invoice.flag_pembayaran', '!=', '1'],
        //                                                         ['account_payable_detail.id_invoice', '=', $idInvoice],
        //                                                     ])
        //                                             ->groupBy('account_payable_detail.id_invoice');


        // $dataTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                             ->leftJoin('account_payable', 'account_payable.id_supplier', 'purchase_order.id_supplier')
        //                             ->leftJoin('account_payable_detail', 'purchase_invoice.id', 'account_payable_detail.id_invoice')
        //                             ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
        //                                 $totalSisaTagihan->on('purchase_invoice.id', '=', 'totalSisaTagihan.id');
        //                             })
        //                             ->select(
        //                                         'purchase_invoice.id',
        //                                         'purchase_invoice.kode_invoice',
        //                                         'purchase_invoice.tanggal_invoice',
        //                                         'purchase_invoice.tanggal_jt',
        //                                         'purchase_invoice.grand_total',
        //                                         'purchase_invoice.flag_pembayaran',
        //                                         DB::raw("COALESCE(account_payable.jenis_pembayaran, '-') AS jenis_pembayaran"),
        //                                         DB::raw("COALESCE(account_payable_detail.nominal_bayar, 0) AS nominal_bayar"),
        //                                         DB::raw("(COALESCE(purchase_invoice.grand_total, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) AS sisa_tagihan"),
        //                                     )
        //                             ->orderBy('purchase_invoice.id', 'desc')
        //                             ->where([
        //                                 ['purchase_invoice.id', '=', $idInvoice],
        //                                 ['purchase_invoice.flag_pembayaran', '!=', '1']
        //                             ])
        //                             ->get();

        $dataTagihan = AccountPayableBalance::leftJoin('purchase_invoice', 'account_payable_balance.id_invoice', 'purchase_invoice.id')
                                                ->select(
                                                    'purchase_invoice.id',
                                                    'purchase_invoice.kode_invoice',
                                                    'purchase_invoice.tanggal_invoice',
                                                    'purchase_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(account_payable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['account_payable_balance.id_invoice', '=', $idInvoice]
                                                ])
                                                ->first();

        return response()->json($dataTagihan);
    }

    public function getCostData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $totalPotonganTagihan = AccountPayableCost::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_cost.id_invoice')
                                                        ->select(
                                                                    'purchase_invoice.id',
                                                                    DB::raw("SUM(account_payable_cost.nominal) AS sumPotongan")
                                                                )
                                                        ->where([
                                                                    ['purchase_invoice.id', '=', $idInvoice]
                                                                ])
                                                        ->groupBy('account_payable_cost.id_invoice');


        $dataTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('account_payable_detail', 'account_payable_detail.id_invoice', 'purchase_invoice.id')
                                    ->leftJoin('account_payable', 'account_payable_detail.id_ap', 'account_payable.id')
                                    ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                                        $totalPotonganTagihan->on('purchase_invoice.id', '=', 'totalPotonganTagihan.id');
                                    })
                                    ->select(
                                        'purchase_invoice.id',
                                        'purchase_invoice.kode_invoice',
                                        'purchase_invoice.tanggal_invoice',
                                        'purchase_invoice.tanggal_jt',
                                        DB::raw("(COALESCE(account_payable.nominal_potongan, 0) - COALESCE(totalPotonganTagihan.sumPotongan,0)) AS sisa_potongan"),
                                    )
                                    ->where([
                                        ['account_payable_detail.id_invoice', '=', $idInvoice],
                                        // ['purchase_invoice.flag_pembayaran', '!=', '1'],
                                        ['account_payable.deleted_at', '=', null]
                                    ])
                                    ->get();



        return response()->json($dataTagihan);
    }

    public function getCostList(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $dataPotongan = AccountPayableCost::where([
                                                    ['id_invoice', '=', $idInvoice]
                                                ])
                                                ->get();

        return response()->json($dataPotongan);
    }

    public function getPaymentData(Request $request)
    {

        $idInvoice = $request->input('idInvoice');

        $dataPotongan = AccountPayableDetail::leftJoin('account_payable', 'account_payable_detail.id_ap', 'account_payable.id')
                                            ->leftJoin('company_account', 'account_payable.rekening_pembayaran', '=', 'company_account.id')
                                            ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                            ->select(
                                                'account_payable.id',
                                                'account_payable.kode_ap',
                                                'account_payable.jenis_pembayaran',
                                                'account_payable.tanggal',
                                                'account_payable_detail.nominal_bayar',
                                                DB::raw("COALESCE(account_payable.keterangan, '-') AS keterangan"),
                                                DB::raw("CASE WHEN account_payable.rekening_pembayaran is null then ' - '
                                                                   ELSE CONCAT(bank.nama_bank, ' - ', company_account.nomor_rekening, ' A/N ', company_account.atas_nama)
                                                            END AS rekening")
                                            )
                                            ->where([
                                                ['id_invoice', '=', $idInvoice]
                                            ])
                                            ->get();

        return response()->json($dataPotongan);
    }

    public function StoreAccountPayable(Request $request)
    {
        $idSupp = $request->input('idSupplier');
        $rekening = $request->input('Rekening');
        $jenisPembayaran = $request->input('JenisPembayaran');
        $tanggalBayar = $request->input('Tanggal');
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

        $countKode = DB::table('account_payable')
                            ->select(DB::raw("MAX(RIGHT(kode_ap,2)) AS angka"))
                            ->whereDate('tanggal', $tanggalBayar)
                            ->first();
        $count = $countKode->angka;
        $counter = $count + 1;

        $kodeTgl = Carbon::parse($tanggalBayar)->format('ymd');
        $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tanggalBayar))));

        if ($counter < 10) {
            $kodeAp = "ap-cv-".$kodeTgl."0".$counter;
        }
        else {
            $kodeAp = "ap-cv-".$kodeTgl.$counter;
        }

        $AccountPayable = new AccountPayable();
        $AccountPayable->kode_ap = $kodeAp;
        $AccountPayable->id_supplier = $idSupp;
        $AccountPayable->rekening_pembayaran = $rekening;
        $AccountPayable->jenis_pembayaran = $jenisPembayaran;
        $AccountPayable->keterangan = $keterangan;
        $AccountPayable->nominal = $nominal;
        $AccountPayable->tanggal = $tanggalBayar;
        $AccountPayable->flag_revisi = 0;
        if ($potongan > 0) {
            $AccountPayable->flag_potongan = 1;
            $AccountPayable->nominal_potongan = $potongan;
        }
        else {
            $AccountPayable->flag_potongan = 0;
            $AccountPayable->nominal_potongan = 0;
        }
        $AccountPayable->status = 'posted';
        $AccountPayable->created_by = $user;
        $AccountPayable->save();

        if ($AccountPayable) {
            $ARDetail = new AccountPayableDetail();
            $ARDetail->id_ap = $AccountPayable->id;
            $ARDetail->id_invoice = $idInvoice;
            $ARDetail->nominal_bayar = $nominal;
            $ARDetail->nominal_sisa = $sisa;
            $ARDetail->created_by = $user;
            $ARDetail->save();

            $purchaseInvoice = PurchaseInvoice::find($idInvoice);
            if ($sisa == 0) {
                $purchaseInvoice->flag_pembayaran = 1;
            }
            else {
                $purchaseInvoice->flag_pembayaran = 2;
            }
            $purchaseInvoice->save();
            HelperAccounting::PaymentAPBalance($purchaseInvoice->id, $nominal);
            HelperAccounting::RemoveAPBalance($purchaseInvoice->id, 'payment');

            if ($rekening != "") {
                $akun = 2;
                $akunRekening = CompanyAccount::find($rekening);
                $settings = GLAccountSettings::find(1);
                $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                $supplier = Supplier::find($idSupp);
                $akunSupplier = $supplier->id_account ?? 0;
                $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
                $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("hutang", $akun, $idAkunRekening, $idAkunHutang, $tanggalBayar, $nominal, $AccountPayable->id);
                $postJournal = HelperAccounting::PostJournal("bank_keluar", $AccountPayable->id, $idAkunHutang, $idAkunRekening, $tanggalBayar, $nominal, 'system');
            }
            else {
                $akun = 1;
                $settings = GLAccountSettings::find(1);
                $idAkunKas = $settings->id_account_kas ?? 0;
                $supplier = Supplier::find($idSupp);
                $akunSupplier = $supplier->id_account ?? 0;
                $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
                $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

                $postGLKasBank = HelperAccounting::PostGLKasBank("hutang", $akun, $idAkunKas, $idAkunHutang, $tanggalBayar, $nominal, $AccountPayable->id);
                $postJournal = HelperAccounting::PostJournal("kas_keluar", $AccountPayable->id, $idAkunHutang, $idAkunKas, $tanggalBayar, $nominal, 'system');
            }
        }

        $log = ActionLog::create([
            'module' => 'Account Payable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Payable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function StoreAccountPayableCost(Request $request)
    {
        $keterangan = $request->input('keterangan');
        $potongan = $request->input('potongan');
        $idInvoice = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $potongan = str_replace(",", ".", $potongan);

        $AccountPayableCost = new AccountPayableCost();
        $AccountPayableCost->id_invoice = $idInvoice;
        $AccountPayableCost->nominal = $potongan;
        $AccountPayableCost->keterangan = $keterangan;
        $AccountPayableCost->created_by = $user;
        $AccountPayableCost->save();

        $log = ActionLog::create([
            'module' => 'Account Payable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Payable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function setDataTagihanMass(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idSupplier = $request->input('idSupplier');
            $invoices = $request->input('invoices');

            if ($idSupplier != "") {
                $deleteTemp = DB::table('temp_transaction')
                                    ->where([
                                        ['module', '=', 'account_payable'],
                                        ['value1', '=', $idSupplier],
                                    ])
                                    ->whereIn('value2', $invoices)
                                    ->delete();

                // $totalSisaTagihan = AccountPayableDetail::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
                //                                             ->select(
                //                                                         'purchase_invoice.id',
                //                                                         DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
                //                                                     )
                //                                             // ->where([
                //                                             //             ['purchase_invoice.flag_pembayaran', '!=', '1']
                //                                             //         ])
                //                                             ->groupBy('account_payable_detail.id_invoice');

                // $totalPotonganTagihan = AccountPayableCost::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_cost.id_invoice')
                //                                             ->select(
                //                                                         'purchase_invoice.id',
                //                                                         DB::raw("SUM(account_payable_cost.nominal) AS sumPotongan")
                //                                                     )
                //                                             // ->where([
                //                                             //             ['purchase_invoice.flag_pembayaran', '!=', '1']
                //                                             //         ])
                //                                             ->groupBy('account_payable_cost.id_invoice');


                // $dataTagihan = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                //                                 //->leftJoin('account_payable', 'account_payable.id_supplier', 'purchase_order.id_supplier')
                //                                 //->leftJoin('account_payable_detail', 'purchase_invoice.id', 'account_payable_detail.id_invoice')
                //                                 ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
                //                                     $totalSisaTagihan->on('purchase_invoice.id', '=', 'totalSisaTagihan.id');
                //                                 })
                //                                 ->leftJoinSub($totalPotonganTagihan, 'totalPotonganTagihan', function($totalPotonganTagihan) {
                //                                     $totalPotonganTagihan->on('purchase_invoice.id', '=', 'totalPotonganTagihan.id');
                //                                 })
                //                                 ->select(
                //                                             'purchase_invoice.id',
                //                                             'purchase_invoice.kode_invoice',
                //                                             'purchase_invoice.tanggal_invoice',
                //                                             'purchase_invoice.tanggal_jt',
                //                                             'purchase_invoice.grand_total',
                //                                             'purchase_invoice.flag_pembayaran',
                //                                             DB::raw("DATE_FORMAT(purchase_invoice.tanggal_invoice,'%Y-%m') AS periode_invoice"),
                //                                         // 'account_payable.flag_potongan',
                //                                         // DB::raw("COALESCE(account_payable.jenis_pembayaran, '-') AS jenis_pembayaran"),
                //                                             DB::raw("COALESCE(totalSisaTagihan.sumBayar, 0) AS nominal_bayar"),
                //                                             DB::raw("COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sumPotongan"),
                //                                             DB::raw("(COALESCE(purchase_invoice.grand_total, 0) - COALESCE(totalSisaTagihan.sumBayar,0)) - COALESCE(totalPotonganTagihan.sumPotongan, 0) AS sisa_tagihan"),
                //                                         )
                //                                 ->orderBy('purchase_invoice.id', 'asc')
                //                                 ->where([
                //                                     ['purchase_order.id_supplier', '=', $idSupplier],
                //                                     ['purchase_invoice.flag_pembayaran', '!=', '1']
                //                                 ])
                //                                 ->whereIn('purchase_invoice.id', $invoices)
                //                                 ->get();

                $dataTagihan = AccountPayableBalance::leftJoin('purchase_invoice', 'account_payable_balance.id_invoice', 'purchase_invoice.id')
                                                ->select(
                                                    'purchase_invoice.id',
                                                    'purchase_invoice.kode_invoice',
                                                    'purchase_invoice.tanggal_invoice',
                                                    'purchase_invoice.tanggal_jt',
                                                    DB::raw("COALESCE(account_payable_balance.nominal_invoice, 0) AS grand_total"),
                                                    DB::raw("COALESCE(account_payable_balance.nominal_invoice, 0) - COALESCE(account_payable_balance.nominal_outstanding, 0) AS nominal_bayar"),
                                                    DB::raw("COALESCE(account_payable_balance.nominal_outstanding, 0) AS sisa_tagihan")
                                                )
                                                ->where([
                                                    ['account_payable_balance.id_supplier', '=', $idSupplier]
                                                ])
                                                ->whereIn('purchase_invoice.id', $invoices)
                                                ->orderByRaw('purchase_invoice.tanggal_invoice desc')
                                                ->get();


                if ($dataTagihan != "") {
                    $listTemp = [];
                    foreach ($dataTagihan as $detail) {
                        $dataTemps = [
                            'module' => 'account_payable',
                            'value1' => $idSupplier,
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

        $idSupplier = $request->input('idSupplier');
        $invoices = $request->input('invoices');

        if ($idSupplier != "") {

            $dataTagihan = TempTransaction::leftJoin('delivery', 'temp_transaction.value1', '=', 'delivery.id')
                                            ->select(
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
                                                ['temp_transaction.value1', '=', $idSupplier],
                                                ['temp_transaction.module', '=', 'account_payable']
                                            ])
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
        $idSupplier = $request->input('idSupplier');

        $dataTagihan = TempTransaction::leftJoin('delivery', 'temp_transaction.value1', '=', 'delivery.id')
                                            ->select(
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
                                                ['temp_transaction.value1', '=', $idSupplier],
                                                ['temp_transaction.module', '=', 'account_payable']
                                            ])
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
            $idSupplier = $request->input('idSupplier');
            $invoices = $request->input('invoices');
            $nominal = $request->input('nominal');

            if ($idSupplier != "") {

                $dataTagihan = TempTransaction::leftJoin('delivery', 'temp_transaction.value1', '=', 'delivery.id')
                                            ->select(
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
                                                ['temp_transaction.value1', '=', $idSupplier],
                                                ['temp_transaction.module', '=', 'account_payable']
                                            ])
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

    public function StoreAccountPayableMass(Request $request)
    {
        $idSupp = $request->input('idSupplier');
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

        $countKode = DB::table('account_payable')
                        ->select(DB::raw("MAX(RIGHT(kode_ap,2)) AS angka"))
                        //->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tanggalBayar)
                        ->first();

        $count = $countKode->angka;
        $counter = $count + 1;

        $kodeTgl = Carbon::parse($tanggalBayar)->format('ymd');

        if ($counter < 10) {
            $kodeAp = "ap-cv-".$kodeTgl."0".$counter;
        }
        else {
            $kodeAp = "ap-cv-".$kodeTgl.$counter;
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
                                            ['temp_transaction.value1', '=', $idSupp],
                                            ['temp_transaction.module', '=', 'account_payable']
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

        $AccountPayable = new AccountPayable();
        $AccountPayable->kode_ap = $kodeAp;
        $AccountPayable->id_supplier = $idSupp;
        $AccountPayable->rekening_pembayaran = $rekening;
        $AccountPayable->jenis_pembayaran = $jenisPembayaran;
        $AccountPayable->keterangan = $keterangan;
        $AccountPayable->nominal = $nominal;
        $AccountPayable->tanggal = $tanggalBayar;
        $AccountPayable->flag_revisi = 0;
        $AccountPayable->flag_potongan = 0;
        $AccountPayable->nominal_potongan = 0;
        $AccountPayable->status = 'posted';
        $AccountPayable->created_by = $user;
        $AccountPayable->save();

        if ($AccountPayable) {

            if ($dataTagihan != "") {
                foreach ($dataTagihan as $detail) {

                    $ARDetail = new AccountPayableDetail();
                    $ARDetail->id_ap = $AccountPayable->id;
                    $ARDetail->id_invoice = $detail->value2;
                    $ARDetail->nominal_bayar = $detail->value9;
                    $ARDetail->nominal_sisa = $detail->value8 - $detail->value9;
                    $ARDetail->created_by = $user;
                    $ARDetail->save();

                    $purchaseInvoice = PurchaseInvoice::find($detail->value2);
                    if ($detail->value8 - $detail->value9 == 0) {
                        $purchaseInvoice->flag_pembayaran = 1;
                    }
                    else {
                        $purchaseInvoice->flag_pembayaran = 2;
                    }
                    $purchaseInvoice->save();
                    HelperAccounting::PaymentAPBalance($purchaseInvoice->id, $detail->value9);
                    HelperAccounting::RemoveAPBalance($purchaseInvoice->id, 'payment');

                    if ($rekening != "") {
                        $akun = 2;
                        $akunRekening = CompanyAccount::find($rekening);
                        $settings = GLAccountSettings::find(1);
                        $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                        $supplier = Supplier::find($idSupp);
                        $akunSupplier = $supplier->id_account ?? 0;
                        $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
                        $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

                        if ($idAkunRekening != 0 && $idAkunHutang != 0) {
                            $postGLKasBank = HelperAccounting::PostGLKasBank("hutang", $akun, $idAkunRekening, $idAkunHutang, $tanggalBayar, $detail->value9, $AccountPayable->id);
                            $postJournal = HelperAccounting::PostJournal("bank_keluar", $AccountPayable->id, $idAkunHutang, $idAkunRekening, $tanggalBayar, $detail->value9, 'system');
                        }
                    }
                    else {
                        $akun = 1;
                        $settings = GLAccountSettings::find(1);
                        $idAkunKas = $settings->id_account_kas ?? 0;
                        $supplier = Supplier::find($idSupp);
                        $akunSupplier = $supplier->id_account ?? 0;
                        $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
                        $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

                        if ($idAkunKas != 0 && $idAkunHutang != 0) {
                            $postGLKasBank = HelperAccounting::PostGLKasBank("hutang", $akun, $idAkunKas, $idAkunHutang, $tanggalBayar, $detail->value9, $AccountPayable->id);
                            $postJournal = HelperAccounting::PostJournal("kas_keluar", $AccountPayable->id, $idAkunHutang, $idAkunKas, $tanggalBayar, $detail->value9, 'system');
                        }
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['temp_transaction.value1', '=', $idSupp],
                                    ['temp_transaction.module', '=', 'account_payable']
                                ])
                                ->whereIn('value2', $idInvoices)
                                ->delete();

        }

        $log = ActionLog::create([
            'module' => 'Account Payable',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Payable',
            'username' => Auth::user()->user_name
        ]);

        return response()->json("success");
    }

    public function CancelPayment(Request $request)
    {
        $idAp = $request->input('idAp');
        $idInv = $request->input('idInv');
        $user = Auth::user()->user_name;

        $dataInv = PurchaseInvoice::find($idInv);

        $dataAp = AccountPayable::find($idAp);

        $detailAp = AccountPayableDetail::where([
            ['id_ap', '=', $idAp],
            ['id_invoice', '=', $idInv]
        ])
        ->first();

        if ($dataAp == null && $detailAp == null ) {
            return response()->json("failNotFound");
        }

        if ($detailAp->nominal_bayar == $dataAp->nominal) {
            $dataAp->deleted_by = $user;
            $dataAp->save();
            $dataAp->delete();

            $detail = AccountPayableDetail::find($detailAp->id);
            $detail->deleted_by = $user;
            $detail->save();
            $detail->delete();
        }
        else {
            $nominalUpdated = $dataAp->nominal - $detailAp->nominal_bayar;

            $dataAp->nominal = $nominalUpdated;
            $dataAp->save();

            $detail = AccountPayableDetail::find($detailAp->id);
            $detail->deleted_by = $user;
            $detail->save();

            $detail->delete();
        }

        if ($detailAp->nominal_bayar == $dataInv->nominal) {
            $dataInv->flag_pembayaran = 0;
            $dataInv->save();
        }
        else {
            $totalPembayaranInv = AccountPayableDetail::where([
                                                                ['id_invoice', '=', $idInv]
                                                            ])
                                                            ->sum('nominal_bayar');

            if ($totalPembayaranInv > 0) {
                $pembayaranTerakhir = AccountPayableDetail::where([
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
        HelperAccounting::InsertAPBalance($idInv, 'cancel_payment');

        $log = ActionLog::create([
            'module' => 'Account Payable',
            'action' => 'Batal',
            'desc' => 'Batal Pembayaran Account Payable',
            'username' => $user
        ]);

        return response()->json("success");
    }
}
