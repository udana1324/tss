<?php

namespace App\Http\Controllers\Product;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Product\ProductBrand;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class ProductBrandController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductBrand'],
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
                                                ['module.url', '=', '/ProductBrand'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Brand',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Product Brand',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_brand.index', $data);
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
                    ->select('merk_item', DB::raw('COUNT(merk_item) AS idCount'))
                    ->groupBy('merk_item');

        $dataBrand = ProductBrand::leftJoinSub($count, 'count', function($count) {
                                    $count->on('product_brand.id', '=', 'count.merk_item');
                                })
                                ->orderBy('count.idCount', 'asc')
                                ->get();

        return response()->json($dataBrand);
    }

    public function create()
    {
        if (Auth::check()) {
            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ProductBrand'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            if ($hakAkses->add = "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Brand',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Product Brand',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_brand.add', $data);
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
                                                ['module.url', '=', '/ProductBrand'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            if ($hakAkses->edit = "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataBrand = ProductBrand::find($id);

                $data['hakAkses'] = $hakAkses;
                $data['dataBrand'] = $dataBrand;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Brand',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Product Brand',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_brand.edit', $data);
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
            'nama_merk'=>'required',
            'keterangan_merk'=> 'required'
        ]);

        $merk = strtolower($request->input('nama_merk'));
        $keterangan = $request->input('keterangan_merk');
        $user = Auth::user()->user_name;

        $brand = ProductBrand::firstOrCreate(
            ['nama_merk' => $merk],
            [
                'keterangan_merk' => $keterangan,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Product Brand',
            'action' => 'Simpan',
            'desc' => 'Simpan Product Brand',
            'username' => Auth::user()->user_name
        ]);

        if ($brand->wasRecentlyCreated) {
            return redirect('ProductBrand')->with('success', 'Data '.strtoupper($merk).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($merk).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{
        $merk = strtolower($request->input('nama_merk'));
        $keterangan = $request->input('keterangan_merk');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'nama_merk' => [
                'required',
                Rule::unique('product_brand')->ignore($id),
            ],
            'keterangan_merk' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($merk).' Telah Digunakan!');
        }

        $update = ProductBrand::find($id);
        $update->nama_merk = $merk;
        $update->keterangan_merk = $keterangan;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Product Brand',
            'action' => 'Update',
            'desc' => 'Update Product Brand',
            'username' => Auth::user()->user_name
        ]);

        return redirect('ProductBrand')->with('success', 'Data '.strtoupper($merk).' Berhasil Diupdate!');
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_merk');
        $user = Auth::user()->user_name;
        $delete = ProductBrand::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Product Brand',
            'action' => 'Delete',
            'desc' => 'Delete Product Brand',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
