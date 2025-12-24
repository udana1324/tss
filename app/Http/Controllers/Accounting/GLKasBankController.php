<?php

namespace App\Http\Controllers\Accounting;

use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperAccounting;
use App\Classes\BusinessManagement\HelperKasBank;
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
use App\Exports\ExportEntryKasBank;
use App\Models\Accounting\GLAccountLevel;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLJournal;
use App\Models\Accounting\GLKasBank;
use App\Models\Accounting\GLKasBankDetail;
use App\Models\Accounting\GLKasBankTerms;
use App\Models\Accounting\GLSubAccount;
use App\Models\Setting\Preference;
use App\Models\TempTransaction;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class GLKasBankController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            if (str_contains(url()->current(), "/GLKasBank/Kas")) {
                $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLKasBank/Kas'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {
                $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GLKasBank/Bank'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $parentMenu = Module::find($hakAkses->parent);
                $dataStatus = GLKasBank::distinct()->get('status');

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;


                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Account',
                    'action' => 'Index',
                    'desc' => 'Tampil GL Account',
                    'username' => Auth::user()->user_name
                ]);

                if ($hakAkses->menu == "Kas") {
                    $log = ActionLog::create([
                        'module' => 'GL Kas',
                        'action' => 'Index',
                        'desc' => 'Tampil GL Kas',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.kas.index', $data);
                }
                else {
                    $log = ActionLog::create([
                        'module' => 'GL Bank',
                        'action' => 'Index',
                        'desc' => 'Tampil GL Bank',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.bank.index', $data);
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

    public function getDataIndex(Request $request)
    {
        $periode = $request->input('periode');
        $jenis = $request->input('jenis');
        $kasBank = $jenis == "kas" ? 1 : 2;

        $accounts = GLKasBank::when($periode != "", function($q) use ($periode) {
                            $q->whereMonth('gl_kas_bank.tanggal_transaksi', Carbon::parse($periode)->format('m'));
                            $q->whereYear('gl_kas_bank.tanggal_transaksi', Carbon::parse($periode)->format('Y'));
                        })
                        ->where([
                            ['gl_kas_bank.id_account', '=', $kasBank],
                            // ['gl_kas_bank.jenis', '=', 'input'],
                        ])
                        ->orderBy('gl_kas_bank.tanggal_transaksi', 'desc')
                        ->orderBy('gl_kas_bank.id', 'desc')
                        ->get();



        return response()->json($accounts);
    }

    public function createKas()
    {
        if (Auth::check()) {

            if (str_contains(url()->current(), "/GLKasBank/Kas")) {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            if ($hakAkses->add == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $parentMenu = Module::find($hakAkses->parent);


                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $delete = DB::table('gl_kas_bank_detail')
                            ->where([
                                ['id_kas_bank', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                $log = ActionLog::create([
                    'module' => 'KasBank',
                    'action' => 'Buat',
                    'desc' => 'Entri Kas',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_kas_bank.kas.add', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function createBank()
    {
        if (Auth::check()) {

            if (str_contains(url()->current(), "/GLKasBank/Kas")) {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            if ($hakAkses->add == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $parentMenu = Module::find($hakAkses->parent);


                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $delete = DB::table('gl_kas_bank_detail')
                            ->where([
                                ['id_kas_bank', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                $log = ActionLog::create([
                    'module' => 'KasBank',
                    'action' => 'Buat',
                    'desc' => 'Entri Bank',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_kas_bank.bank.add', $data);
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
            'id_account'=> 'required',
            'id_account_sub'=> 'required',
            'jenis_transaksi'=> 'required',
            'tanggal_transaksi'=> 'required'
        ]);

        $tgl = $request->input('tanggal_transaksi');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/GLKasBank')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $accID = $request->input('id_account');
            $accSubID = $request->input('id_account_sub');
            $jenisTransaksi = $request->input('jenis_transaksi');
            $nominalEntry = $request->input('total');
            $tglTransaksi = $request->input('tanggal_transaksi');
            $user = Auth::user()->user_name;

            $blnPeriode = date("m", strtotime($tglTransaksi));
            $thnPeriode = date("Y", strtotime($tglTransaksi));

            $countKode = DB::table('gl_kas_bank')
                        ->select(DB::raw("MAX(RIGHT(nomor_kas_bank,2)) AS angka"))
                        //->whereYear('tanggal_transaksi', $thnPeriode)
                        ->whereDate('tanggal_transaksi', $tglTransaksi)
                        ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglTransaksi)->format('ymd');

            if ($counter < 10) {
                $nmrKB = "kb-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrKB = "kb-cv-".$kodeTgl.$counter;
            }


            $kasBank = new GLKasBank();
            $kasBank->nomor_kas_bank = $nmrKB;
            $kasBank->id_account = $accID;
            $kasBank->id_account_sub = $accSubID;
            $kasBank->jenis_transaksi = $jenisTransaksi;
            $kasBank->nominal_transaksi = $nominalEntry;
            $kasBank->tanggal_transaksi = $tglTransaksi;
            $kasBank->status = 'draft';
            $kasBank->jenis = 'input';
            $kasBank->created_by = $user;
            $kasBank->save();

            $data = $kasBank;

            $setDetail = DB::table('gl_kas_bank_detail')
                            ->where([
                                        ['id_kas_bank', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_kas_bank' => $kasBank->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'GL Kas Bank',
                'action' => 'Simpan',
                'desc' => 'Simpan GL Kas Bank',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('GLKasBank.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->nomor_kas_bank).' Telah Disimpan!');
        }
        else {
            return redirect('/GLKasBank')->with('error', $exception);
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $dataKasBank = GLKasBank::leftJoin('gl_account', 'gl_kas_bank.id_account', '=', 'gl_account.id')
                                    ->select(
                                        'gl_kas_bank.*',
                                        'gl_account.account_name',
                                        'gl_account.account_number'
                                    )
                                    ->where([
                                        ['gl_kas_bank.id', '=', $id],
                                    ])
                                    ->first();

            if ($dataKasBank->id_account == 1) {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            if ($hakAkses->edit == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'gl_kas_bank'],
                                    ['value1', '=', $id]
                                ])->delete();

                $dataDetail = GLKasBankDetail::where([
                                                    ['id_kas_bank', '=', $id]
                                                ])
                                                ->get();


                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'gl_kas_bank',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_kas_bank,
                            'value2' => $detail->id_account,
                            'value3' => $detail->nominal,
                            'value4' => $detail->keterangan
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataKasBank'] = $dataKasBank;

                if ($dataKasBank->id_account == 1) {
                    $log = ActionLog::create([
                        'module' => 'GL Kas',
                        'action' => 'edit',
                        'desc' => 'edit GL Kas',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.kas.edit', $data);
                }
                else {
                    $log = ActionLog::create([
                        'module' => 'GL Bank',
                        'action' => 'Edit',
                        'desc' => 'Edit GL Bank',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.bank.edit', $data);
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

    public function update(Request $request, $id)
    {
	    $request->validate([
            'id_account'=> 'required',
            'id_account_sub'=> 'required',
            'jenis_transaksi'=> 'required',
            'tanggal_transaksi'=> 'required'
        ]);

        $tgl = $request->input('tanggal_transaksi');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/GLKasBank')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $accID = $request->input('id_account');
            $accSubID = $request->input('id_account_sub');
            $jenisTransaksi = $request->input('jenis_transaksi');
            $nominalEntry = $request->input('total');
            $tglTransaksi = $request->input('tanggal_transaksi');
            $user = Auth::user()->user_name;

            $kasBank = GLKasBank::find($id);
            $kasBank->id_account = $accID;
            $kasBank->id_account_sub = $accSubID;
            $kasBank->jenis_transaksi = $jenisTransaksi;
            $kasBank->nominal_transaksi = $nominalEntry;
            $kasBank->tanggal_transaksi = $tglTransaksi;
            $kasBank->jenis = 'input';
            $kasBank->updated_by = $user;
            $kasBank->save();

            $data = $kasBank;

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'gl_kas_bank'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();


            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = GLKasBankDetail::find($detail->id_detail);
                        $listItem->id_kas_bank = $detail->value1;
                        $listItem->id_account = $detail->value2;
                        $listItem->nominal = $detail->value3;
                        $listItem->keterangan = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new GLKasBankDetail();
                        $listItem->id_kas_bank = $detail->value1;
                        $listItem->id_account = $detail->value2;
                        $listItem->nominal = $detail->value3;
                        $listItem->keterangan = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('gl_kas_bank_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'gl_kas_bank'],
                                    ['value1', '=', $id]
                                ])->delete();

            $log = ActionLog::create([
                'module' => 'GL Kas Bank',
                'action' => 'Update',
                'desc' => 'Update GL Kas Bank',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('GLKasBank.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->nomor_kas_bank).' Telah Disimpan!');
        }
        else {
            return redirect('/GLKasBank')->with('error', $exception);
        }
    }

    public function delete(Request $request)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

            if ($hakAkses->delete == "Y") {

                $id = $request->input('id');
                $account = GLKasBank::find($id);
                $menu = DB::table('gl_account')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $account->delete();

                $log = ActionLog::create([
                    'module' => 'GL Account',
                    'action' => 'Delete',
                    'desc' => 'Delete Account',
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
            $id = $request->input('idKasBank');
            $account = $request->input('idAccount');
            $nominal = $request->input('nominal');
            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('gl_kas_bank_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_kas_bank', '=' , $id],
                                    ['id_account', '=', $account],
                                    ['keterangan', '=', $keterangan],
                                    ['created_by', '=', Auth::user()->user_name]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new GLKasBankDetail();
                    $listItem->id_kas_bank = $id;
                    $listItem->id_account = $account;
                    $listItem->nominal = $nominal;
                    $listItem->keterangan = $keterangan;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'GL Kas Bank Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan GL Kas Bank Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'gl_kas_bank'],
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
                    $listItem->module = 'gl_kas_bank';
                    $listItem->value1 = $id;
                    $listItem->value2 = $account;
                    $listItem->value3 = $nominal;
                    $listItem->value4 = $keterangan;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'GL Kas Bank Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan GL Kas Bank Detail',
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
            $id = $request->input('idKasBank');
            $account = $request->input('idAccount');
            $nominal = $request->input('nominal');
            $keterangan = $request->input('keterangan');

            $dataAccount = GLSubAccount::leftJoin('gl_account', 'gl_sub_account.id_account', '=', 'gl_account.id')
                                        ->leftJoin('gl_mother_account', 'gl_account.id_mother_account', '=', 'gl_mother_account.id')
                                        ->first();


            if ($id == "") {
                $id = 'DRAFT';

                $listItem = GLKasBankDetail::find($idDetail);
                $listItem->id_kas_bank = $id;
                $listItem->id_account = $account;
                $listItem->nominal = $nominal;
                $listItem->side = $dataAccount->default_side;
                $listItem->keterangan = $keterangan;
                $listItem->updated_by = Auth::user()->user_name;
                $listItem->save();
            }
            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $account;
                $listItem->value3 = $nominal;
                $listItem->value4 = $keterangan;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'GL Kas Bank Detail',
                'action' => 'Update',
                'desc' => 'Update GL Kas Bank Detail',
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
        $id = $request->input('idKasBank');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != 'edit') {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = GLKasBankDetail::leftJoin('gl_sub_account', 'gl_kas_bank_detail.id_account', '=', 'gl_sub_account.id')
                                            ->select(
                                                'gl_kas_bank_detail.id',
                                                'gl_kas_bank_detail.nominal',
                                                'gl_kas_bank_detail.keterangan',
                                                'gl_sub_account.account_number',
                                                'gl_sub_account.account_name'
                                            )
                                            ->where([
                                                ['gl_kas_bank_detail.id_kas_bank', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('gl_kas_bank_detail.created_by', $user);
                                            })
                                            ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('gl_sub_account', 'temp_transaction.value2', '=', 'gl_sub_account.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'gl_sub_account.account_number',
                                            'gl_sub_account.account_name'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'gl_kas_bank']
                                        ])
                                        ->orderBy('temp_transaction.value1')
                                        ->get();
        }

        return response()->json($detail);
    }

    public function GetFooter(Request $request)
    {
        $id = $request->input('idKasBank');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != 'edit') {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = GLKasBankDetail::select(
                                        DB::raw('COALESCE(SUM(gl_kas_bank_detail.nominal),0) AS nominal')
                                    )
                                    ->where([
                                        ['gl_kas_bank_detail.id_kas_bank', '=', $id]
                                    ])
                                    ->when($id == "DRAFT", function($q) use ($user) {
                                        $q->where('gl_kas_bank_detail.created_by', $user);
                                    })
                                    ->groupBy('gl_kas_bank_detail.id_kas_bank', 'gl_kas_bank_detail.created_by')
                                    ->first();
        }
        else {
            $detail = TempTransaction::select(
                                        DB::raw('COALESCE(SUM(temp_transaction.value3),0) AS nominal')
                                    )
                                    ->where([
                                        ['temp_transaction.value1', '=', $id],
                                        ['temp_transaction.module', '=', 'gl_kas_bank']
                                    ])
                                    ->groupBy('temp_transaction.value1')
                                    ->first();
        }

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function EditDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = GLKasBankDetail::leftJoin('gl_account', 'gl_kas_bank_detail.id_account', '=', 'gl_account.id')
                                            ->select(
                                                'gl_kas_bank_detail.*',
                                                'gl_account.account_number',
                                                'gl_account.account_name'
                                            )
                                            ->where([
                                                ['gl_kas_bank_detail.id', '=', $id]
                                            ])
                                            ->first();
        }
        else {

            $detail = TempTransaction::leftJoin('gl_account', 'temp_transaction.value2', '=', 'gl_account.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value1',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'gl_account.account_number',
                                            'gl_account.account_name'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'gl_kas_bank']
                                        ])
                                        ->first();
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
                $delete = DB::table('gl_kas_bank_detail')->where('id', '=', $id)->delete();
            }


        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function detail($id)
    {
        if (Auth::check()) {
            $dataKasBank = GLKasBank::leftJoin('gl_sub_account', 'gl_kas_bank.id_account_sub', '=', 'gl_sub_account.id')
                                    ->select(
                                        'gl_kas_bank.*',
                                        'gl_sub_account.account_name',
                                        'gl_sub_account.account_number'
                                    )
                                    ->where([
                                        ['gl_kas_bank.id', '=', $id],
                                    ])
                                    ->first();

            if ($dataKasBank->id_account == 1) {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataKasBank'] = $dataKasBank;

                if ($dataKasBank->id_account == 1) {
                    $log = ActionLog::create([
                        'module' => 'GL Kas',
                        'action' => 'Detail',
                        'desc' => 'Detail GL Kas',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.kas.detail', $data);
                }
                else {
                    $log = ActionLog::create([
                        'module' => 'GL Bank',
                        'action' => 'Detail',
                        'desc' => 'Detail GL Bank',
                        'username' => Auth::user()->user_name
                    ]);

                    return view('pages.accounting.gl_kas_bank.bank.detail', $data);
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

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $jenisEntri = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status, &$jenisEntri) {
            $btnAction = $request->input('submit_action');
            $kasBank = GLKasBank::find($id);
            $jenisEntri = $kasBank->id_account;

            if ($btnAction == "posting") {

                $postJournal = HelperAccounting::PostJournalKasBank($kasBank->id, Auth::user()->user_name);
                $kasBank->flag_entry = '1';
                $kasBank->status = "posted";
                $kasBank->save();

                $log = ActionLog::create([
                    'module' => 'GLKasBank',
                    'action' => 'Posting',
                    'desc' => 'Posting GLKasBank',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($kasBank->nomor_kas_bank).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "postingEntry") {

                $postJournal = HelperAccounting::PostJournalKasBank($kasBank->id, Auth::user()->user_name);
                $kasBank->flag_entry = '1';
                $kasBank->status = "posted";
                $kasBank->save();

                $log = ActionLog::create([
                    'module' => 'GLKasBank',
                    'action' => 'Posting',
                    'desc' => 'Posting GLKasBank',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($kasBank->nomor_kas_bank).' Telah Diposting!';
                $status = 'postingEntry';
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($kasBank->jenis != 'input') {
                    $msg = 'Entry Kas/Bank '.strtoupper($kasBank->nomor_kas_bank).' Tidak dapat Direvisi karena diinput oleh sistem!';
                    $status = "warning";
                }
                else {
                    $akun = $kasBank->id_account == 1 ? "kas" : "bank";
                    $removeGLJournal = HelperAccounting::RemoveJournalKasBank($kasBank->id);
                    $kasBank->status = "draft";
                    $kasBank->flag_revisi = '1';
                    $kasBank->flag_entry = '0';
                    $kasBank->updated_by = Auth::user()->user_name;
                    $kasBank->save();

                    $log = ActionLog::create([
                        'module' => 'GL Kas Bank',
                        'action' => 'Revisi',
                        'desc' => 'Revisi GL Kas Bank',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Entry Kas Bank '.strtoupper($kasBank->nomor_kas_bank).' Telah Direvisi!';
                    $status = 'success';
                }
                // $delete = DB::table('gl_journal')->where([
                //                                     ['id_sumber', '=', $id],
                //                                     ['sumber', '=', 'gl_kas_bank']
                //                                 ])
                //                                 ->delete();
            }
            elseif ($btnAction == "batal") {
                // $delete = DB::table('gl_journal')->where([
                //     ['id_sumber', '=', $id],
                //     ['sumber', '=', 'gl_kas_bank']
                // ])
                // ->delete();

                $kasBank->status = "batal";
                $kasBank->updated_by = Auth::user()->user_name;
                $kasBank->save();

                $log = ActionLog::create([
                    'module' => 'Gl Kas Bank',
                    'action' => 'Batal',
                    'desc' => 'Batal Gl Kas Bank',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Kas Bank '.strtoupper($kasBank->nomor_kas_bank).' Telah Dibatalkan!';
                $status = "success";
            }
        });


        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('GLKasBank.edit', [$id]);
            }
            elseif ($status == "postingEntry") {
                if ($jenisEntri == "1") {
                    return redirect('GLKasBank/Kas/Add')->with("success", $msg);
                }
                else {
                    return redirect('GLKasBank/Bank/Add')->with("success", $msg);
                }

            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
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

    public function cetak($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $dataKasBank = GLKasBank::leftJoin('gl_sub_account', 'gl_kas_bank.id_account_sub', '=', 'gl_sub_account.id')
                                    ->select(
                                        'gl_kas_bank.*',
                                        'gl_sub_account.account_name',
                                        'gl_sub_account.account_number'
                                    )
                                    ->where([
                                        ['gl_kas_bank.id', '=', $id],
                                    ])
                                    ->first();

            if ($dataKasBank->id_account == 1) {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Kas'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }
            else {

                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLKasBank/Bank'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();
            }

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();


                $dataPreference = Preference::leftJoin('company_account', 'preference.rekening', '=', 'company_account.id')
                                            ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                            ->select(
                                                'bank.kode_bank',
                                                'bank.nama_bank',
                                                'company_account.nomor_rekening',
                                                'company_account.cabang',
                                                'company_account.atas_nama',
                                                'preference.*'
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();

                $detailKasBank = GLKasBankDetail::leftJoin('gl_sub_account', 'gl_kas_bank_detail.id_account', 'gl_sub_account.id')
                                                ->select(
                                                    'gl_sub_account.account_name',
                                                    'gl_sub_account.account_number',
                                                    'gl_kas_bank_detail.nominal',
                                                    'gl_kas_bank_detail.keterangan'
                                                )
                                                ->where([
                                                    ['gl_kas_bank_detail.id_kas_bank', '=', $dataKasBank->id]
                                                ])
                                                ->get();

                $data['dataKasBank'] = $dataKasBank;
                // $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                // $data['dataAlamat'] = $dataAlamat;
                $data['detailkasBank'] = $detailKasBank;
                // $data['dataSales'] = $dataSales;

                $log = ActionLog::create([
                    'module' => 'Kas Bank',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Kas Bank',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperKasBank::cetakPdf($data);

                $fpdf->Output('I', strtoupper(str_replace("/", "_", $dataKasBank->nomor_kas_bank)).".pdf");
                exit;
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function export(Request $request)
    {
        $periode = $request->input('bulan_picker_val');
        $jenis = $request->input('jenis');
        $periodeBulan = Carbon::parse($periode)->isoFormat('MMM');
        $periodeTahun = Carbon::parse($periode)->isoFormat('Y');
        return Excel::download(new ExportEntryKasBank($request), 'BukuBesar_'.$jenis.'_'.$periodeBulan.'_'.$periodeTahun.'.xlsx');
    }
}
