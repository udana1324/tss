<?php

namespace App\Http\Controllers\Library;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Library\DataIndex;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class DataIndexController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/DataIndex'],
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
                                                ['module.url', '=', '/DataIndex'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataIndex = DataIndex::all();

                $data['dataIndex'] = $dataIndex;
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Index',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Index',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.data_index.index', $data);
            }
            else {
                return redirect('/DataIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataParent = DataIndex::select('id','kode_index', 'nama_index');

        $dataIndex = DataIndex::leftJoinSub($dataParent, 'dataParent', function($dataParent) {
                                    $dataParent->on('data_index.parent', '=', 'dataParent.id');
                                })
                                ->select(
                                    'data_index.*',
                                    DB::raw('dataParent.kode_index as kode_parent'),
                                    DB::raw('dataParent.nama_index as nama_parent')
                                )
                                ->get();

        return response()->json($dataIndex);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/DataIndex'],
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

                $dataIndex = DataIndex::get();

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['dataIndex'] = $dataIndex;

                $log = ActionLog::create([
                    'module' => 'Index',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Index',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.library.data_index.add', $data);
            }
            else {
                return redirect('/DataIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/DataIndex'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataIndex = DataIndex::find($id);

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $dataIndex = DataIndex::where([
                                            ['id', '!=', $id]
                                        ])
                                        ->get();

                $index = DataIndex::find($id);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['dataIndex'] = $dataIndex;
                $data['index'] = $index;

                $log = ActionLog::create([
                    'module' => 'Index',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Index',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.library.data_index.edit', $data);
            }
            else {
                return redirect('/DataIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $request->validate([
	    	'kode_index'=>'required',
	        'nama_index'=>'required'
	    ]);

      	$kd = strtolower($request->input('kode_index'));
        $nm = $request->input('nama_index');
        $parent = $request->input('parentIndex');
        $user = Auth::user()->user_name;

        $index = DataIndex::firstOrCreate(
            ['kode_index' => $kd],
            [
                'nama_index' => $nm,
                'parent' => $parent,
                'created_by' => $user
            ]
        );

        $log = ActionLog::create([
                'module' => 'Index',
                'action' => 'Tambah',
                'desc' => 'Tambah Index Baru',
                'username' => Auth::user()->user_name
        ]);

        if ($index->wasRecentlyCreated) {
            return redirect('DataIndex')->with('success', 'Data Index '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return redirect('DataIndex')->with('danger', 'Kode Index '.strtoupper($nm).' Telah Tersedia!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
	    	'kode_index'=>'required',
	        'nama_index'=>'required'
        ]);

        $kd = strtolower($request->input('kode_index'));
        $nm = $request->input('nama_index');
        $parent = $request->input('parentIndex');
      	$user = Auth::user()->user_name;

        $update = DataIndex::find($id);
        $update->kode_index = $kd;
        $update->nama_index = $nm;
        $update->parent = $parent;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'Index',
            'action' => 'Update',
            'desc' => 'Update Index',
            'username' => Auth::user()->user_name
        ]);

          return redirect('DataIndex')->with('success', 'Data Index '.strtoupper($nm).' Telah Diupdate!');
    }

    public function delete(Request $request)
    {

      	$id = $request->input('id_bank');
      	$user = Auth::user()->user_name;
      	$data = DataIndex::find($id);
        $bank = DB::table('bank')
                    ->where('id', '=', $id)
                    ->update([
                        'deleted_by' => Auth::user()->user_name
                    ]);
        $data->delete();

        $log = ActionLog::create([
            'module' => 'Index',
            'action' => 'Delete',
            'desc' => 'Delete Index',
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
