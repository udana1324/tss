<?php

namespace App\Exports;

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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ExportEntryKasBank extends DefaultValueBinder implements FromView, WithCustomValueBinder
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->bulan = $request->input('bulan_picker_val');
        $this->jenis = $request->input('jenis');
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) >= 16) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function view(): View
    {
        $dataFP = [];
        $txt = "";
        $bulan = $this->bulan;
        $jenis = $this->jenis;

        // $arrayIDs = explode(',', $ids);

        if ($bulan != null || $jenis != null) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

            $kasBank = $jenis == "kas" ? 1 : 2;

            $accounts = GLKasBank::
                            // leftJoin('gl_kas_bank_detail', 'gl_kas_bank_detail.id_kas_bank', '=', 'gl_kas_bank.id')
                            leftJoin('gl_sub_account as a', 'gl_kas_bank.id_account_sub', 'a.id')
                            // ->leftJoin('gl_sub_account as b', 'gl_kas_bank_detail.id_account_sub', 'b.id')
                            ->select(
                                'a.account_number as kasbank_account_number',
                                'a.account_name as kasbank_account_name',
                                'gl_kas_bank.tanggal_transaksi',
                                'gl_kas_bank.nomor_kas_bank',
                                'gl_kas_bank.nominal_transaksi',
                                'gl_kas_bank.jenis_transaksi',
                            )
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('gl_kas_bank.tanggal_transaksi', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('gl_kas_bank.tanggal_transaksi', Carbon::parse($bulan)->format('Y'));
                            })
                            ->where([
                                ['gl_kas_bank.id_account', '=', $kasBank],
                                // ['gl_kas_bank.jenis', '=', 'input'],
                            ])
                            ->orderBy('gl_kas_bank.tanggal_transaksi', 'asc')
                            ->get();
        }
        $ppnPercentageInc = 1+($taxSettings->ppn_percentage/100);
        $ppnPercentageExc = $taxSettings->ppn_percentage/100;
        $data['dataExport'] = $accounts;
        $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
        $data['taxSettings'] = $taxSettings;
        $data['ppnPercentageInc'] = $ppnPercentageInc;
        $data['ppnPercentageExc'] = $ppnPercentageExc;


        return View('pages.accounting.gl_kas_bank.exportEntryKasBank', $data);
    }
}
