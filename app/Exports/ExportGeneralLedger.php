<?php

namespace App\Exports;

use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLJournalDetail;
use App\Models\Accounting\GLKasBank;
use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\SalesTaxInvoiceDetail;
use App\Models\Accounting\TaxSettings;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Sales\SalesInvoice;
use App\Models\Setting\Preference;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ExportGeneralLedger extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->format = $request->input('format');
        $this->id = $request->input('account');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0.00_);(#,##0.00)',
            'D' => '#,##0.00_);(#,##0.00)',
            'E' => '#,##0.00_);(#,##0.00)',
            'F' => '#,##0.00_);(#,##0.00)'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) >= 16) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $jenisPeriode = $this->jenisPeriode;
        $id = $this->id;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $format = $this->format;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        // $arrayIDs = explode(',', $ids);

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


        return View('pages.accounting.gl_journal.export_gl', $data);
    }
}
