<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Purchasing\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Setting\Preference;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class APCardExport implements FromQuery, WithTitle, WithHeadings
{

    use Exportable;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function query()
    {
        $sheets = [];
        $bulan = $this->bulan;

        if ($bulan != null) {
            return PurchaseInvoice::query()->leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                           ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                           ->leftJoin('account_payable_detail', 'account_payable_detail.id_invoice', '=', 'purchase_invoice.id')
                                           ->leftJoin('account_payable', 'account_payable_detail.id_ap', '=', 'account_payable.id')
                                           ->select(
                                               'purchase_order.tanggal_po',
                                               'purchase_order.no_po',
                                               'purchase_invoice.tanggal_invoice',
                                               'purchase_invoice.kode_invoice',
                                               'supplier.nama_supplier',
                                               DB::raw("' ' AS FP"),
                                               'purchase_invoice.durasi_jt',
                                               'purchase_invoice.tanggal_jt',
                                               'purchase_invoice.dpp',
                                               'purchase_invoice.ppn',
                                               DB::raw("' ' AS PP"),
                                               'purchase_invoice.grand_total',
                                               DB::raw("' ' AS JP"),
                                               DB::raw("' ' AS Nominal"),
                                               'account_payable.tanggal'
                                           )
                                           ->when($bulan != "", function($q) use ($bulan) {
                                                $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                           });
        }
    }

    public function title(): string
    {
        return 'Kartu Hutang';
    }

    public function headings(): array
    {
        return [
            'Tgl PO',
            'No. PO',
            'Tanggal Inv',
            'No. Invoice',
            'Nama Supplier',
            'No. Faktur Pajak',
            'TOP',
            'Due Date',
            'DPP',
            'PPN',
            'Potongan Pajak',
            'Total Hutang',
            'Jenis Potput',
            'Nominal',
            'Tanggal Pembayaran'
        ];
    }
}
