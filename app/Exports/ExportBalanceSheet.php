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

class ExportBalanceSheet extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithColumnFormatting
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
            'B' => '#,##0.00_);(#,##0.00)',
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


        return View('pages.accounting.gl_journal.export_bs', $data);
    }
}
