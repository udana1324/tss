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

class ExportTrialBalance extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->format = $request->input('format');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0.00_);(#,##0.00)',
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
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $format = $this->format;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        // $arrayIDs = explode(',', $ids);

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


        return View('pages.accounting.gl_journal.export_tb', $data);
    }
}
