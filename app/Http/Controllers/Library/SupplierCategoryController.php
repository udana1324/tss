<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Library\SupplierCategory;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Setting\Module;

class SupplierCategoryController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/SupplierCategory'],
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
                                                ['module.url', '=', '/SupplierCategory'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Supplier Category',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Category',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.supplier_category.index', $data);
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
        $count = DB::table('supplier')
                    ->select('kategori_supplier', DB::raw('COUNT(kategori_supplier) AS idCount'))
                    ->groupBy('kategori_supplier');

        $dataCategory = SupplierCategory::leftJoinSub($count, 'count', function($count) {
                                            $count->on('supplier_category.id', '=', 'count.kategori_supplier');
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
                                            ['module.url', '=', '/SupplierCategory'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $log = ActionLog::create([
                    'module' => 'Supplier Category',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Category',
                    'username' => Auth::user()->user_name
                ]);
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.supplier_category.add', $data);
            }
            else {
                return redirect('/SupplierCategory')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/SupplierCategory'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataCategory = SupplierCategory::find($id);

                $data['dataCategory'] = $dataCategory;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Supplier Category',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Category',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.supplier_category.edit', $data);
            }
            else {
                return redirect('/SupplierCategory')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
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

        $kategoriSupp = SupplierCategory::firstOrCreate(
            ['kode_kategori' => $kd],
            [
                'nama_kategori' => $nm,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Supplier Category',
            'action' => 'Simpan',
            'desc' => 'Simpan Category',
            'username' => Auth::user()->user_name
        ]);

        if ($kategoriSupp->wasRecentlyCreated) {
            return redirect('SupplierCategory')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
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
                Rule::unique('supplier_category')->ignore($id),
            ],
            'nama_kategori' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }

        $update = SupplierCategory::find($id);
        $update->kode_kategori = $kd;
        $update->nama_kategori = $nm;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Supplier Category',
            'action' => 'Update',
            'desc' => 'Update Category',
            'username' => Auth::user()->user_name
        ]);

        if ($update) {
            return redirect('SupplierCategory')->with('success', 'Data '.strtoupper($nm).' Telah Diupdate!');
        }
        else {
            return redirect('SupplierCategory')->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_kategori');
        $user = Auth::user()->user_name;
        $delete = SupplierCategory::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Supplier Category',
            'action' => 'Delete',
            'desc' => 'Delete Category',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
