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
use App\Exports\ReportSalesCashierExport;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Library\Customer;
use App\Models\Library\CustomerGroup;
use App\Models\Sales\SalesCashier;
use App\Models\Setting\Module;

class SalesCashierReportController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportSalesCashier'],
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
                                                ['module.url', '=', '/ReportSalesCashier'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataCustomer = Customer::distinct()->orderBy('nama_customer', 'asc')->get();
                // $dataGroup = CustomerGroup::distinct()->orderBy('nama_group', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                // $data['dataGroup'] = $dataGroup;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Cashier Report',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Sales Cashier Report',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportSalesCashier', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataSalesCashierReport(Request $request)
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

        if ($jenisPeriode != null) {;

            $transaction = SalesCashier::leftJoin('customer', 'sales_cashier.id_customer', '=', 'customer.id')
                                        ->select(
                                            'sales_cashier.*',
                                            'customer.nama_customer'
                                        )
                                        ->where([
                                            ['sales_cashier.status_sales', '=', 'posted']
                                        ])
                                        ->when($jenis == "customer" && $customer != "", function($q) use ($customer) {
                                            $q->where('customer.id', '=', $customer);
                                        })
                                        // ->when($jenis == "grup" && $grup != "", function($q) use ($grup) {
                                        //     $q->whereIn('customer.id', function($subQuery) use ($grup) {
                                        //         $subQuery->select('id_customer')->from('customer_group_detail')
                                        //         ->where('id_group', '=', $grup);
                                        //     });
                                        // })
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('sales_cashier.tanggal_penjualan', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('sales_cashier.tanggal_penjualan', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('sales_cashier.tanggal_penjualan', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('sales_cashier.tanggal_penjualan', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->orderBy('sales_cashier.id', 'asc')
                                        ->get();
        }

        return response()->json($transaction);
    }

    public function exportDataSalesCashierReport(Request $request)
    {
        return Excel::download(new ReportSalesCashierExport($request), 'ReportSalesCashier.xlsx');
    }
}
