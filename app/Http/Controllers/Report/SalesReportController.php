<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportSalesExport;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Library\Customer;
use App\Models\Library\CustomerGroup;
use App\Models\Setting\Module;

class SalesReportController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportSales'],
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
                                                ['module.url', '=', '/ReportSales'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataCustomer = Customer::distinct()->orderBy('nama_customer', 'asc')->get();
                $dataGroup = CustomerGroup::distinct()->orderBy('nama_group', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataGroup'] = $dataGroup;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Report',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Report',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportSales', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataSalesReport(Request $request)
    {
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $customer = $request->input('customer');
        $jenis = $request->input('jenis');
        $grup = $request->input('grup');

        $transaction = "";

        if ($jenisPeriode != null) {
            $totalPembayaran = AccountReceiveableDetail::leftJoin('account_receiveable', 'account_receiveable_detail.id_ar', '=', 'account_receiveable.id')
                                                        ->select(
                                                            'account_receiveable_detail.id_invoice',
                                                            'account_receiveable.tanggal',
                                                            DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumPembayaran")
                                                        )
                                                        ->groupBy('account_receiveable_detail.id_invoice');

            $transaction = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                            ->leftJoinSub($totalPembayaran, 'totalPembayaran', function($totalPembayaran) {
                                                $totalPembayaran->on('sales_invoice.id', '=', 'totalPembayaran.id_invoice');
                                            })
                                            ->select(
                                                'sales_invoice.*',
                                                'sales_order.no_so',
                                                'customer.nama_customer',
                                                'customer_detail.nama_outlet',
                                                'totalPembayaran.tanggal',
                                                DB::raw('COALESCE(totalPembayaran.sumPembayaran, 0) as sumPembayaran')
                                            )
                                            ->where([
                                                ['sales_invoice.status_invoice', '=', 'posted']
                                            ])
                                            ->when($jenis == "customer" && $customer != "", function($q) use ($customer) {
                                                $q->where('customer.id', '=', $customer);
                                            })
                                            ->when($jenis == "grup" && $grup != "", function($q) use ($grup) {
                                                $q->whereIn('customer.id', function($subQuery) use ($grup) {
                                                    $subQuery->select('id_customer')->from('customer_group_detail')
                                                    ->where('id_group', '=', $grup);
                                                });
                                            })
                                            ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                $q->whereBetween('sales_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                            })
                                            ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                            })
                                            ->orderBy('sales_invoice.id', 'asc')
                                            ->get();
        }

        return response()->json($transaction);
    }

    public function exportDataSalesReport(Request $request)
    {
        return Excel::download(new ReportSalesExport($request), 'ReportSales.xlsx');
    }
}
