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
use App\Models\Product\ProductSpecification;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class ProductSpecificationController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductSpecification'],
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
                                                ['module.url', '=', '/ProductSpecification'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Specification',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Product Specification',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_specification.index', $data);
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
        $count = DB::table('product_detail_specification')
                    ->select('id_spesifikasi', DB::raw('COUNT(id_spesifikasi) AS idCount'))
                    ->groupBy('id_spesifikasi');

        $dataCategory = ProductSpecification::leftJoinSub($count, 'count', function($count) {
                                    $count->on('product_specification.id', '=', 'count.id_spesifikasi');
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
                                                ['module.url', '=', '/ProductSpecification'],
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
                    'module' => 'Product Specification',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Product Specification',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_specification.add', $data);
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
                                                ['module.url', '=', '/ProductSpecification'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataSpek = ProductSpecification::find($id);

                $data['dataSpek'] = $dataSpek;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Specification',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Product Specification',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_specification.edit', $data);
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
            'kode_spesifikasi'=>'required',
            'nama_spesifikasi'=> 'required'
        ]);

        $kode = strtolower($request->input('kode_spesifikasi'));
        $nama = $request->input('nama_spesifikasi');
        $flagCetak = $request->input('flag_cetak');
        $user = Auth::user()->user_name;

        $spec = ProductSpecification::firstOrCreate(
            ['kode_spesifikasi' => $kode],
            [
                'nama_spesifikasi' => $nama,
                'flag_cetak' => $flagCetak,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Product Specification',
            'action' => 'Simpan',
            'desc' => 'Simpan Product Specification',
            'username' => Auth::user()->user_name
        ]);

        if ($spec->wasRecentlyCreated) {
            return redirect('ProductSpecification')->with('success', 'Data '.strtoupper($kode).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{
        $kode = strtolower($request->input('kode_spesifikasi'));
        $nama = $request->input('nama_spesifikasi');
        $flagCetak = $request->input('flag_cetak');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'kode_spesifikasi' => [
                'required',
                Rule::unique('product_specification')->ignore($id),
            ],
            'nama_spesifikasi' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }

        $update = ProductSpecification::find($id);
        $update->kode_spesifikasi = $kode;
        $update->nama_spesifikasi = $nama;
        $update->flag_cetak = $flagCetak;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Product Specification',
            'action' => 'Update',
            'desc' => 'Update Product Specification',
            'username' => Auth::user()->user_name
        ]);

        return redirect('ProductSpecification')->with('success', 'Data '.strtoupper($kode).' Telah Disimpan!');
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_spesifikasi');
        $user = Auth::user()->user_name;
        $delete = ProductSpecification::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Product Specification',
            'action' => 'Delete',
            'desc' => 'Delete Product Specification',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
