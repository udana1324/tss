<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Library\Sales;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Setting\Module;

class SalesController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Sales'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Sales'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales Category',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Category',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.sales.index', $data);
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

        $dataSales = Sales::all();

        return response()->json($dataSales);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Sales'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $log = ActionLog::create([
                    'module' => 'Sales',
                    'action' => 'Tambah',
                    'desc' => 'Tambah',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.sales.add', $data);
            }
            else {
                return redirect('/Sales')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Sales'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSales = Sales::find($id);

                $data['dataSales'] = $dataSales;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Sales',
                    'action' => 'Ubah',
                    'desc' => 'Ubah',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.sales.edit', $data);
            }
            else {
                return redirect('/Sales')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sales'=>'required',
            'telp_sales'=> 'required',
            'email_sales' => 'required|email:rfc'
        ]);

        $nm = $request->input('nama_sales');
        $telp = $request->input('telp_sales');
        $email = $request->input('email_sales');
        $user = Auth::user()->user_name;

        $sales = new Sales();
        $sales->nama_sales = $nm;
        $sales->telp_sales = $telp;
        $sales->email_sales = $email;
        $sales->created_by = $user;
        $sales->save();

        $log = ActionLog::create([
            'module' => 'Sales Category',
            'action' => 'Simpan',
            'desc' => 'Simpan Category',
            'username' => Auth::user()->user_name
        ]);

        if ($sales->wasRecentlyCreated) {
            return redirect('Sales')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Simpan Data '.strtoupper($nm).' Gagal!');
        }
    }

    public function update(Request $request, $id)
	{
        $request->validate([
            'nama_sales'=>'required',
            'telp_sales'=> 'required',
            'email_sales' => 'required|email:rfc'
        ]);

        $nm = $request->input('nama_sales');
        $telp = $request->input('telp_sales');
        $email = $request->input('email_sales');
        $user = Auth::user()->user_name;


        $sales = Sales::find($id);
        $sales->nama_sales = $nm;
        $sales->telp_sales = $telp;
        $sales->email_sales = $email;
        $sales->updated_by = $user;
        $sales->save();

        $log = ActionLog::create([
            'module' => 'Sales Category',
            'action' => 'Update',
            'desc' => 'Update Category',
            'username' => Auth::user()->user_name
        ]);

        if ($sales) {
            return redirect('Sales')->with('success', 'Data '.strtoupper($nm).' Telah Diupdate!');
        }
        else {
            return redirect('Sales')->with('warning', 'Update Data '.strtoupper($nm).' Gagal!');
        }
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_sales');
        $user = Auth::user()->user_name;
        $delete = Sales::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Sales Category',
            'action' => 'Delete',
            'desc' => 'Delete Category',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
