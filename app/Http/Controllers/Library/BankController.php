<?php

namespace App\Http\Controllers\Library;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Library\Bank;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class BankController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Bank'],
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
                                                ['module.url', '=', '/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataBank = Bank::all();

                $data['dataBank'] = $dataBank;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Bank',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Bank',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.bank.index', $data);
            }
            else {
                return redirect('/Bank')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataBank = Bank::all();

        return response()->json($dataBank);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Bank'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Bank',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Bank',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.library.bank.add', $data);
            }
            else {
                return redirect('/Bank')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Bank'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataBank = Bank::find($id);

                $data['hakAkses'] = $hakAkses;
                $data['dataBank'] = $dataBank;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Bank',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Bank',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.library.bank.edit', $data);
            }
            else {
                return redirect('/Bank')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $request->validate([
	    	'kode_bank'=>'required',
	        'nama_bank'=>'required'
	    ]);

      	$kd = strtolower($request->input('kode_bank'));
        $nm = $request->input('nama_bank');
        $deskripsi = $request->input('deskripsi_bank');
        $user = Auth::user()->user_name;

        $bank = Bank::firstOrCreate(
            ['kode_bank' => $kd],
            [
                'nama_bank' => $nm,
                'deskripsi_bank' => $deskripsi,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
                'module' => 'Bank',
                'action' => 'Tambah',
                'desc' => 'Tambah Bank Baru',
                'username' => Auth::user()->user_name
        ]);

        if ($bank->wasRecentlyCreated) {
            return redirect('Bank')->with('success', 'Data Bank '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return redirect('Bank')->with('danger', 'Kode Bank '.strtoupper($nm).' Telah Tersedia!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
	    	'kode_bank'=>'required',
	        'nama_bank'=>'required'
        ]);

        $kd = strtolower($request->input('kode_bank'));
        $nm = $request->input('nama_bank');
        $deskripsi = $request->input('deskripsi_bank');
      	$user = Auth::user()->user_name;

        $update = Bank::find($id);
        $update->kode_bank = $kd;
        $update->nama_bank = $nm;
        $update->deskripsi_bank = $deskripsi;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Bank',
            'action' => 'Update',
            'desc' => 'Update Bank',
            'username' => Auth::user()->user_name
        ]);

          return redirect('Bank')->with('success', 'Data Bank '.strtoupper($nm).' Telah Diupdate!');
    }

    public function delete(Request $request)
    {

      	$id = $request->input('id_bank');
      	$user = Auth::user()->user_name;
      	$data = Bank::find($id);
        $bank = DB::table('bank')
                    ->where('id', '=', $id)
                    ->update([
                        'deleted_by' => Auth::user()->user_name
                    ]);
        $data->delete();

        $log = ActionLog::create([
            'module' => 'Bank',
            'action' => 'Delete',
            'desc' => 'Delete Bank',
            'username' => Auth::user()->user_name
        ]);

        if ($data) {
            $request->session()->flash('delet', 'Data Berhasil dihapus!');
            return response()->json();
        }
        else {
            $request->session()->flash('delet', 'Data Gagal dihapus!');
            return response()->json();
        }
    }
}
