<?php

namespace App\Exports;

use App\Models\Accounting\GLJournal;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GeneralLedgerExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idAccount = $request->input('account');
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $idAccount = $this->idAccount;
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

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
                                        ['gl_journal.id_account', '=', $idAccount]
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
                                    ['gl_journal.id_account', '=', $idAccount]
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

            if ($jenisPeriode == "harian") {
                $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
            }
            else if ($jenisPeriode == "bulanan") {
                $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
            }
            else {
                $txt = Carbon::parse($bulan)->isoFormat('Y');
            }
        }

        $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.accounting.gl_journal.reportGeneralLedgerExport', $data);
    }
}
