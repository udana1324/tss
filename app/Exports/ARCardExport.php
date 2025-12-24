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
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ARCardExport implements FromQuery, WithHeadings, WithTitle
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
            return SalesInvoice::query()->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                            ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('account_receiveable_detail', 'account_receiveable_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('account_receiveable', 'account_receiveable_detail.id_ar', '=', 'account_receiveable.id')
                                            ->select(
                                                'sales_invoice.tanggal_invoice',
                                                'delivery.kode_pengiriman',
                                                'sales_invoice.kode_invoice',
                                                'sales_order.no_po_customer',
                                                'customer.nama_customer',
                                                'sales_invoice.grand_total',
                                                'sales_invoice.tanggal_jt',
                                                'account_receiveable.tanggal',
                                                DB::raw("CASE WHEN sales_invoice.tanggal_jt < CURDATE() AND sales_invoice.flag_pembayaran <> 1 Then sales_invoice.grand_total ELSE null END  AS DueAmount"),
                                                'sales_invoice.durasi_jt',
                                            )
                                            ->when($bulan != "", function($q) use ($bulan) {
                                                    $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                    $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                            });
        }
    }

    public function title(): string
    {
        return 'Kartu Piutang';
    }

    public function headings(): array
    {
        return [
            'Date',
            'No. SJ',
            'Inv#',
            'PO. Cust',
            'Customer Name',
            'Total Amount(IDR)',
            'Due',
            'Paid',
            'Due Amount',
            'Term of Payment',
            'Overdue',
            'Overdue + Term of payment'
        ];
    }
}
