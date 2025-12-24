<?php

namespace App\Exports;

use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\SalesTaxInvoiceDetail;
use App\Models\Accounting\TaxSettings;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Sales\SalesInvoice;
use App\Models\Setting\Preference;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;

class FinancialForecastExport implements WithMultipleSheets
{

    use Exportable;

    public function __construct(Request $request)
    {
        $this->bulan = $request->input('bulan_picker_val');
    }

    public function sheets(): array
    {
        $sheets = [];
        $bulan = $this->bulan;

        if ($bulan != null) {
            $sheets[] = new ARCardExport($bulan);
            $sheets[] = new APCardExport($bulan);
        }

        return $sheets;
    }
}
