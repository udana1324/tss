<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\User;
use App\Models\Setting\UserProfile;
use App\Models\Setting\ModuleAccess;
use App\Models\Setting\Module;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;

class UsersController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Users'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $userGroup = Auth::user()->user_group;

            if ($countAkses > 0) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                if ($userGroup == "super_admin" || $userGroup == "admin") {

                    $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Users'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();


                    $dataMenu = Module::select('menu', 'id')
                                              ->where('active', 'Y')
                                              ->pluck('menu', 'id');

                    $dataUser = User::all()->where('active', 'Y');

                    $profile = UserProfile::all()->where('active', 'Y');

                    $log = ActionLog::create([
                        'module' => 'Users',
                        'action' => 'Index',
                        'desc' => 'Tampil Users',
                        'username' => Auth::user()->user_name
                    ]);

                    $data['hakAkses'] = $hakAkses;
                    $data['dataUser'] = $dataUser;
                    $data['profile'] = $profile;
                    $data['dataMenu'] = $dataMenu;
                    $parentMenu = Module::find($hakAkses->parent);

                    $data['parent'] = "parent".ucwords($parentMenu->menu);


                    return view('pages.setting.users.index', $data);
                }
                else {
                    $idProfile = DB::table('user_profile')
                                    ->select('id')
                                    ->where([
                                                ['user_name', '=', Auth::user()->user_name]
                                            ])
                                    ->first();

                    return redirect()->route('Users.Profile', ['id' => $idProfile->id]);
                }
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
        $profile = UserProfile::where([
                                ['active', '=', 'Y']
                            ])
                            ->get();

        return response()->json($profile);
    }

    public function create()
    {
        if (Auth::check()) {
            SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
            $user = Auth::user()->user_name;
            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Users'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ((Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") && $hakAkses->add == "Y") {

                $data = array();

                $parentMenu = DB::table('module')
                                ->select(
                                            'module.id',
                                            'module.menu',
                                            'module.parent',
                                        )
                                ->where([
                                            ['module.active', '=', 'Y']
                                ])
                                ->whereIn('module.parent', ['main', 'header'])
                                ->orderBy('module.order_number')
                                ->get();

                foreach ($parentMenu as $dataParent) {
                    $dataParentTemp = DB::table('module')
                                        ->select(
                                                    'module.id',
                                                    'module.menu',
                                                    'module.parent',
                                                )
                                        ->when(Auth::user()->user_group != "super_admin", function($q) {
                                            $q->where('module.url', '!=', '/Modules');
                                        })
                                        ->where([
                                                    ['module.parent', '=', $dataParent->id],
                                                    ['module.active', '=', 'Y']
                                        ])
                                        ->orderBy('module.order_number')
                                        ->get();
                    $dataParent->child = $dataParentTemp;
                }
                $log = ActionLog::create([
                    'module' => 'Users',
                    'action' => 'Create',
                    'desc' => 'Tambah Users Baru',
                    'username' => Auth::user()->user_name
                ]);
                $data['dataParent'] = $parentMenu;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);


                return view('pages.setting.users.add', $data);
            }
            else {
                return redirect('/Users')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {
            SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
            $user = Auth::user()->user_name;
            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Users'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if (($user == "sata" || Auth::user()->user_group == "admin") && $hakAkses->edit == "Y") {
                $data = array();

                $dataUsers = DB::table('user_profile')
                            ->join('users', 'user_profile.user_name', '=', 'users.user_name')
                            ->select(
                                    'user_profile.id',
                                    'user_profile.user_name',
                                    'user_profile.user_group',
                                    'user_profile.nama_user',
                                    'user_profile.telp_user',
                                    'user_profile.email_user',
                                    'users.active',
                                    DB::raw("users.id AS id_user"))
                            ->where('user_profile.id', '=' , $id)
                            ->first();
                $parentMenu = DB::table('module')
                            ->select(
                                        'module.id',
                                        'module.menu',
                                        'module.parent',
                                    )
                            ->where([
                                        ['module.active', '=', 'Y']
                                    ])
                            ->whereIn('module.parent', ['main', 'header'])
                            ->orderBy('module.order_number')
                            ->get();


                foreach ($parentMenu as $dataParent) {
                    $dataParentTemp = DB::table('module')
                                        ->select(
                                                    'module.id',
                                                    'module.menu',
                                                    'module.parent',
                                                )
                                        ->when(Auth::user()->user_group != "super_admin", function($q) {
                                            $q->where('module.url', '!=', '/Modules');
                                        })
                                        ->where([
                                                    ['module.parent', '=', $dataParent->id],
                                                    ['module.active', '=', 'Y']
                                                ])
                                        ->orderBy('module.order_number')
                                        ->get();
                    $dataParent->child = $dataParentTemp;
                }

                $hakAksesUser = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module_access.user_id', '=', $dataUsers->id_user]
                                            ])
                                    ->get();

                $log = ActionLog::create([
                        'module' => 'Users',
                        'action' => 'Edit',
                        'desc' => 'Edit Users',
                        'username' => Auth::user()->user_name
                    ]);

                $data['dataParent'] = $parentMenu;
                $data['dataUsers'] = $dataUsers;
                $data['hakAksesUser'] = $hakAksesUser;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.setting.users.edit', $data);
            }
            else {
                return redirect('/Users')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function display($id)
    {
        if (Auth::check()) {
            $user = Auth::user()->user_name;
            SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Users'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $data = array();

                $parentMenu = DB::table('module')
                                ->select(
                                            'module.id',
                                            'module.menu',
                                            'module.parent',
                                        )
                                ->where([
                                            ['module.parent', '=', 'main'],
                                            ['module.active', '=', 'Y']
                                        ])
                                ->orderBy('module.order_number')
                                ->get();
                $data['parent'] = $parentMenu;

                $dataUsers = DB::table('user_profile')
                            ->join('users', 'user_profile.user_name', '=', 'users.user_name')
                            ->select(
                                    'user_profile.id',
                                    'user_profile.user_name',
                                    'user_profile.user_group',
                                    'user_profile.nama_user',
                                    'user_profile.telp_user',
                                    'user_profile.email_user',
                                    'users.active',
                                    DB::raw("users.id AS id_user"))
                            ->where('user_profile.id', '=' , $id)
                            ->first();

                $hakAksesUser = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module_access.user_id', '=', $dataUsers->id_user]
                                            ])
                                    ->get();

                foreach ($parentMenu as $dataParent) {
                    $dataParentTemp = DB::table('module')
                                        ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                        ->select(
                                                    'module.id',
                                                    'module.menu',
                                                    'module.parent',
                                                )
                                        ->where([
                                                    ['module.parent', '=', $dataParent->id],
                                                    ['module.active', '=', 'Y'],
                                                    ['module_access.user_id', '=' , $dataUsers->id_user]
                                                ])
                                        ->orderBy('module.order_number')
                                        ->get();
                    $varParent = str_replace(" / ", "", trim($dataParent->menu));
                    $data[$varParent] = $dataParentTemp;
                }

                $log = ActionLog::create([
                        'module' => 'Users',
                        'action' => 'Display',
                        'desc' => 'Display Profile Users',
                        'username' => Auth::user()->user_name
                    ]);

                $data['dataUsers'] = $dataUsers;
                $data['hakAksesUser'] = $hakAksesUser;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);


                return view('pages.setting.users.display', $data);
            }
            else {
                return redirect('/Users')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
    }

    public function cekUsername(Request $request)
    {
        $userName = strtolower($request->input('username'));

        $user = DB::table('users')
                        ->select('user_name')
                        ->where([
                                 ['user_name', '=', $userName]
                                ])
                        ->get();


        return response()->json($user);
    }

    public function resetPassword(Request $request)
    {
        $id = $request->input('id');
        $dataProfile = UserProfile::find($id);
        $users = DB::table('users')
                      ->where('user_name', $dataProfile->user_name)
                      ->update([
                                'user_password' => Hash::make('123456'),
                                'action' => 'reset password',
                                'updated_by' => Auth::user()->user_name,
                              ]);
        $log = ActionLog::create([
                        'module' => 'Users',
                        'action' => 'Reset',
                        'desc' => 'Reset User Password',
                        'username' => Auth::user()->user_name
                    ]);


        return response()->json($users);
    }

    public function store(Request $request)
    {
        $metode = $request->input('metode');
        if ($metode == "store") {
            $request->validate([
              'username'=>'required',
              'usergroup'=> 'required'
            ]);

            $username = strtolower($request->input('username'));
            $usergroup = $request->input('usergroup');
            $nama = $request->input('nm_user');
            $telp = $request->input('telp_user');
            $email = $request->input('email_user');
            $defaultPassword = "123456";
            $user = Auth::user()->user_name;

            $users = User::firstOrCreate(
              ['user_name' => $username],
              [
                'user_password' => Hash::make($defaultPassword),
                'user_group' => $usergroup,
                'active' => 'Y',
                'action' => 'TAMBAH',
                'created_by' => $user,
                'user' => $user,
                'remember_token' => Str::random(10),
              ]
            );

            $usersProfile = UserProfile::firstOrCreate(
              ['user_name' => $username],
              [
                'user_group' => $usergroup,
                'nama_user' => $nama,
                'telp_user' => $telp,
                'email_user' => $email,
                'active' => 'Y',
                'created_by' => $user
              ]
            );

            $idUser = $users->id;

            $arrayMenu = $request->input('module');
                if ($arrayMenu != "") {
                    $countMenu = count($arrayMenu);
                    $listMenu = [];
                    for ($i = 0; $i < $countMenu; $i++) {
                        $dataAkses=[
                                            'user_id' => $idUser,
                                            'menu_id' => $arrayMenu[$i],
                                            'add' => 'N',
                                            'edit' => 'N',
                                            'delete' => 'N',
                                            'posting' => 'N',
                                            'print' => 'N',
                                            'export' => 'N',
                                            'approve' => 'N',
                                            'revisi' => 'N',
                                            'active' => 'Y',
                                            'action' => 'TAMBAH',
                                            'created_by' => $user
                                        ];
                        array_push($listMenu, $dataAkses);
                    }
                    ModuleAccess::insert($listMenu);
                }

                $log = ActionLog::create([
                        'module' => 'Users',
                        'action' => 'Save',
                        'desc' => 'Tambah User Baru',
                        'username' => Auth::user()->user_name
                    ]);
            if ($users->wasRecentlyCreated) {

                return redirect('Users')->with('success', 'User '.strtoupper($username).' Telah Dibuat!');
            }
            else {
                return redirect('Users')->with('error', 'Username '.strtoupper($username).' Telah Terpakai!');
            }
        }
        else {
            $request->validate([
              'username'=>'required',
              'usergroup'=> 'required'
            ]);

            $idUser = $request->input('userId');
            $idProfile = $request->input('profileId');
            $username = strtolower($request->input('username'));
            $usergroup = $request->input('usergroup');
            $nama = $request->input('nm_user');
            $telp = $request->input('telp_user');
            $email = $request->input('email_user');
            $aktif = $request->input('flagAktif');
            $defaultPassword = "123456";
            $user = Auth::user()->user_name;

            $users = DB::table('users')
                      ->where('id', $idUser)
                      ->update([
                                'user_group' => $usergroup,
                                'active' => $aktif,
                                'action' => 'EDIT',
                                'updated_by' => $user,
                              ]);

            $usersProfile = DB::table('user_profile')
                              ->where('id', $idProfile)
                              ->update([
                                        'user_group' => $usergroup,
                                        'nama_user' => $nama,
                                        'telp_user' => $telp,
                                        'email_user' => $email,
                                        'active' => $aktif,
                                        'updated_by' => $user,
                                      ]);

            $arrayMenu = $request->input('module');
            if ($arrayMenu != "") {
                $countMenu = count($arrayMenu);
                $listMenu = [];
                for ($i = 0; $i < $countMenu; $i++) {
                    $checkAkses = ModuleAccess::Where([['user_id', '=', $idUser], ['menu_id', '=', $arrayMenu[$i]]])->first();
                    if ($checkAkses) {
                        $dataAkses=[
                            'user_id' => $idUser,
                            'menu_id' => $arrayMenu[$i],
                            'add' => $checkAkses->add,
                            'edit' => $checkAkses->edit,
                            'delete' => $checkAkses->delete,
                            'posting' => $checkAkses->posting,
                            'print' => $checkAkses->print,
                            'export' => $checkAkses->export,
                            'approve' => $checkAkses->approve,
                            'revisi' => $checkAkses->revisi,
                            'active' => 'Y',
                            'action' => 'EDIT',
                            'created_by' => $checkAkses->created_by,
                            'updated_by' => $user
                        ];
                    }
                    else {
                        $dataAkses=[
                            'user_id' => $idUser,
                            'menu_id' => $arrayMenu[$i],
                            'add' => 'N',
                            'edit' => 'N',
                            'delete' => 'N',
                            'posting' => 'N',
                            'print' => 'N',
                            'export' => 'N',
                            'approve' => 'N',
                            'revisi' => 'N',
                            'active' => 'Y',
                            'action' => 'EDIT',
                            'created_by' => $user,
                            'updated_by' => $user
                        ];
                    }
                    array_push($listMenu, $dataAkses);
                }
                $deleteAkses = DB::table('module_access')->where([['user_id', '=', $idUser]])->delete();
                ModuleAccess::insert($listMenu);
            }

            return redirect('Users')->with('success', 'Data Username '.strtoupper($username).' Telah Diupdate!');
        }

    }

    public function ubahPasswordUser (Request $request)
    {
        $id = $request->input('user_id');
        $passwordLama = $request->input('old_password');
        $passwordBaru = $request->input('new_password');
        $userName = Auth::user()->user_name;
        $dataUser = User::find($id);
        $userPassword = $dataUser->user_password;
        $newPassword = Hash::make($passwordBaru);
        $test = Hash::check($passwordLama, $userPassword);

        if (Hash::check($passwordLama, $userPassword)) {
            $dataUsers = DB::table('users')
                      ->where('id', $id)
                      ->update([
                                'user_password' => $newPassword,
                                'action' => 'UBAHPASSWORD',
                                'updated_by' => $userName,
                              ]);

            $log = ActionLog::create([
                    'module' => 'Users',
                    'action' => 'Ubah Password',
                    'desc' => 'Ubah Password User',
                    'username' => Auth::user()->user_name
                ]);

        }
        else {
            return back()->with('danger', 'Password Lama Salah!');
        }

        Auth::logout();
        Session::flush();
        return redirect('main')->with('success', 'Ubah Password Berhasil!');
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            if (Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") {
                $id = $request->input('id');
                $dataUser = UserProfile::find($id);
                $userName = $dataUser->user_name;
                $menu = DB::table('user_profile')
                            ->where('id', '=', $id)
                            ->update([
                                'active' => 'N'
                            ]);

                $users = DB::table('users')
                            ->where('user_name', '=', $userName)
                            ->update([
                                'active' => 'N'
                            ]);

                $log = ActionLog::create([
                    'module' => 'User Profile',
                    'action' => 'Delete',
                    'desc' => 'Delete Menu',
                    'username' => Auth::user()->user_name
                ]);

                if ($dataUser) {
                    //$request->session()->flash('success', 'Data Berhasil dihapus!');
                    return response()->json();
                }
                else {
                    //$request->session()->flash('danger', 'Data Gagal dihapus!');
                    return response()->json();
                }
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }
}
