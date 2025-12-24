<?php

namespace App\Http\Controllers\Stock;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Stock\StockIndex;
use App\Models\ActionLog;
use App\Models\Library\DataIndex;
use App\Models\Setting\Module;
use stdClass;

class StockIndexController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/StockIndex'],
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
                                                ['module.url', '=', '/StockIndex'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataIndex = StockIndex::all();

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

                return view('pages.stock.stock_index.index', $data);
            }
            else {
                return redirect('/StockIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getStockIndex()
    {
        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $list = [];
        $i = 0;
        foreach ($dataIndex as $index) {
            $txt = "";
            foreach ($index->ancestors as $ancestors) {
                $txt = $txt.$ancestors->nama_index." > ";
            }

            $txt = $txt.$index->nama_index;
            $dataTxt = [
                'id' => $index->id,
                'nama_index' => $txt
            ];

            array_push($list, $dataTxt);
        }

        return response()->json($list);
    }

    public function getParentIndex(Request $request)
    {
        $jenis = $request->input('idJenis');

        $data = DataIndex::find($jenis);

        $parent = StockIndex::select('id', 'nama_index');

        $dataIndex = StockIndex::leftJoinSub($parent, 'parent', function($join_in) {
                                    $join_in->on('stock_index.parent_id', '=', 'parent.id');
                                })
                                ->select(
                                    'stock_index.id',
                                    'stock_index.nama_index',
                                    DB::raw("CONCAT(parent.nama_index) AS nama_parent")
                                )
                                ->where([
                                    ['stock_index.jenis_index', '=', $data->parent],
                                ])
                                ->defaultOrder()
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
                                            ['module.url', '=', '/StockIndex'],
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

                $dataIndex = DataIndex::orderBy('parent', 'asc')->orderBy('id', 'asc')->get();

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['dataIndex'] = $dataIndex;

                $log = ActionLog::create([
                    'module' => 'Index',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Index',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.stock.stock_index.add', $data);
            }
            else {
                return redirect('/StockIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/StockIndex'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataIndex = StockIndex::find($id);

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $dataIndex = StockIndex::where([
                                            ['id', '!=', $id]
                                        ])
                                        ->get();

                $index = StockIndex::find($id);

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['dataIndex'] = $dataIndex;
                $data['index'] = $index;

                $log = ActionLog::create([
                    'module' => 'Index',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Index',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.stock.stock_index.edit', $data);
            }
            else {
                return redirect('/StockIndex')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {

	    $request->validate([
	        'nama_index'=>'required'
	    ]);

      	$jenis = $request->input('jenisIndex');
        $nm = $request->input('nama_index');
        $parent = $request->input('parentIndex');
        $user = Auth::user()->user_name;

        $getJenis = DataIndex::find($jenis);

        if ($getJenis->parent == null) {
            $stockIndex = new StockIndex();
            $stockIndex->jenis_index = $jenis;
            $stockIndex->nama_index = $nm;
            $stockIndex->created_by = $user;
            $stockIndex->save();
        }
        else {
            $parentIndex = StockIndex::find($parent);
            $stockIndex = new StockIndex();
            $stockIndex->jenis_index = $jenis;
            $stockIndex->nama_index = $nm;
            $stockIndex->created_by = $user;

            $parentIndex->appendNode($stockIndex);
        }

        $log = ActionLog::create([
                'module' => 'Index',
                'action' => 'Tambah',
                'desc' => 'Tambah Index Baru',
                'username' => Auth::user()->user_name
        ]);

        if ($stockIndex->wasRecentlyCreated) {
            //return redirect('StockIndex')->with('success', 'Data Index '.strtoupper($nm).' Telah Disimpan!');
            return redirect()->back()->with('success', 'Data Index '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return redirect('StockIndex')->with('danger', 'Kode Index '.strtoupper($nm).' Telah Tersedia!');
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

        $update = StockIndex::find($id);
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

          return redirect('StockIndex')->with('success', 'Data Index '.strtoupper($nm).' Telah Diupdate!');
    }

    public function delete(Request $request)
    {

        //StockIndex::fixTree();
	$id = $request->input('id_index');
      	$user = Auth::user()->user_name;
      	$data = StockIndex::find($id);

        $count = DB::table('stock_transaction')
                    ->select(DB::raw("COUNT(id_index) AS angka"))
                    ->where([
                        ['id_index', '=', $id]
                    ])
                    ->first();

        if ($count->angka > 0) {
            $result = "failedUsed";
        }
        else {
            $data->deleted_by = $user;
            $data->delete();
            $result = "sukses";
        }

        $log = ActionLog::create([
            'module' => 'Index',
            'action' => 'Delete',
            'desc' => 'Delete Index',
            'username' => Auth::user()->user_name
        ]);

        return response()->json($result);
    }
}
