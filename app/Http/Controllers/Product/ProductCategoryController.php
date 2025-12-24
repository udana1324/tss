<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Classes\BusinessManagement\SetMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Product\ProductCategory;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class ProductCategoryController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductCategory'],
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
                                                ['module.url', '=', '/ProductCategory'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Category',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Product Category',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_category.index', $data);
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
        $count = DB::table('product')
                    ->select('kategori_item', DB::raw('COUNT(kategori_item) AS idCount'))
                    ->groupBy('kategori_item');

        $dataCategory = ProductCategory::leftJoinSub($count, 'count', function($count) {
                                    $count->on('product_category.id', '=', 'count.kategori_item');
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
                                                ['module.url', '=', '/ProductCategory'],
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
                    'module' => 'Product Category',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Product Category',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_category.add', $data);
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
                                                ['module.url', '=', '/ProductCategory'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataCategory = ProductCategory::find($id);

                $data['dataCategory'] = $dataCategory;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Category',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Product Category',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_category.edit', $data);
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
            'kode_kategori'=>'required',
            'nama_kategori'=> 'required'
        ]);

        $kode = strtolower($request->input('kode_kategori'));
        $nama = $request->input('nama_kategori');
        $kodePajak = $request->input('kode_kategori_pajak');
        $user = Auth::user()->user_name;

        $category = ProductCategory::firstOrCreate(
            ['kode_kategori' => $kode],
            [
                'nama_kategori' => $nama,
                'kode_kategori_pajak' => $kodePajak,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Product Category',
            'action' => 'Simpan',
            'desc' => 'Simpan Product Category',
            'username' => Auth::user()->user_name
        ]);

        if ($category->wasRecentlyCreated) {
            return redirect('ProductCategory')->with('success', 'Data '.strtoupper($kode).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{
        $kode = strtolower($request->input('kode_kategori'));
        $nama = $request->input('nama_kategori');
        $kodePajak = $request->input('kode_kategori_pajak');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'kode_kategori' => [
                'required',
                Rule::unique('product_category')->ignore($id),
            ],
            'nama_kategori' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }

        $update = ProductCategory::find($id);
        $update->kode_kategori = $kode;
        $update->nama_kategori = $nama;
        $update->kode_kategori_pajak = $kodePajak;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Product Category',
            'action' => 'Update',
            'desc' => 'Update Product Category',
            'username' => Auth::user()->user_name
        ]);

        return redirect('ProductCategory')->with('success', 'Data '.strtoupper($kode).' Telah Disimpan!');
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_kategori');
        $user = Auth::user()->user_name;
        $delete = ProductCategory::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Product Category',
            'action' => 'Delete',
            'desc' => 'Delete Product Category',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
