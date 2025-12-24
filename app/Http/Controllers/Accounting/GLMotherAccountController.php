<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting\Module;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Accounting\GLMotherAccount;

class GLMotherAccountController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLMotherAccount'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();
            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLMotherAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $parentMenu = Module::find($hakAkses->parent);

                $data['hakAkses'] = $hakAkses;


                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Mother Account',
                    'action' => 'Index',
                    'desc' => 'Tampil GL Mother Account',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.accounting.gl_mother_account.index', $data);
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

        $accounts = GLMotherAccount::orderBy('account_number', 'asc')
                                    ->get();

        return response()->json($accounts);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLMotherAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->add == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);


                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account',
                    'action' => 'Buat',
                    'desc' => 'Buat Account Baru',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_mother_account.add', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $accNumber = $request->input('account_number');
	    $accName = $request->input('account_name');
        $defSide = $request->input('default_side');
        $group = $request->input('group');

        $dataValidation = Validator::make($request->all(), [
            'account_number' => [
                'required',
                Rule::unique('gl_mother_account'),
            ],
            'account_name'=> 'required',
            'default_side'=> 'required',
            'group'=> 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Nomor Account '.strtoupper($accNumber).' Telah Digunakan!');
        }

        if (Auth::check()) {
            $user = Auth::user()->user_name;
        }
        else {
            return redirect('/');
        }

        $account = GLMotherAccount::firstOrCreate(
            ['account_number' => $accNumber],
            [
                'account_name' => $accName,
                'default_side' => $defSide,
                'group' => $group,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'GL Mother Account',
            'action' => 'TAMBAH',
            'desc' => 'Tambah Mother Account Baru',
            'username' => $user
        ]);

      if ($account->wasRecentlyCreated) {
        return redirect('GLMotherAccount')->with('success', 'Mother Account '.strtoupper($accNumber.' - '.$accName).' Telah Disimpan!');
      }
      else {
        return redirect('GLMotherAccount')->with('error', 'Mother Account '.strtoupper($accNumber).' Telah Tersedia!');
      }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLMotherAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataAccount = GLMotherAccount::find($id);

                $parentMenu = Module::find($hakAkses->parent);
                $data['dataAccount'] = $dataAccount;
                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Mother Account',
                    'action' => 'Edit',
                    'desc' => 'Edit Mother Account',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_mother_account.edit', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function update(Request $request, $id)
    {
	    $accNumber = $request->input('account_number');
	    $accName = $request->input('account_name');
        $defSide = $request->input('default_side');
        $group = $request->input('group');

        $dataValidation = Validator::make($request->all(), [
            'account_number' => [
                'required',
                Rule::unique('gl_account')->ignore($id),
            ],
            'account_name'=> 'required',
            'default_side'=> 'required',
            'group'=> 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Nomor Account '.strtoupper($accNumber).' Telah Digunakan!');
        }

        if (Auth::check()) {
            $user = Auth::user()->user_name;
        }
        else {
            return redirect('/');
        }

        $menu = DB::table('gl_mother_account')
                ->where('id', '=', $id)
                ->update([
                    'account_number' => $accNumber,
                    'account_name' => $accName,
                    'default_side' => $defSide,
                    'group' => $group,
                    'updated_by' => $user
                ]);

        $log = ActionLog::create([
                    'module' => 'GL Mother Account',
                    'action' => 'UPDATE',
                    'desc' => 'Update Mother Account',
                    'username' => $user
                ]);

        if ($menu) {
            return redirect('GLMotherAccount')->with('success', 'Mother Account '.strtoupper($accNumber.' - '.$accName).' Telah Diupdate!');
        }
        else {
            return redirect('GLMotherAccount')->with('error', 'Update Mother Account '.strtoupper($accNumber).' Gagal!');
        }
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLMotherAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->delete == "Y") {

                $id = $request->input('id');
                $account = GLMotherAccount::find($id);
                $menu = DB::table('gl_mother_account')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $account->delete();

                $log = ActionLog::create([
                    'module' => 'GL Mother Account',
                    'action' => 'Delete',
                    'desc' => 'Delete Mother Account',
                    'username' => Auth::user()->user_name
                ]);

                if ($account) {
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
