<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperAccounting;
use App\Classes\BusinessManagement\HelperSalesTaxInvoice;
use App\Exports\ExportBalanceSheet;
use App\Exports\ExportFakturPajak;
use App\Exports\ExportGeneralLedger;
use App\Exports\ExportTrialBalance;
use App\Exports\GeneralLedgerExport;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\GLAccountSettingsDetail;
use App\Models\Accounting\GLJournal;
use App\Models\Accounting\GLJournalDetail;
use App\Models\Accounting\GLSubAccount;
use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\TaxSerialNumber;
use App\Models\Accounting\TaxSettings;
use App\Models\Library\Customer;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\Setting\Preference;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class GLJournalController extends Controller
{
    public function index() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLJournal'],
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
                                                ['module.url', '=', '/GLJournal'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;

                $parentMenu = Module::find($hakAkses->parent);

                $dataStatus = GLJournal::distinct()->get('status');

                $data['dataStatus'] = $dataStatus;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Journal',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan GL Journal',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_journal.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexGenerate()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GLJournal'],
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
                                                ['module.url', '=', '/GLJournal'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $settings = GLAccountSettings::all();

                $data['hakAkses'] = $hakAkses;
                $data['accountSetting'] = $settings;
                $data['taxSettings'] = $taxSettings;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Generate Jurnal',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Generate Jurnal',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_journal.generate', $data);
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


        $accounts = GLJournal::when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('gl_journal.tanggal_transaksi', Carbon::parse($periode)->format('m'));
                                $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($periode)->format('Y'));
                            })
                            ->when($periode == "", function($q) use ($periode) {
                                $q->where('gl_journal.tanggal_transaksi', '>=', Carbon::now()->subMonth(3)->startOfMonth()->format('Y-m-d'));
                            })
                            ->orderBy('gl_journal.tanggal_transaksi', 'desc')
                            ->orderBy('gl_journal.id', 'desc')
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
                                            ['module.url', '=', '/TaxSerialNumber'],
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

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Nomor Seri Faktur Pajak',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Nomor Seri Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.add', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/TaxSerialNumber'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSerialNumber = TaxSerialNumber::find($id);

                $data['hakAkses'] = $hakAkses;
                $data['dataSerialNumber'] = $dataSerialNumber;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.edit', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/TaxSerialNumber'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSerialNumber = TaxSerialNumber::find($id);

                $exportedFP = SalesTaxInvoice::where([
                    ['id_seri', '=', $id],
                    ['flag_export', '=', '1']
                ])
                ->count();

                $data['hakAkses'] = $hakAkses;
                $data['dataSerialNumber'] = $dataSerialNumber;
                $data['exportedFP'] = $exportedFP;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Tampilan',
                    'desc' => 'Tampilan Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.detail', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $request->validate([
		    'no_permohonan'=>'required',
	        'tanggal_permohonan'=>'required',
            'no_pemberitahuan'=>'required',
	        'tanggal_pemberitahuan'=>'required',
            'tahun_pajak'=>'required',
	        'jumlah_seri'=>'required',
            'seri_awal'=>'required',
	        'seri_akhir'=>'required'
	    ]);

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $noPermohonan = $request->input('no_permohonan');
            $tglPermohonan = $request->input('tanggal_permohonan');
            $noPemberitahuan = $request->input('no_pemberitahuan');
            $tglPemberitahuan = $request->input('tanggal_pemberitahuan');
            $thnPajak = $request->input('tahun_pajak');
            $jmlSeri = $request->input('jumlah_seri');
            $awal = strtolower($request->input('seri_awal'));
            $akhir = $request->input('seri_akhir');
            $user = Auth::user()->user_name;

            $taxSerialNumber = new TaxSerialNumber();
            $taxSerialNumber->no_permohonan = $noPermohonan;
            $taxSerialNumber->tanggal_permohonan = $tglPermohonan;
            $taxSerialNumber->no_pemberitahuan_djp = $noPemberitahuan;
            $taxSerialNumber->tanggal_pemberitahuan_djp = $tglPemberitahuan;
            $taxSerialNumber->tahun_berlaku_seri = $thnPajak;
            $taxSerialNumber->jumlah_no_seri = $jmlSeri;
            $taxSerialNumber->nomor_seri_dari = $awal;
            $taxSerialNumber->nomor_seri_sampai = $akhir;
            $taxSerialNumber->status = 'draft';
            $taxSerialNumber->created_by = $user;
            $taxSerialNumber->save();

            $data = $taxSerialNumber;

            $log = ActionLog::create([
                'module' => 'Tax Serial Number',
                'action' => 'Simpan',
                'desc' => 'Simpan Tax Serial Number',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('TaxSerialNumber.Detail', [$data->id])->with('success', 'Nomor Seri '.strtoupper($data->no_pemberitahuan_djp).' Telah Disimpan!');
        }
        else {
            return redirect('/TaxSerialNumber')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
		'no_permohonan'=>'required',
	        'tanggal_permohonan'=>'required',
            'no_pemberitahuan'=>'required',
	        'tanggal_pemberitahuan'=>'required',
            'tahun_pajak'=>'required',
	        'jumlah_seri'=>'required',
            'seri_awal'=>'required',
	        'seri_akhir'=>'required'
	    ]);

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $noPermohonan = $request->input('no_permohonan');
            $tglPermohonan = $request->input('tanggal_permohonan');
            $noPemberitahuan = $request->input('no_pemberitahuan');
            $tglPemberitahuan = $request->input('tanggal_pemberitahuan');
            $thnPajak = $request->input('tahun_pajak');
            $jmlSeri = $request->input('jumlah_seri');
            $awal = strtolower($request->input('seri_awal'));
            $akhir = $request->input('seri_akhir');
            $user = Auth::user()->user_name;

            $taxSerialNumber = TaxSerialNumber::find($id);
            $taxSerialNumber->no_permohonan = $noPermohonan;
            $taxSerialNumber->tanggal_permohonan = $tglPermohonan;
            $taxSerialNumber->no_pemberitahuan_djp = $noPemberitahuan;
            $taxSerialNumber->tanggal_pemberitahuan_djp = $tglPemberitahuan;
            $taxSerialNumber->tahun_berlaku_seri = $thnPajak;
            $taxSerialNumber->jumlah_no_seri = $jmlSeri;
            $taxSerialNumber->nomor_seri_dari = $awal;
            $taxSerialNumber->nomor_seri_sampai = $akhir;
            $taxSerialNumber->created_by = $user;
            $taxSerialNumber->save();

            $data = $taxSerialNumber;

            $log = ActionLog::create([
                'module' => 'Tax Serial Number',
                'action' => 'Ubah',
                'desc' => 'Ubah Tax Serial Number',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('TaxSerialNumber.Detail', [$data->id])->with('success', 'Nomor Seri '.strtoupper($data->no_pemberitahuan_djp).' Telah Diupdate!');
        }
        else {
            return redirect('/TaxSerialNumber')->with('error', $exception);
        }
    }

    public function delete(Request $request)
    {

        $id = $request->input('id_sei');
        $user = Auth::user()->user_name;
        $data = TaxSerialNumber::find($id);
        $bank = DB::table('tax_serial_number')
                    ->where('id', '=', $id)
                    ->update([
                        'deleted_by' => Auth::user()->user_name
                    ]);
        $data->delete();

        $log = ActionLog::create([
            'module' => 'Tax Serial Number',
            'action' => 'Delete',
            'desc' => 'Delete Tax Serial Number',
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

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $taxSerialNumber = TaxSerialNumber::find($id);
            $data = $taxSerialNumber;

            $cekExported = SalesTaxInvoice::where([
                ['id_seri', '=', $id],
                ['flag_export', '=', '1']
            ])
            ->count();

            if ($btnAction == "posting") {
                $taxSerialNumber->status = "posted";
                $taxSerialNumber->save();
                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Posting',
                    'desc' => 'Posting Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "tutup") {
                $taxSerialNumber->status = "close";
                $taxSerialNumber->updated_by = Auth::user()->user_name;
                $taxSerialNumber->save();

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Tutup',
                    'desc' => 'Tutup Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Ditutup!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                if ($cekExported > 0) {
                    $taxSerialNumber->status = "batal";
                    $taxSerialNumber->updated_by = Auth::user()->user_name;
                    $taxSerialNumber->save();

                    $log = ActionLog::create([
                        'module' => 'Tax Serial Number',
                        'action' => 'Batal',
                        'desc' => 'Batal Tax Serial Number',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Dibatalkan!';
                    $status = 'success';
                }
                else {
                    $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Tidak dapat Dibatalkan karena terdapat Surat Jalan Faktur pajak yang sudah di export!';
                    $status = 'warning';
                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('TaxSerialNumber.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ExecuteGenerate(Request $request)
    {
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $idSetting = $request->input('id_setting');

        $failMsg = "";
        $successCount = 0;
        $returnData = [];
        $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

        $result = HelperAccounting::AutoGenerateJournal($request);

        if ($idSetting != null || $idSetting != "") {
            if ($result['text'] == "success" && count($result["idSumber"]) > 0) {

                $successCount = $successCount + 1;

                $setting = GLAccountSettingsDetail::where([
                                ['id_settings', '=', $idSetting]
                            ])
                            ->get();
                foreach ($setting as $detail) {

                    if ($detail->module_source == "penjualan") {
                        DB::table('sales_invoice')
                        ->whereIn('sales_invoice.id', $result['idSumber'])
                        ->update([
                            'flag_entry' => 1,
                            'updated_by' => Auth::user()->user_name
                        ]);
                    }
                    elseif ($detail->module_source == "pembelian") {
                        DB::table('purchase_invoice')
                        ->whereIn('purchase_invoice.id', $result['idSumber'])
                        ->update([
                            'flag_entry' => 1,
                            'updated_by' => Auth::user()->user_name
                        ]);
                    }
                    elseif ($detail->module_source == "pemasukan") {
                        DB::table('account_receiveable_detail')
                        ->whereIn('account_receiveable_detail.id_ar', $result['idSumber'])
                        ->update([
                            'flag_entry' => 1,
                            'updated_by' => Auth::user()->user_name
                        ]);
                    }
                    elseif ($detail->module_source == "pengeluaran") {
                        DB::table('account_payable_detail')
                        ->whereIn('account_payable_detail.id_ap', $result['idSumber'])
                        ->update([
                            'flag_entry' => 1,
                            'updated_by' => Auth::user()->user_name
                        ]);
                    }
                }
            }
            elseif (count($result["idSumber"]) < 1) {
                $failMsg = "Tidak terdapat data jurnal yang digenerate!";
            }
            else if ($result['text'] == "failNoSetting") {
                $failMsg = "Tidak terdapat account setting yang dapat digunakan!";
            }


            if ($failMsg != "") {
                $fail = ["failMsg" => $failMsg];
                array_push($returnData,$fail);
            }
            else {
                $fail = ["failMsg" => "noFail"];
                array_push($returnData,$fail);
            }

            if ($successCount > 0) {

                $dataJournal = GLJournal::leftJoin('gl_account', 'gl_journal.id_account', 'gl_account.id')
                                        ->select(
                                            'gl_journal.*',
                                            'gl_account.account_number',
                                            'gl_account.account_name',
                                        )
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('gl_journal.tanggal_transaksi', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->orderBy('gl_journal.tanggal_transaksi')
                                        ->orderBy('gl_account.account_number')
                                        ->get();

                $data = ["dataJournal" => $dataJournal];
                array_push($returnData, $data);

            }
            else {
                $data = ["dataJournal" => null];
                array_push($returnData, $data);
            }

            return response()->json($returnData);
        }
        else {
            $failMsg = "Tidak terdapat account setting yang dapat digunakan!";
            $fail = ["failMsg" => $failMsg];
            array_push($returnData,$fail);
            return response()->json($returnData);
        }
    }

    public function exportDataFP(Request $request)
    {
        $periode = $request->input('bulan_picker_val');
        $periodeBulan = Carbon::parse($periode)->isoFormat('MMM');
        $periodeTahun = Carbon::parse($periode)->isoFormat('Y');
        return Excel::download(new ExportFakturPajak($request), 'FakturPajak_'.$periodeBulan.'_'.$periodeTahun.'.xlsx');
    }

    public function indexGL() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/GeneralLedger'],
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
                                                ['module.url', '=', '/GeneralLedger'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $subAccount = GLSubAccount::select(
                                                    'gl_sub_account.id',
                                                    'gl_sub_account.account_number',
                                                    'gl_sub_account.account_name',
                                                )
                                        ->orderBy('gl_sub_account.account_number')
                                        ->get();

                $account = GLAccount::select(
                                                    'gl_account.id',
                                                    'gl_account.account_number',
                                                    'gl_account.account_name',
                                                )
                                        ->orderBy('gl_account.account_number')
                                        ->get();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;
                $data['subAccounts'] = $subAccount;
                $data['accounts'] = $account;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'GL Journal',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan GL Journal',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_journal.reportGeneralLedger', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getGeneralLedger(Request $request)
    {
        $id = $request->input('idAccount');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $user = Auth::user()->user_name;

        $transaction = "";

        if ($jenisPeriode != null) {
            $detailDebet = GLJournal::leftJoin('gl_sub_account', 'gl_journal.id_account', '=', 'gl_sub_account.id')
                                    ->select(
                                        'gl_journal.id',
                                        DB::raw("gl_journal.nominal as 'debet'"),
                                        DB::raw("'-' as 'kredit'"),
                                        'gl_journal.deskripsi',
                                        'gl_journal.tanggal_transaksi',
                                        'gl_sub_account.account_number',
                                        'gl_sub_account.account_name'
                                    )
                                    ->where([
                                        ['gl_journal.side', '=', 'debet'],
                                        ['gl_journal.id_account', '=', $id]
                                    ])->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                        $q->whereBetween('gl_journal.tanggal_transaksi', [$tglStart, $tglEnd]);
                                    })
                                    ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                        $q->whereMonth('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                        $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                                    })
                                    ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                        $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($tahun)->format('Y'));
                                    });

            $transaction = GLJournal::leftJoin('gl_sub_account', 'gl_journal.id_account', '=', 'gl_sub_account.id')
                                ->select(
                                    'gl_journal.id',
                                    DB::raw("'-' as 'debet'"),
                                    DB::raw("gl_journal.nominal as 'kredit'"),
                                    'gl_journal.deskripsi',
                                    'gl_journal.tanggal_transaksi',
                                    'gl_sub_account.account_number',
                                    'gl_sub_account.account_name'
                                )
                                ->where([
                                    ['gl_journal.side', '=', 'credit'],
                                    ['gl_journal.id_account', '=', $id]
                                ])
                                ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                    $q->whereBetween('gl_journal.tanggal_transaksi', [$tglStart, $tglEnd]);
                                })
                                ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                    $q->whereMonth('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                    $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                                })
                                ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                    $q->whereYear('gl_journal.tanggal_transaksi', Carbon::parse($tahun)->format('Y'));
                                })
                                ->orderBy('gl_journal.tanggal_transaksi', 'asc')
                                ->union($detailDebet)
                                ->get();
        }

        return response()->json($transaction);
    }

    public function exportGL(Request $request)
    {
        $id = $request->input('account');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tanggal_picker_start');
        $tglEnd = $request->input('tanggal_picker_end');
        $bulan = $request->input('bulan_picker_val');
        $tahun = $request->input('tahun_picker_val');
        $format = $request->input('format');

        $account = GLAccount::find($id);
        if ($jenisPeriode == "harian") {
            $date = Carbon::parse($tglStart)->subDay(1)->format('Y-m-d');
        }
        elseif ($jenisPeriode == "bulanan") {
            $date = Carbon::parse($bulan)->subMonth()->lastOfMonth()->format('Y-m-d');
        }
        else {
            $date = Carbon::parse($tahun)->subYear()->endOfYear()->format('Y-m-d');
        }

        $previousJournalEntries = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                        ->leftJoin('gl_journal', 'gl_journal_detail.id_journal', '=',  'gl_journal.id')
                                        ->select(
                                            'gl_journal_detail.tanggal_transaksi',
                                            'gl_journal_detail.nominal',
                                            'gl_journal_detail.side',
                                            'gl_journal_detail.sumber',
                                            'gl_journal_detail.deskripsi',
                                            'gl_sub_account.account_name',
                                            'gl_sub_account.account_number',
                                            'gl_journal.kode_ref'
                                        )
                                        ->where([
                                            ['gl_sub_account.id_account', '=', $id]
                                        ])
                                        // ->when($jenisPeriode == "bulanan", function($q) use ($date) {
                                        //     $q->whereMonth('gl_journal_detail.tanggal_transaksi', Carbon::parse($date)->format('m'));
                                        //     $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($date)->format('Y'));
                                        // })
                                        // ->when($jenisPeriode == "tahunan", function($q) use ($date) {
                                        //     $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($date)->format('Y'));
                                        // })
                                        ->whereRaw("Date(gl_journal_detail.tanggal_transaksi) <= '".$date."'")
                                        ->get();



        $saldoAwalDebet = $previousJournalEntries->filter(function ($item) {
                                return $item['side'] === 'debet';
                            })->sum('nominal');

        $saldoAwalKredit = $previousJournalEntries->filter(function ($item) {
                                return $item['side'] === 'credit';
                            })->sum('nominal');

        $journalEntries = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                        ->leftJoin('gl_journal', 'gl_journal_detail.id_journal', '=',  'gl_journal.id')
                                        ->select(
                                            'gl_journal_detail.tanggal_transaksi',
                                            'gl_journal_detail.nominal',
                                            'gl_journal_detail.side',
                                            'gl_journal_detail.sumber',
                                            'gl_journal_detail.deskripsi',
                                            'gl_sub_account.account_name',
                                            'gl_sub_account.account_number',
                                            'gl_journal.kode_ref'
                                        )
                                        ->where([
                                            ['gl_sub_account.id_account', '=', $id]
                                        ])
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('gl_journal_detail.tanggal_transaksi', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('gl_journal_detail.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->orderBy('gl_journal_detail.tanggal_transaksi', 'asc')
                                        ->orderBy('gl_journal.id', 'asc')
                                        ->get();

        $mutasiDebet = $journalEntries->filter(function ($item) {
                                return $item['side'] === 'debet';
                            })->sum('nominal');

        $mutasiKredit = $journalEntries->filter(function ($item) {
                                return $item['side'] === 'credit';
                            })->sum('nominal');

        if ($jenisPeriode == "harian") {
            $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
        }
        elseif ($jenisPeriode == "bulanan") {
            $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
        }
        else {
            $txt = Carbon::parse($tahun)->isoFormat('Y');
        }


        $data['dataDetails'] = $journalEntries;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['jenisPeriode'] = $jenisPeriode;
        $data['saldoAwalDebet'] = $saldoAwalDebet ?? 0;
        $data['saldoAwalKredit'] = $saldoAwalKredit  ?? 0;
        $data['mutasiDebet'] = $mutasiDebet ?? 0;
        $data['mutasiKredit'] = $mutasiKredit  ?? 0;
        $data['dataAccount'] = $account;
        $data['txtPeriode'] = $txt;

        $log = ActionLog::create([
            'module' => 'General Ledger',
            'action' => 'Generate',
            'desc' => 'Generate General Ledger',
            'username' => Auth::user()->user_name
        ]);

        $txtTitle = "General Ledger Akun ".str_replace(["/"],"_",$account->account_number)." ".str_replace(["/"],"_",$txt);

        if ($format == "pdf") {
            $pdf = Pdf::loadView('pages.accounting.gl_journal.cetak_gl', ['data' => $data]);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream($txtTitle.".pdf");
        }
        elseif ($format == "excel") {
            return Excel::download(new ExportGeneralLedger($request), $txtTitle.'.xlsx');
        }

    }

    public function displayEntry(Request $request)
    {
        $id = $request->input('idJournal');
        $entry = GLJournal::leftJoin('gl_journal_detail', 'gl_journal_detail.id_journal', '=', 'gl_journal.id')
                            ->leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=', 'gl_sub_account.id')
                            ->select(
                                'gl_journal.kode_ref',
                                'gl_journal.tanggal_transaksi',
                                'gl_journal_detail.sumber',
                                'gl_journal_detail.nominal',
                                'gl_journal_detail.side',
                                'gl_journal_detail.deskripsi',
                                'gl_sub_account.account_number',
                                'gl_sub_account.account_name',
                            )
                            ->where([
                                ['gl_journal.id', '=', $id]
                            ])
                            ->get();

        return response()->json($entry);
    }

    public function cetakGL($id)
    {
        //$id = $request->input('idJournal');
        //$bulan = $request->input('bulan');
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/GeneralLedger'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $bulanTemp = "2025-11-01";
                $jenisPeriode = "bulanan";

                if ($jenisPeriode == "bulanan") {
                    $date = Carbon::parse($bulanTemp)->lastOfMonth()->format('Y-m-d');
                }
                else {
                    $date = Carbon::parse($bulanTemp)->endOfYear()->format('Y-m-d');
                }

                $bulan = Carbon::parse($bulanTemp)->format('m');
                $tahun = Carbon::parse($bulanTemp)->format('Y');

                $saldoAkhir = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                                ->select(
                                                    'gl_sub_account.id_account',
                                                    DB::raw("SUM(CASE
                                                                 WHEN gl_journal_detail.side = 'debet'
                                                                     THEN gl_journal_detail.nominal
                                                                 ELSE -gl_journal_detail.nominal
                                                             END) AS saldo_akhir")
                                                )
                                                ->whereRaw("Date(gl_journal_detail.tanggal_transaksi) <= '".$date."'")
                                                ->groupBy('gl_sub_account.id_account');



                $accounts = GLAccount::leftJoinSub($saldoAkhir, 'saldoAkhir', function($saldoAkhir) {
                                        $saldoAkhir->on('gl_account.id', '=', 'saldoAkhir.id_account');
                                    })
                                    ->leftJoin('gl_mother_account', 'gl_account.id_mother_account', '=', 'gl_mother_account.id')
                                    ->select(
                                        'gl_mother_account.account_number as mother_account_number',
                                        'gl_mother_account.account_name as mother_account_name',
                                        'gl_mother_account.group',
                                        'gl_account.account_number',
                                        'gl_account.account_name',
                                        'saldoAkhir.saldo_akhir',
                                    )
                                    ->orderBy('gl_account.id_mother_account', 'asc')
                                    ->orderBy('gl_account.order_number', 'asc')
                                    ->get();


                if ($jenisPeriode == "bulanan") {
                    $txt = Carbon::parse($bulanTemp)->isoFormat('MMM Y');
                }
                else {
                    $txt = Carbon::parse($bulanTemp)->isoFormat('Y');
                }

                $aktivaLancar = $accounts->filter(function ($account) {
                                            return $account['mother_account_number'] === '10';
                                        });

                $aktivaTetap = $accounts->filter(function ($account) {
                                            return $account['mother_account_number'] === '15';
                                        });

                $liabilitas = $accounts->filter(function ($account) {
                                            return $account['mother_account_number'] === '20';
                                        });

                $ekuitas = $accounts->filter(function ($account) {
                                            return $account['mother_account_number'] === '30';
                                        });

                $akumulasiPenyusutan = $accounts->filter(function ($account) {
                                            return $account['mother_account_number'] === '16';
                                        });

                $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
                $data['aktivaLancar'] = $aktivaLancar;
                $data['aktivaTetap'] = $aktivaTetap;
                $data['liabilitas'] = $liabilitas;
                $data['ekuitas'] = $ekuitas;
                $data['akumulasiPenyusutan'] = $akumulasiPenyusutan;
                $data['bulan'] = $bulan;
                $data['tahun'] = $tahun;
                $data['jenisPeriode'] = $jenisPeriode;
                $data['txtPeriode'] = $txt;

                $log = ActionLog::create([
                    'module' => 'General Ledger',
                    'action' => 'Generate',
                    'desc' => 'Generate General Ledger',
                    'username' => Auth::user()->user_name
                ]);

                // $fpdf = HelperDelivery::cetakPdfDlv($data);

                // $fpdf->Output('I', strtoupper(str_replace(["-","/"],"_",$dataDelivery->kode_pengiriman)).".pdf");
                // exit;

                $pdf = Pdf::loadView('pages.accounting.gl_journal.cetak_bs', ['data' => $data]);
                $pdf->setPaper('a4', 'portrait');
                return $pdf->stream("test.pdf");
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexTB() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/TrialBalance'],
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
                                                ['module.url', '=', '/TrialBalance'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Trial Balance',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Trial Balance',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_journal.reportTrialBalance', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function exportTB(Request $request)
    {
        // $id = $request->input('account');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tanggal_picker_start');
        $tglEnd = $request->input('tanggal_picker_end');
        $bulan = $request->input('bulan_picker_val');
        $tahun = $request->input('tahun_picker_val');
        $format = $request->input('format');

        // $account = GLAccount::find($id);
        if ($jenisPeriode == "harian") {
            $date = Carbon::parse($tglStart)->subDay(1)->format('Y-m-d');
        }
        elseif ($jenisPeriode == "bulanan") {
            $date = Carbon::parse($bulan)->subMonth()->lastOfMonth()->format('Y-m-d');
        }
        else {
            $date = Carbon::parse($tahun)->subYear()->endOfYear()->format('Y-m-d');
        }

        $saldoAwal = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                        ->select(
                                            'gl_sub_account.id_account',
                                            DB::raw("SUM(CASE
                                                            WHEN gl_journal_detail.side = 'debet'
                                                                THEN gl_journal_detail.nominal
                                                            ELSE -gl_journal_detail.nominal
                                                        END) AS saldo_awal")
                                        )
                                        ->whereRaw("Date(gl_journal_detail.tanggal_transaksi) <= '".$date."'")
                                        ->groupBy('gl_sub_account.id_account');


        $mutasi = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                        ->select(
                                            'gl_sub_account.id_account',
                                            DB::raw("SUM(CASE
                                                            WHEN gl_journal_detail.side = 'debet'
                                                                THEN gl_journal_detail.nominal
                                                            ELSE 0
                                                        END) AS mutasi_debet"),
                                            DB::raw("SUM(CASE
                                                            WHEN gl_journal_detail.side = 'credit'
                                                                THEN gl_journal_detail.nominal
                                                            ELSE 0
                                                        END) AS mutasi_kredit"),
                                        )
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('gl_journal_detail.tanggal_transaksi', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('gl_journal_detail.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('gl_journal_detail.tanggal_transaksi', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->groupBy('gl_sub_account.id_account');


        $account = GLAccount::leftJoinSub($saldoAwal, 'saldoAwal', function($saldoAwal) {
                                $saldoAwal->on('gl_account.id', '=', 'saldoAwal.id_account');
                            })
                            ->leftJoinSub($mutasi, 'mutasi', function($mutasi) {
                                $mutasi->on('gl_account.id', '=', 'mutasi.id_account');
                            })
                            ->select(
                                'gl_account.account_number',
                                'gl_account.account_name',
                                'mutasi.mutasi_debet',
                                'mutasi.mutasi_kredit',
                                'saldoAwal.saldo_awal',
                            )
                            ->orderBy('gl_account.id_mother_account', 'asc')
                            ->orderBy('gl_account.order_number', 'asc')
                            ->get();

        if ($jenisPeriode == "harian") {
            $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
        }
        elseif ($jenisPeriode == "bulanan") {
            $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
        }
        else {
            $txt = Carbon::parse($tahun)->isoFormat('Y');
        }


        $data['dataDetails'] = $account;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['jenisPeriode'] = $jenisPeriode;
        $data['txtPeriode'] = $txt;

        $log = ActionLog::create([
            'module' => 'Trial Balance',
            'action' => 'Generate',
            'desc' => 'Generate Trial Balance',
            'username' => Auth::user()->user_name
        ]);

        $txtTitle = "Trial Balance ".str_replace(["/"],"_",$txt);

        if ($format == "pdf") {
            $pdf = Pdf::loadView('pages.accounting.gl_journal.cetak_tb', ['data' => $data]);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream($txtTitle.".pdf");
        }
        elseif ($format == "excel") {
            return Excel::download(new ExportTrialBalance($request), $txtTitle.'.xlsx');
        }

    }

    public function indexBS() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/BalanceSheet'],
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
                                                ['module.url', '=', '/BalanceSheet'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Balance Sheet',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Balance Sheet',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.gl_journal.reportBalanceSheet', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function exportBS(Request $request)
    {
        // $id = $request->input('account');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tanggal_picker_start');
        $tglEnd = $request->input('tanggal_picker_end');
        $bulan = $request->input('bulan_picker_val');
        $tahun = $request->input('tahun_picker_val');
        $format = $request->input('format');

        // $account = GLAccount::find($id);
        if ($jenisPeriode == "harian") {
            $date = Carbon::parse($tglStart)->subDay(1)->format('Y-m-d');
        }
        elseif ($jenisPeriode == "bulanan") {
            $date = Carbon::parse($bulan)->subMonth()->lastOfMonth()->format('Y-m-d');
        }
        else {
            $date = Carbon::parse($tahun)->subYear()->endOfYear()->format('Y-m-d');
        }

        $saldoAkhir = GLJournalDetail::leftJoin('gl_sub_account', 'gl_journal_detail.id_account', '=',  'gl_sub_account.id')
                                        ->select(
                                            'gl_sub_account.id_account',
                                            DB::raw("SUM(CASE
                                                        WHEN gl_journal_detail.side = 'debet'
                                                            THEN gl_journal_detail.nominal
                                                        ELSE -gl_journal_detail.nominal
                                                    END) AS saldo_akhir")
                                        )
                                        ->whereRaw("Date(gl_journal_detail.tanggal_transaksi) <= '".$date."'")
                                        ->groupBy('gl_sub_account.id_account');



        $accounts = GLAccount::leftJoinSub($saldoAkhir, 'saldoAkhir', function($saldoAkhir) {
                                $saldoAkhir->on('gl_account.id', '=', 'saldoAkhir.id_account');
                            })
                            ->leftJoin('gl_mother_account', 'gl_account.id_mother_account', '=', 'gl_mother_account.id')
                            ->select(
                                'gl_mother_account.account_number as mother_account_number',
                                'gl_mother_account.account_name as mother_account_name',
                                'gl_mother_account.group',
                                'gl_account.account_number',
                                'gl_account.account_name',
                                'saldoAkhir.saldo_akhir',
                            )
                            ->orderBy('gl_account.id_mother_account', 'asc')
                            ->orderBy('gl_account.order_number', 'asc')
                            ->get();

        $aktivaLancar = $accounts->filter(function ($account) {
                                    return $account['mother_account_number'] === '10';
                                });

        $aktivaTetap = $accounts->filter(function ($account) {
                                    return $account['mother_account_number'] === '15';
                                });

        $liabilitas = $accounts->filter(function ($account) {
                                    return $account['mother_account_number'] === '20';
                                });

        $ekuitas = $accounts->filter(function ($account) {
                                    return $account['mother_account_number'] === '30';
                                });

        $akumulasiPenyusutan = $accounts->filter(function ($account) {
                                    return $account['mother_account_number'] === '16';
                                });


        if ($jenisPeriode == "harian") {
            $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
        }
        elseif ($jenisPeriode == "bulanan") {
            $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
        }
        else {
            $txt = Carbon::parse($tahun)->isoFormat('Y');
        }


        $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
        $data['aktivaLancar'] = $aktivaLancar;
        $data['aktivaTetap'] = $aktivaTetap;
        $data['liabilitas'] = $liabilitas;
        $data['ekuitas'] = $ekuitas;
        $data['akumulasiPenyusutan'] = $akumulasiPenyusutan;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['jenisPeriode'] = $jenisPeriode;
        $data['txtPeriode'] = $txt;

        $log = ActionLog::create([
            'module' => 'Trial Balance',
            'action' => 'Generate',
            'desc' => 'Generate Trial Balance',
            'username' => Auth::user()->user_name
        ]);

        $txtTitle = "Laporan Keuangan ".str_replace(["/"],"_",$txt);

        if ($format == "pdf") {
            $pdf = Pdf::loadView('pages.accounting.gl_journal.cetak_bs', ['data' => $data]);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream($txtTitle.".pdf");
        }
        elseif ($format == "excel") {
            return Excel::download(new ExportBalanceSheet($request), $txtTitle.'.xlsx');
        }

    }
}
