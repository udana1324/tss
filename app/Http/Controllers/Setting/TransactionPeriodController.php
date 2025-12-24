<?php

namespace App\Http\Controllers\Setting;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\TransactionPeriod;
use App\Models\Library\Bank;
use App\Models\Library\CompanyAccount;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class TransactionPeriodController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TransactionPeriod'],
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
                                                ['module.url', '=', '/TransactionPeriod'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Transaction Period',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Transaction Period',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.setting.transaction_period.index', $data);
            }
            else {
                return redirect('/Preference')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataPref = TransactionPeriod::all();
        return response()->json($dataPref);
    }

    public function updateDataAksesPeriode(Request $request)
    {
        $idMenu = $request->input('id_menu');
        $jenisAkses = $request->input('jenis_akses');
        $valueAkses = $request->input('value_akses');
        $user = Auth::user()->user_name;

        $AksesMenu = DB::table('transaction_period')
                      ->where([
                                ['id', '=', $idMenu]
                              ])
                      ->update([
                                $jenisAkses => $valueAkses,
                                'updated_by' => $user,
                                'updated_at' => now(),
                              ]);
        $log = ActionLog::create([
                    'module' => 'Transaction Period',
                    'action' => 'Update',
                    'desc' => 'Update Transaction Period',
                    'username' => Auth::user()->user_name
                ]);

        return response()->json($AksesMenu);
    }
}
