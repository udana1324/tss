<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting\Module;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Accounting\GLAccountLevel;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\GLAccountSettingsDetail;
use App\Models\Accounting\GLMotherAccount;
use App\Models\Accounting\GLSubAccount;
use App\Models\TempTransaction;

class GLAccountSettingsController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLAccountSettings'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();
            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataType = GLAccountLevel::get();

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLAccountSettings'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
                $parentMenu = Module::find($hakAkses->parent);

                $dataSettings = GLAccountSettings::find(1);

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

                $data['hakAkses'] = $hakAkses;
                $data['dataType'] = $dataType;
                $data['dataSettings'] = $dataSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Account Settings',
                    'action' => 'Index',
                    'desc' => 'Tampil Account Settings',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.accounting.gl_account_settings.edit', $data);
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

        $accounts = GLAccountSettings::select(
                                        'gl_account_settings.*',
                                    )
                                    ->get();



        return response()->json($accounts);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLAccountSettings'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->add == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $accounts = GLAccount::orderBy('account_number', 'asc')->get();

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
                $parentMenu = Module::find($hakAkses->parent);

                $data['accounts'] = $accounts;
                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $delete = DB::table('gl_account_settings_detail')->where('id_settings', '=', 'DRAFT')->delete();

                $log = ActionLog::create([
                    'module' => 'Menu',
                    'action' => 'Buat',
                    'desc' => 'Buat Menu Baru',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_account_settings.add', $data);
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
                                                ['module.url', '=', '/GLAccounts'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->edit == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataSettings = GLAccountSettings::find($id);
                $accounts = GLAccount::orderBy('account_number', 'asc')->get();

                $parentAccount = GLAccount::select(
                                                    'gl_account.id',
                                                    'gl_account.account_number',
                                                    'gl_account.account_name',
                                                )
                                        ->where([
                                                    ['gl_account.account_type', '=', '1']
                                                ])
                                        ->orderBy('gl_account.account_number')
                                        ->get();



                foreach ($parentAccount as $dataParent) {
                    $dataParentTemp = GLAccount::select(
                                                    'gl_account.id',
                                                    'gl_account.account_number',
                                                    'gl_account.account_name',
                                                )
                                                ->where([
                                                            ['gl_account.parent', '=', $dataParent->id],
                                                        ])
                                                ->orderBy('gl_account.account_number')
                                                ->get();

                    $dataParent->child = $dataParentTemp;
                }

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'gl_account_settings'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = GLAccountSettingsDetail::where([
                                                    ['id_settings', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'gl_account_settings',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_settings,
                            'value2' => $detail->id_account,
                            'value3' => $detail->module_source,
                            'value4' => $detail->field_source,
                            'value5' => $detail->side,
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $data['dataParent'] = $parentAccount;


                $parentMenu = Module::find($hakAkses->parent);

                $data['accounts'] = $accounts;
                $data['dataSettings'] = $dataSettings;
                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Account Settings',
                    'action' => 'Edit',
                    'desc' => 'Edit Account Settings',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_account_settings.edit', $data);
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
		'module'=>'required',
	    ]);

	    $module = $request->input('module');

        $accountSettings = GLAccountSettings::firstOrCreate(
            ['module' => $module],
            [
                'created_by' => Auth::user()->user_name
            ]
        );

        $setDetail = DB::table('gl_account_settings_detail')
                            ->where([
                                        ['id_settings', '=', 'DRAFT']
                                    ])
                            ->update([
                                'id_settings' => $accountSettings->id,
                                'updated_by' => Auth::user()->user_name
                            ]);

        $log = ActionLog::create([
            'module' => 'GL Account',
            'action' => 'TAMBAH',
            'desc' => 'Tambah Account Baru',
            'username' => Auth::user()->user_name
        ]);

      if ($accountSettings->wasRecentlyCreated) {
        return redirect('GLAccountSettings')->with('success', 'Data Settings '.strtoupper($module).' Telah Disimpan!');
      }
      else {
        return redirect('GLAccountSettings')->with('error', 'Nomor Account '.strtoupper($module).' Telah Tersedia!');
      }
    }

    public function update(Request $request, $id)
    {
	    $akunPenjualan = $request->input('sales_account');
        $akunPembelian = $request->input('purchasing_account');
        $akunPiutang = $request->input('account_receiveable');
        $akunHutang = $request->input('account_payable');
        $akunKas = $request->input('cash_account');
        $ppnMasuk = $request->input('ppn_masuk_account');
        $ppnKeluar = $request->input('ppn_keluar_account');
        $stockAccount = $request->input('stock_account');

        $accountSetting = DB::table('gl_account_settings')
                ->where('id', '=', $id)
                ->update([
                    'id_account_penjualan' => $akunPenjualan,
                    'id_account_pembelian' => $akunPembelian,
                    'id_account_piutang' => $akunPiutang,
                    'id_account_hutang' => $akunHutang,
                    'id_account_kas' => $akunKas,
                    'id_account_pajak_keluar' => $ppnKeluar,
                    'id_account_pajak_masuk' => $ppnMasuk,
                    'id_account_persediaan' => $stockAccount,
                    'updated_by' => Auth::user()->user_name
                ]);


        $log = ActionLog::create([
            'module' => 'GL Account Setting',
            'action' => 'UPDATE',
            'desc' => 'Update Account Setting',
            'username' => Auth::user()->user_name
        ]);

        if ($accountSetting) {
            return redirect('GLAccountSettings')->with('success', 'Data Akun Telah Diupdate!');
        }
        else {
            return redirect('GLAccountSettings')->with('error', 'Update Data Akun Gagal!');
        }
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLAccountSettings'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->delete == "Y") {

                $id = $request->input('id');
                $account = GLAccountSettings::find($id);
                $menu = DB::table('gl_account_settings')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $account->delete();

                $log = ActionLog::create([
                    'module' => 'GL Account Settings',
                    'action' => 'Delete',
                    'desc' => 'Delete Account Settings',
                    'username' => Auth::user()->user_name
                ]);

                if ($account) {
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

    public function StoreDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idSettings');
            $account = $request->input('idAccount');
            $side = $request->input('sisi');
            $source = $request->input('source');
            $field = $request->input('field');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('gl_account_settings_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_settings', '=' , $id],
                                    ['id_account', '=', $account]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new GLAccountSettingsDetail();
                    $listItem->id_settings = $id;
                    $listItem->id_account = $account;
                    $listItem->module_source = $source;
                    $listItem->field_source = $field;
                    $listItem->side = $side;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'GL Account Settings Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan GL Account Settings Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'gl_account_settings'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $account],
                                    ['deleted_at', '=', null]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'gl_account_settings';
                    $listItem->value1 = $id;
                    $listItem->value2 = $account;
                    $listItem->value3 = $source;
                    $listItem->value4 = $field;
                    $listItem->value5 = $side;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'GL Account Settings Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan GL Account Settings Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function UpdateDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $idDetail = $request->input('idDetail');
            $id = $request->input('idSetting');
            $account = $request->input('idAccount');
            $side = $request->input('sisi');
            $source = $request->input('source');
            $field = $request->input('field');

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = GLAccountSettingsDetail::find($idDetail);
                $listItem->id_settings = $id;
                $listItem->id_account = $account;
                $listItem->module_source = $source;
                $listItem->field_source = $field;
                $listItem->side = $side;
                $listItem->updated_by = Auth::user()->user_name;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $account;
                $listItem->value3 = $source;
                $listItem->value4 = $field;
                $listItem->value5 = $side;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'GL Account Settings Detail',
                'action' => 'Update',
                'desc' => 'Update GL Account Settings Detail',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetDetail(Request $request)
    {
        $id = $request->input('id');
        $mode = $request->input('mode');

        if ($mode != 'edit') {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = GLAccountSettingsDetail::leftJoin('gl_account', 'gl_account_settings_detail.id_account', '=', 'gl_account.id')
                                            ->select(
                                                'gl_account_settings_detail.*',
                                                'gl_account.account_number',
                                                'gl_account.account_name'
                                            )
                                            ->where([
                                                ['gl_account_settings_detail.id_settings', '=', $id]
                                            ])
                                            ->orderBy('gl_account.account_number', 'asc')
                                            ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('gl_account', 'temp_transaction.value2', '=', 'gl_account.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'gl_account.account_number',
                                            'gl_account.account_name'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'gl_account_settings']
                                        ])
                                        ->orderBy('gl_account.account_number', 'asc')
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = GLAccountSettingsDetail::leftJoin('gl_account', 'gl_account_settings_detail.id_account', '=', 'gl_account.id')
                                            ->select(
                                                'gl_account_settings_detail.*',
                                                'gl_account.account_number',
                                                'gl_account.account_name'
                                            )
                                            ->where([
                                                ['gl_account_settings_detail.id_settings', '=', $id]
                                            ])
                                            ->get();
        }
        else {

            $detail = TempTransaction::leftJoin('gl_account', 'temp_transaction.value2', '=', 'gl_account.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'gl_account.account_number',
                                            'gl_account.account_name'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'gl_account_settings']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            if ($mode != "") {
                $detail = TempTransaction::find($id);
                $detail->deleted_by = Auth::user()->user_name;
                $detail->action = "hapus";
                $detail->save();

                $detail->delete();
            }
            else {
                $delete = DB::table('gl_account_settings_detail')->where('id', '=', $id)->delete();
            }


        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function getSubAccount(Request $request)
    {
        $idAccount = $request->input('id_account');

        if ($idAccount != null || $idAccount != "") {
            $subAccount = GLSubAccount::select(
                                    'id',
                                    'account_name',
                                    'account_number'
                                )
                                ->where([
                                 ['id_account', '=', $idAccount]
                                ])
                        ->get();
        }
        else {
            $subAccount = GLSubAccount::select(
                                    'id',
                                    'account_name',
                                    'account_number'
                                )
                                // ->where([
                                //  ['id_account', '=', $idAccount]
                                // ])
                        ->get();
        }

        return response()->json($subAccount);
    }
}
