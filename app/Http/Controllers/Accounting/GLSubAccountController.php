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
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLSubAccount;
use App\Models\Accounting\GLMotherAccount;

class GLSubAccountController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLSubAccount'],
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
                                                ['module.url', '=', '/GLSubAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $parentMenu = Module::find($hakAkses->parent);

                $motherAccount = GLMotherAccount::orderBy('account_number', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['motherAccount'] = $motherAccount;


                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Account',
                    'action' => 'Index',
                    'desc' => 'Tampil GL Account',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.accounting.gl_sub_account.index', $data);
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

        $accounts = GLSubAccount::leftJoin('gl_account', 'gl_sub_account.id_account', 'gl_account.id')
                        ->leftJoin('gl_mother_account', 'gl_account.id_mother_account', 'gl_mother_account.id')
                        ->select(
                            'gl_sub_account.*',
                            DB::raw("gl_account.account_number as paccount_number"),
                            DB::raw("gl_account.account_name as paccount_name"),
                            DB::raw("gl_mother_account.account_number as maccount_number"),
                            DB::raw("gl_mother_account.account_name as maccount_name"),
                        )
                        ->orderBy('gl_sub_account.account_number', 'asc')
                        ->get();



        return response()->json($accounts);
    }

    public function getParents(Request $request)
    {
        $idMother = $request->input('idMotherAccount');

        $accounts = GLAccount::where([
                                ['gl_account.id_mother_account', '=', $idMother]
                            ])
                            ->orderBy('gl_account.account_number', 'asc')
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
                                                ['module.url', '=', '/GLSubAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->add == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $motherAccount = GLMotherAccount::orderBy('account_number', 'asc')->get();


                $parentMenu = Module::find($hakAkses->parent);

                $data['motherAccount'] = $motherAccount;
                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sub Account',
                    'action' => 'Buat',
                    'desc' => 'Buat Sub Account Baru',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_sub_account.add', $data);
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
        $idMother = $request->input('id_mother_account');
        $idAccount = $request->input('id_account');
        $frontNum = $request->input('front_account_number');

        $dataValidation = Validator::make($request->all(), [
            'account_number' => [
                'required',
                Rule::unique('gl_sub_account'),
            ],
            'account_name'=> 'required',
            'id_mother_account'=> 'required',
            'id_account'=> 'required',
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

        $accNumberFull = $frontNum."-".$accNumber;

        $countItem = DB::table('gl_sub_account')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['account_number', '=', $accNumberFull]
                                ])
                                ->first();

        $count = $countItem->angka;

        if ($count > 0) {
            return redirect()->back()->with('warning', 'Update Account '.strtoupper($accNumber).' Gagal! Sub Account Number telah tersedia!');
        }

        $account = GLSubAccount::firstOrCreate(
            ['account_number' => $accNumberFull],
            [
                'account_name' => $accName,
                'id_mother_account' => $idMother,
                'id_account' => $idAccount,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'GL Sub Account',
            'action' => 'TAMBAH',
            'desc' => 'Tambah Sub Account Baru',
            'username' => $user
        ]);

      if ($account->wasRecentlyCreated) {
        return redirect('GLSubAccount')->with('success', 'Data Sub Account '.strtoupper($accNumber.' - '.$accName).' Telah Disimpan!');
      }
      else {
        return redirect('GLSubAccount')->with('error', 'Nomor Account '.strtoupper($accNumber).' Telah Tersedia!');
      }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLSubAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataAccount = GLSubAccount::find($id);
                $motherAccount = GLMotherAccount::orderBy('account_number', 'asc')->get();


                $parentMenu = Module::find($hakAkses->parent);

                $data['motherAccount'] = $motherAccount;
                $data['dataAccount'] = $dataAccount;
                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Sub Account',
                    'action' => 'Edit',
                    'desc' => 'Edit Sub Account',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_sub_account.edit', $data);
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
        $idMother = $request->input('id_mother_account');
        $idAccount = $request->input('id_account');
        $frontNum = $request->input('front_account_number');

        $dataValidation = Validator::make($request->all(), [
            'account_number' => [
                'required',
                Rule::unique('gl_sub_account')->ignore($id),
            ],
            'account_name'=> 'required',
            'id_mother_account'=> 'required',
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

        $accNumberFull = $frontNum."-".$accNumber;

        $countItem = DB::table('gl_sub_account')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id', '!=' , $id],
                                    ['account_number', '=', $accNumberFull]
                                ])
                                ->first();

        $count = $countItem->angka;

        if ($count > 0) {
            return redirect()->back()->with('warning', 'Update Account '.strtoupper($accNumber).' Gagal! Sub Account Number telah tersedia!');
        }

        $menu = DB::table('gl_sub_account')
                ->where('id', '=', $id)
                ->update([
                    'account_number' => $accNumberFull,
                    'account_name' => $accName,
                    'id_mother_account' => $idMother,
                    'id_account' => $idAccount,
                    'updated_by' => $user
                ]);

        $log = ActionLog::create([
                    'module' => 'GL Sub Account',
                    'action' => 'UPDATE',
                    'desc' => 'Update Sub Account',
                    'username' => $user
                ]);

        if ($menu) {
            return redirect('GLSubAccount')->with('success', 'Data Account '.strtoupper($accNumber.' - '.$accName).' Telah Diupdate!');
        }
        else {
            return redirect('GLSubAccount')->with('error', 'Update Account '.strtoupper($accNumber).' Gagal!');
        }
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLSubAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->delete == "Y") {

                $id = $request->input('id');
                $account = GLSubAccount::find($id);
                $menu = DB::table('gl_sub_account')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $account->delete();

                $log = ActionLog::create([
                    'module' => 'GL Sub Account',
                    'action' => 'Delete',
                    'desc' => 'Delete Sub Account',
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
