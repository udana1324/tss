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
use App\Models\Library\CustomerGroup;
use App\Models\ActionLog;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerGroupDetail;
use App\Models\Setting\Module;

class CustomerGroupController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CustomerGroup'],
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
                                                ['module.url', '=', '/CustomerGroup'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer Group',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Group',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_group.index', $data);
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
                                            ['module.url', '=', '/CustomerGroup'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $parentMenu = Module::find($hakAkses->parent);

                $dataCustomer = Customer::distinct()
                                        ->leftJoin('customer_group_detail', 'customer_group_detail.id_customer', 'customer.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->whereNotIn('customer.id', function($query){
                                            $query->select('id_customer')->from('customer_group_detail');
                                        })
                                        ->get();

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Customer Group',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Group',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_group.add', $data);
            }
            else {
                return redirect('/CustomerGroup')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/CustomerGroup'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataGroup = CustomerGroup::find($id);
                $dataCustomer = Customer::distinct()
                                        ->leftJoin('customer_group_detail', 'customer_group_detail.id_customer', 'customer.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->whereNotIn('customer.id', function($query){
                                            $query->select('id_customer')->from('customer_group_detail');
                                        })
                                        ->get();

                $detailCust = CustomerGroupDetail::leftJoin('customer', 'customer_group_detail.id_customer', '=', 'customer.id')
                                                    ->select(
                                                        'customer.id',
                                                        'customer.nama_customer'
                                                    )
                                                    ->where('id_group', $id)->get();

                $data['dataGroup'] = $dataGroup;
                $data['dataCustomer'] = $dataCustomer;
                $data['detailCust'] = $detailCust;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer Group',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Group',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_group.edit', $data);
            }
            else {
                return redirect('/CustomerGroup')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CustomerGroup'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataGroup = CustomerGroup::find($id);

                $detailCust = CustomerGroupDetail::leftJoin('customer', 'customer_group_detail.id_customer', '=', 'customer.id')
                                                    ->select(
                                                        'customer.id',
                                                        'customer.nama_customer'
                                                    )
                                                    ->where('id_group', $id)->get();

                $data['dataGroup'] = $dataGroup;
                $data['detailCust'] = $detailCust;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Customer Group',
                    'action' => 'Detail',
                    'desc' => 'Detail Group',
                    'username' => Auth::user()->user_name
                ]);

                Session::put('currentParent', 'nav-pustaka');

                return view('pages.library.customer_group.detail', $data);
            }
            else {
                return redirect('/CustomerGroup')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataGroup = CustomerGroup::all();

        return response()->json($dataGroup);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_group'=>'required',
            'nama_group'=> 'required'
        ]);

        $kd = strtolower($request->input('kode_group'));
        $nm = $request->input('nama_group');
        $flagHarga = $request->input('flagHarga');
        $user = Auth::user()->user_name;

        $groupCust = CustomerGroup::firstOrCreate(
            ['kode_group' => $kd],
            [
                'nama_group' => $nm,
                'flag_harga' => $flagHarga,
                'created_by' => $user
            ]
        );

        $arrayCust = $request->input('isi');
        if ($arrayCust != "") {
            $listTerms = [];
                foreach ($arrayCust as $detil) {
                    $dataTerms=[
                        'id_group' => $groupCust->id,
                        'id_customer' => $detil["custID"],
                        'created_by' => Auth::user()->user_name,
                        'created_at' => now(),
                    ];
                    array_push($listTerms, $dataTerms);
                }
            CustomerGroupDetail::insert($listTerms);
        }

        $log = ActionLog::create([
            'module' => 'Customer Group',
            'action' => 'Simpan',
            'desc' => 'Simpan Group',
            'username' => Auth::user()->user_name
        ]);

        if ($groupCust->wasRecentlyCreated) {
            return redirect('CustomerGroup')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($nm).' Telah Digunakan!');
        }
    }

    public function update(Request $request, $id)
	{

        $kd = strtolower($request->input('kode_group'));
        $nm = $request->input('nama_group');
        $flagHarga = $request->input('flagHarga');
        $user = Auth::user()->user_name;

        $dataValidation = Validator::make($request->all(), [
            'kode_group' => [
                'required',
                Rule::unique('customer_group')->ignore($id),
            ],
            'nama_group' => 'required',
        ]);

        if ($dataValidation->fails()) {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }

        $update = CustomerGroup::find($id);
        $update->kode_group = $kd;
        $update->nama_group = $nm;
        $update->flag_harga = $flagHarga;
        $update->updated_by = $user;
        $update->save();

        $arrayCust = $request->input('isi');
        if ($arrayCust != "") {
            $delete = DB::table('customer_group_detail')->where('id_group', '=', $id)->delete();
            $listTerms = [];
                foreach ($arrayCust as $detil) {
                    $dataTerms=[
                        'id_group' => $update->id,
                        'id_customer' => $detil["custID"],
                        'created_by' => Auth::user()->user_name,
                        'created_at' => now(),
                    ];
                    array_push($listTerms, $dataTerms);
                }
            CustomerGroupDetail::insert($listTerms);
        }

        $log = ActionLog::create([
            'module' => 'Customer Group',
            'action' => 'Update',
            'desc' => 'Update Group',
            'username' => Auth::user()->user_name
        ]);

        if ($update) {
            return redirect('CustomerGroup')->with('success', 'Data '.strtoupper($nm).' Telah Disimpan!');
        }
        else {
            return back()->with('warning', 'Kode '.strtoupper($kd).' Telah Digunakan!');
        }
	}

    public function delete(Request $request)
    {

        $id = $request->input('id_group');
        $user = Auth::user()->user_name;
        $delete = CustomerGroup::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Customer Group',
            'action' => 'Delete',
            'desc' => 'Delete Group',
            'username' => Auth::user()->user_name
        ]);

        $request->session()->flash('delet', 'Data Berhasil dihapus!');
        return response()->json(['success'=>'Data Berhasil Dihapus!']);
     }
}
