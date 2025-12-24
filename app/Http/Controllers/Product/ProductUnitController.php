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
use App\Models\Product\ProductUnit;
use App\Models\ActionLog;
use App\Models\Product\ProductDetail;
use App\Models\Setting\Module;

class ProductUnitController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ProductUnit'],
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
                                                ['module.url', '=', '/ProductUnit'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Unit',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Product Unit',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_unit.index', $data);
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
                                                ['module.url', '=', '/ProductUnit'],
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
                    'module' => 'Product Unit',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Product Unit',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_unit.add', $data);
            }
            else {
                return redirect('/ProductUnit')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                                ['module.url', '=', '/ProductUnit'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataUnit = ProductUnit::find($id);

                $data['dataUnit'] = $dataUnit;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Product Unit',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Product Unit',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.product.product_unit.edit', $data);
            }
            else {
                return redirect('/ProductUnit')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $count = ProductDetail::select('id_satuan', DB::raw('COUNT(id_satuan) AS idCount'))
                                ->groupBy('id_satuan');

        $dataUnit = ProductUnit::leftJoinSub($count, 'count', function($count) {
                                    $count->on('product_unit.id', '=', 'count.id_satuan');
                                })
                                ->orderBy('count.idCount', 'asc')
                                ->get();

        return response()->json($dataUnit);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_satuan'=>'required',
            'nama_satuan'=> 'required',
            'keterangan_satuan' => 'required'
        ]);

        $kode = strtolower($request->input('kode_satuan'));
        $nama = $request->input('nama_satuan');
        $keterangan = $request->input('keterangan_satuan');
        $kodePajak = $request->input('kode_satuan_pajak');
        $user = Auth::user()->user_name;

        $unit = ProductUnit::firstOrCreate(
            ['kode_satuan' => $kode],
            [
                'nama_satuan' => $nama,
                'keterangan_satuan' => $keterangan,
                'kode_satuan_pajak' => $kodePajak,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'Product Unit',
            'action' => 'Simpan',
            'desc' => 'Simpan Product Unit',
            'username' => Auth::user()->user_name
        ]);

        if ($unit->wasRecentlyCreated) {
            return redirect('ProductUnit')->with('success', 'Data '.strtoupper($kode).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{
        $kode = strtolower($request->input('kode_satuan'));
        $nama = $request->input('nama_satuan');
        $keterangan = $request->input('keterangan_satuan');
        $kodePajak = $request->input('kode_satuan_pajak');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'kode_satuan' => [
                'required',
                Rule::unique('product_unit')->ignore($id),
            ],
            'nama_satuan' => 'required',
            'keterangan_satuan' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kode).' Telah Digunakan!');
        }

        $update = ProductUnit::find($id);
        $update->kode_satuan = $kode;
        $update->nama_satuan = $nama;
        $update->keterangan_satuan = $keterangan;
        $update->kode_satuan_pajak = $kodePajak;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Product Unit',
            'action' => 'Update',
            'desc' => 'Update Product Unit',
            'username' => Auth::user()->user_name
        ]);

        return redirect('ProductUnit')->with('success', 'Data '.strtoupper($kode).' Berhasil Diupdate!');
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_satuan');
        $user = Auth::user()->user_name;
        $delete = ProductUnit::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Product Unit',
            'action' => 'Delete',
            'desc' => 'Delete Product Unit',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
    }
}
