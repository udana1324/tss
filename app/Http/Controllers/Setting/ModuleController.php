<?php

namespace App\Http\Controllers\Setting;

use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Module;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;

class ModuleController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            if (Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataChild = DB::table('module')
                                ->select(DB::raw('id AS menu_id'), DB::raw('menu AS menu_name'), DB::raw('order_number AS menu_order'))
                                ->where([
                                         ['module.parent', '=', 'main'],
                                         ['module.active', '=' , 'Y']
                                        ]);
                $Module = Module::leftJoinSub($dataChild, 'dataChild', function($dataChild) {
                                    $dataChild->on('module.parent', '=', 'dataChild.menu_id');
                                })
                                ->orderBy('menu_order', 'asc')
                                ->orderBy('order_number', 'asc')
                                ->get();

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Modules'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $arrayStatus = array();
                foreach ($Module as $modStatus)
                {
                    if ($modStatus->active == "Y") {
                        $arrayStatus[] = "Active";
                    }
                    else {
                        $arrayStatus[] = "Inactive";
                    }
                }

                $dataParent = DB::table('module')
                            ->select(
                                        'module.id',
                                        'module.menu',
                                        'module.parent',
                                    )
                            ->where([
                                        ['module.active', '=', 'Y']
                                    ])
                            ->whereIn('module.parent', ['main', 'header'])
                            ->orderBy('module.order_number')
                            ->get();

                $uniqArrayStatus = array_unique($arrayStatus);

                $data['hakAkses'] = $hakAkses;
                $data['Module'] = $Module;
                $data['arrayStatus'] = $uniqArrayStatus;
                $data['dataParent'] = $dataParent;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'Index',
                    'desc' => 'Tampil Menu',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.setting.module.index', $data);
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
        $dataChild = DB::table('module')
                        ->select(DB::raw('id AS menu_id'), DB::raw('menu AS menu_name'), DB::raw('parent AS parent_id'), DB::raw('order_number AS menu_order'))
                        ->where([
                            ['module.parent', '=', 'main'],
                            ['module.active', '=' , 'Y']
                        ]);

        $Module = Module::leftJoinSub($dataChild, 'dataChild', function($dataChild) {
                            $dataChild->on('module.parent', '=', 'dataChild.menu_id');
                        })
                        ->orderBy('menu_order', 'asc')
                        ->orderBy('order_number', 'asc')
                        ->get();



        return response()->json($Module);
    }

    public function create()
    {
        if (Auth::check()) {

            if (Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $Module = Module::where([
                                            ['active', '=', 'Y'],
                                            ['parent', '=' , 'main']
                                        ])
                                ->orderBy('order_number', 'asc')
                                ->get();
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Modules'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $data['Module'] = $Module;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'Buat',
                    'desc' => 'Buat Menu Baru',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.setting.module.add', $data);
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
        	'menu'=>'required',
        	'url'=> 'required'
      	]);

	    $nm = strtolower($request->input('menu'));
	    $url = $request->input('url');
        $parent = $request->input('modul');
        $menuIcon = $request->input('menu_icon');
        $orderNum = $request->input('order_number');
        if (Auth::check()) {
            $user = Auth::user()->user_name;
        }
        else {
            return redirect('/');
        }

        if ($parent == "" || $parent == null) {
            $parent = "main";
        }

        $menu = Module::create([
            'menu' => ucwords($nm),
            'url' => $url,
            'parent' => $parent,
            'order_number' => $orderNum,
            'menu_icon' => $menuIcon,
            'active' => 'Y',
            'created_by' => $user
        ]);

        $log = ActionLog::create([
            'module' => 'Menu',
            'action' => 'TAMBAH',
            'desc' => 'Tambah Menu Baru',
            'username' => $user
        ]);

      if ($menu->wasRecentlyCreated) {
        return redirect('Modules')->with('success', 'Data Menu '.strtoupper($nm).' Telah Disimpan!');
      }
      else {
        return redirect('Modules')->with('error', 'Data '.strtoupper($nm).' Telah Tersedia!');
      }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            if (Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataMenu = Module::find($id);

                $Module = Module::where([
                                            ['active', '=', 'Y'],
                                            ['parent', '=' , 'main']
                                        ])
                                ->orderBy('order_number', 'asc')
                                ->get();

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Modules'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['Module'] = $Module;
                $data['dataMenu'] = $dataMenu;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'Edit',
                    'desc' => 'Edit Menu',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.setting.module.edit', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        	'menu'=>'required',
        	'url'=> 'required'
      	]);

	    $nm = strtolower($request->input('menu'));
	    $url = $request->input('url');
        $parent = $request->input('modul');
        $menuIcon = $request->input('menu_icon');
        $orderNum = $request->input('order_number');
        $isActive = $request->input('active');
        if ($isActive != 'Y') {
            $isActive = 'N';
        }
        if (Auth::check()) {
            $user = Auth::user()->user_name;
        }
        else {
            return redirect('/');
        }

        if ($parent == "" || $parent == null) {
            $parent = "main";
        }

        $menu = DB::table('module')
                ->where('id', '=', $id)
                ->update([
                    'menu' => ucwords($nm),
                    'url' => $url,
                    'parent' => $parent,
                    'order_number' => $orderNum,
                    //'menu_icon' => $menuIcon,
                    'active' => $isActive,
                    'updated_by' => $user
                ]);

        $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'UPDATE',
                    'desc' => 'Update Menu',
                    'username' => $user
                ]);

        if ($menu) {
            return redirect('Modules')->with('success', 'Data Menu '.strtoupper($nm).' Telah Diupdate!');
        }
        else {
            return redirect('Modules')->with('error', 'Update Data Menu '.strtoupper($nm).' Gagal!');
        }
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            if (Auth::user()->user_group == "admin" || Auth::user()->user_group == "super_admin") {
                $id = $request->input('id');
                $dataMenu = Module::find($id);
                $menu = DB::table('module')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $dataMenu->delete();

                $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'Delete',
                    'desc' => 'Delete Menu',
                    'username' => Auth::user()->user_name
                ]);

                if ($dataMenu) {
                    //$request->session()->flash('success', 'Data Berhasil dihapus!');
                    return response()->json();
                }
                else {
                    //$request->session()->flash('danger', 'Data Gagal dihapus!');
                    return response()->json();
                }
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }
}
