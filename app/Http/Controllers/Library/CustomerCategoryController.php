<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Classes\BusinessManagement\SetMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Library\CustomerCategory;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class CustomerCategoryController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CustomerCategory'],
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
                                                ['module.url', '=', '/CustomerCategory'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer Category',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Category',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_category.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CustomerCategory'],
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
                    'module' => 'Customer Category',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Category',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_category.add', $data);
            }
            else {
                return redirect('/CustomerCategory')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/CustomerCategory'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataCategory = CustomerCategory::find($id);

                $data['dataCategory'] = $dataCategory;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer Category',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Category',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_category.edit', $data);
            }
            else {
                return redirect('/CustomerCategory')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $count = DB::table('customer')
                    ->select('kategori_customer', DB::raw('COUNT(kategori_customer) AS idCount'))
                    ->groupBy('kategori_customer');

        $dataCategory = CustomerCategory::leftJoinSub($count, 'count', function($count) {
                                            $count->on('customer_category.id', '=', 'count.kategori_customer');
                                        })
                                        ->orderBy('count.idCount', 'asc')
                                        ->get();

        return response()->json($dataCategory);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori'=>'required',
            'nama_kategori'=> 'required'
        ]);

        $kd = strtolower($request->input('kode_kategori'));
        $nm = $request->input('nama_kategori');
        $user = Auth::user()->user_name;

        $kategoriCust = CustomerCategory::firstOrCreate(
            ['kode_kategori' => $kd],
            [
                'nama_kategori' => $nm,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Customer Category',
            'action' => 'Simpan',
            'desc' => 'Simpan Category',
            'username' => Auth::user()->user_name
        ]);

        if ($kategoriCust->wasRecentlyCreated) {
            return redirect('CustomerCategory')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($nm).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{

        $kd = strtolower($request->input('kode_kategori'));
        $nm = $request->input('nama_kategori');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'kode_kategori' => [
                'required',
                Rule::unique('customer_category')->ignore($id),
            ],
            'nama_kategori' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }

        $update = CustomerCategory::find($id);
        $update->kode_kategori = $kd;
        $update->nama_kategori = $nm;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Customer Category',
            'action' => 'Update',
            'desc' => 'Update Category',
            'username' => Auth::user()->user_name
        ]);

        if ($update) {
            return redirect('CustomerCategory')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_kategori');
        $user = Auth::user()->user_name;
        $delete = CustomerCategory::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Customer Category',
            'action' => 'Delete',
            'desc' => 'Delete Category',
            'username' => Auth::user()->user_name
        ]);

        $request->session()->flash('delet', 'Data Berhasil dihapus!');
        return response()->json(['success'=>'Data Berhasil Dihapus!']);
     }
}
