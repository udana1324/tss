<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Exports\FinancialForecastExport;
use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportSalesExport;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Setting\Module;

class FinanceForecastController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/FinanceForecast'],
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
                                                ['module.url', '=', '/FinanceForecast'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataProduct = Product::distinct()->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Finance Forecast Report',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Finance Forecast Report',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.finance_forecast.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function exportDataFinanceReport(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new FinancialForecastExport($request), 'ForecastKeuangan_'.$kodeTgl.'.xlsx');
    }
}
