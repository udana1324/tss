<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Module;
use App\Models\Setting\ModuleAccess;
use App\Models\Setting\User;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;

class ModuleAccessController extends Controller
{
    public function index() {
        if (Auth::check()) {

            if (Auth::user()->user_name == "sata" || Auth::user()->user_group == "admin") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataUser = User::all()->where('active', 'Y');
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportSalesDetail'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $data['dataUser'] = $dataUser;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Akses Menu',
                    'action' => 'Index',
                    'desc' => 'Tampil Halaman',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.setting.moduleAccess.index');
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    //Ajax Call
    public function getUsers(Request $request)
    {
        $grup = $request->input('user_group');

        $data = User::leftJoin('user_profile', 'user_profile.user_name', '=', 'users.user_name')
                        ->select('users.id', 'users.user_name')
                        ->where([
                            ['users.user_group', '=', $grup],
                            ['users.active', '=', 'Y']
                        ])
                        ->get();

        return response()->json($data);
    }

    public function getMenu(Request $request)
    {
        $idUser = $request->input('id_user');

        $data = ModuleAccess::join('module', 'module.id', '=', 'module_access.menu_id')
                           ->select('module.menu', 'module.id as idMenu', 'module_access.*')
                           ->when(Auth::user()->user_group != "super_admin", function($q) {
                                $q->where('module.url', '!=', '/Modules');
                            })
                           ->where([
                                ['module_access.user_id', '=', $idUser],
                                ['module.active', '=', 'Y'],
                                ['module.parent', '!=', 'main']
                           ])
                           ->get();

        return response()->json($data);
    }

    public function updateDataAksesMenu(Request $request)
    {
        $idUser = $request->input('id_user');
        $idMenu = $request->input('id_menu');
        $jenisAkses = $request->input('jenis_akses');
        $valueAkses = $request->input('value_akses');
        $user = Auth::user()->user_name;

        if($jenisAkses == "all") {
            $AksesMenu = DB::table('module_access')
                      ->where([
                                ['user_id', '=', $idUser],
                                ['menu_id', '=', $idMenu]
                              ])
                      ->update([
                                'add' => $valueAkses,
                                'edit' => $valueAkses,
                                'delete' => $valueAkses,
                                'export' => $valueAkses,
                                'print' => $valueAkses,
                                'approve' => $valueAkses,
                                'posting' => $valueAkses,
                                'revisi' => $valueAkses,
                                'action' => 'EDIT',
                                'updated_by' => $user,
                                'updated_at' => now(),
                              ]);
        }
        else {
            $AksesMenu = DB::table('module_access')
                      ->where([
                                ['user_id', '=', $idUser],
                                ['menu_id', '=', $idMenu]
                              ])
                      ->update([
                                $jenisAkses => $valueAkses,
                                'action' => 'EDIT',
                                'updated_by' => $user,
                                'updated_at' => now(),
                              ]);
        }


        $log = ActionLog::create([
                    'module' => 'Akses Menu',
                    'action' => 'Update',
                    'desc' => 'Update Akses Menu',
                    'username' => Auth::user()->user_name
                ]);

        return response()->json($AksesMenu);
    }

}
