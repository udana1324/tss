<?php

namespace App\Http\Controllers\Library;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLSubAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Library\Bank;
use App\Models\Library\CompanyAccount;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class CompanyAccountController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CompanyAccount'],
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
                                                ['module.url', '=', '/CompanyAccount'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataRek = CompanyAccount::join('bank', 'company_account.bank', '=', 'bank.id')
                                        ->select(	'company_account.id',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank',
                                                    'company_account.cabang',
                                                    'company_account.nomor_rekening',
                                                    'company_account.atas_nama',
                                                    'company_account.bank'
                                                )
                                        ->get();

                $listBank = Bank::select(DB::raw("CONCAT(kode_bank, ' - ', nama_bank) AS bank"), 'id')
                                      ->pluck('bank', 'id');

                $data['hakAkses'] = $hakAkses;
                $data['dataRek'] = $dataRek;
                $data['listBank'] = $listBank;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'CompanyAccount',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Account',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.company_account.index', $data);
            }
            else {
                return redirect('/CompanyAccount')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataRek = CompanyAccount::join('bank', 'company_account.bank', '=', 'bank.id')
                                ->select(
                                    'company_account.id',
                                    'bank.kode_bank',
                                    'bank.nama_bank',
                                    'company_account.cabang',
                                    'company_account.nomor_rekening',
                                    'company_account.atas_nama',
                                    'company_account.bank'
                                )
                                ->get();
        return response()->json($dataRek);
    }

    public function create() {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CompanyAccount'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $listBank = Bank::select(DB::raw("CONCAT(kode_bank, ' - ', nama_bank) AS bank"), 'id')
                                      ->pluck('bank', 'id');

                $parentAccount = GLAccount::select(
                                                    'gl_account.id',
                                                    'gl_account.account_number',
                                                    'gl_account.account_name',
                                                )
                                            ->orderBy('gl_account.account_number')
                                            ->get();



                foreach ($parentAccount as $dataParent) {
                    $dataParentTemp = GLSubAccount::select(
                                                    'gl_sub_account.id',
                                                    'gl_sub_account.account_number',
                                                    'gl_sub_account.account_name',
                                                )
                                                ->where([
                                                            ['gl_sub_account.id_mother_account', '=', $dataParent->id],
                                                        ])
                                                ->orderBy('gl_sub_account.account_number')
                                                ->get();

                    $dataParent->child = $dataParentTemp;
                }

                $data['dataParent'] = $parentAccount;

                $data['listBank'] = $listBank;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'CompanyAccount',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Account',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.company_account.add', $data);
            }
            else {
                return redirect('/CompanyAccount')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id) {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/CompanyAccount'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $listBank = Bank::select(DB::raw("CONCAT(kode_bank, ' - ', nama_bank) AS bank"), 'id')
                                      ->pluck('bank', 'id');

                $parentAccount = GLAccount::select(
                                                    'gl_account.id',
                                                    'gl_account.account_number',
                                                    'gl_account.account_name',
                                                )
                                            ->whereIn('gl_account.id', [1,2])
                                            ->orderBy('gl_account.account_number')
                                            ->get();



                foreach ($parentAccount as $dataParent) {
                    $dataParentTemp = GLSubAccount::select(
                                                    'gl_sub_account.id',
                                                    'gl_sub_account.account_number',
                                                    'gl_sub_account.account_name',
                                                )
                                                ->where([
                                                    ['gl_sub_account.id_account', '=', $dataParent->id],
                                                ])
                                                ->orderBy('gl_sub_account.account_number')
                                                ->get();

                    $dataParent->child = $dataParentTemp;
                }

                $data['dataParent'] = $parentAccount;

                $dataAccount = CompanyAccount::find($id);

                $data['listBank'] = $listBank;
                $data['dataAccount'] = $dataAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'CompanyAccount',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Account',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.library.company_account.edit', $data);
            }
            else {
                return redirect('/CompanyAccount')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $request->validate([
	    	'bank'=>'required',
	    	'rek_bank'=>'required',
	        'nama_bank'=>'required'
	    ]);

      	$atasNama = strtolower($request->input('nama_bank'));
      	$rekBank = $request->input('rek_bank');
      	$bank = $request->input('bank');
      	$cabang = strtolower($request->input('cabang_bank'));
        $idAccount = $request->input('id_account');
      	$user = Auth::user()->user_name;

      	$rek = CompanyAccount::firstOrCreate(
            ['nomor_rekening' => $rekBank],
            [
                  'bank' => $bank,
                  'cabang' => $cabang,
                  'atas_nama' => $atasNama,
                  'id_account' => $idAccount,
                  'created_by' => $user
            ]
        );

        $log = ActionLog::create([
            'module' => 'CompanyAccount',
            'action' => 'Tambah',
            'desc' => 'Tambah Account',
            'username' => Auth::user()->user_name
        ]);

        if ($rek->wasRecentlyCreated) {
            return redirect('CompanyAccount')->with('success', 'Data Rekening '.strtoupper($rekBank).' '.ucwords($atasNama).' Telah Disimpan!');
        }
        else {
            return redirect('CompanyAccount/Add')->with('danger', 'Nomor Rekening '.strtoupper($rekBank).' Telah Tersedia!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
	    	'bank'=>'required',
	    	'rek_bank'=>'required',
	        'nama_bank'=>'required'
        ]);

        $atasNama = strtolower($request->input('nama_bank'));
      	$rekBank = $request->input('rek_bank');
      	$bank = $request->input('bank');
      	$cabang = strtolower($request->input('cabang_bank'));
        $idAccount = $request->input('id_account');
        $user = Auth::user()->user_name;

        $findAccount = CompanyAccount::where([
                                        ['nomor_rekening', '=', $rekBank],
                                        ['id', '!=', $id]
                                    ])->first();
        //dd($findAccount);
        if ($findAccount) {
            return redirect()->back()->with('warning', 'Data Rekening '.strtoupper($rekBank).' Telah Tersedia!');
        }
        else {
            $update = CompanyAccount::find($id);
            $update->nomor_rekening = $rekBank;
            $update->bank = $bank;
            $update->cabang = $cabang;
            $update->atas_nama = $atasNama;
            $update->id_account = $idAccount;
            $update->updated_by = $user;
            $update->save();

            $log = ActionLog::create([
                'module' => 'CompanyAccount',
                'action' => 'Update',
                'desc' => 'Update Account',
                'username' => Auth::user()->user_name
            ]);

            return redirect('CompanyAccount')->with('success', 'Data Rekening '.strtoupper($rekBank).' '.ucwords($atasNama).' Telah Diupdate!');
        }
    }

    public function delete(Request $request)
    {

      	$id = $request->input('id');
      	$user = Auth::user()->user_name;
      	$data = CompanyAccount::find($id);
        $Account = DB::table('company_account')
                        ->where('id', '=', $id)
                        ->update([
                        'deleted_by' => Auth::user()->user_name
                    ]);
        $data->delete();

        $log = ActionLog::create([
            'module' => 'CompanyAccount',
            'action' => 'Delete',
            'desc' => 'Delete Account',
            'username' => Auth::user()->user_name
        ]);

      	if ($data) {
            return response()->json($data);
        }
        else {
            return response()->json($data);
        }
    }
}
