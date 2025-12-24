<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Classes\BusinessManagement\SetMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\ActionLog;
use App\Models\Accounting\GLAccountLevel;
use App\Models\Setting\Module;

class GLAccountLevelController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLAccountLevel'],
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
                                                ['module.url', '=', '/GLAccountLevel'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Level',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Account Level',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_account_level.index', $data);
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
        $count = DB::table('gl_account')
                    ->select('account_type', DB::raw('COUNT(account_type) AS idCount'))
                    ->groupBy('account_type');

        $dataCategory = GLAccountLevel::leftJoinSub($count, 'count', function($count) {
                                    $count->on('gl_account_level.id', '=', 'count.account_type');
                                })
                                ->orderBy('count.idCount', 'asc')
                                ->get();

        return response()->json($dataCategory);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLAccountLevel'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Level',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Account Level',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_account_level.add', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLAccountLevel'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataAccountLevel = GLAccountLevel::find($id);

                $data['dataAccountLevel'] = $dataAccountLevel;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Level',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Account Level',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_account_level.edit', $data);
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
        $request->validate([
            'nama_level'=>'required'
        ]);

        $nama = $request->input('nama_level');
        $user = Auth::user()->user_name;

        $category = GLAccountLevel::firstOrCreate(
            ['nama_level' => $nama],
            [
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Account Level',
            'action' => 'Simpan',
            'desc' => 'Simpan Account Level',
            'username' => Auth::user()->user_name
        ]);

        if ($category->wasRecentlyCreated) {
            return redirect('GLAccountLevel')->with('success', 'Data '.strtoupper($nama).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($nama).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{
        $nama = $request->input('nama_level');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'nama_level' => [
                'required',
                Rule::unique('gl_account_level')->ignore($id),
            ],
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Account Level '.strtoupper($nama).' Telah Digunakan!');
        }

        $update = GLAccountLevel::find($id);
        $update->nama_level = $nama;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Account Level',
            'action' => 'Update',
            'desc' => 'Update Account Level',
            'username' => Auth::user()->user_name
        ]);

        return redirect('GLAccountLevel')->with('success', 'Data '.strtoupper($nama).' Telah Disimpan!');
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_level');
        $user = Auth::user()->user_name;
        $delete = GLAccountLevel::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Account Level',
            'action' => 'Delete',
            'desc' => 'Delete Account Level',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
