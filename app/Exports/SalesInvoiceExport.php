<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesInvoiceExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idCust = $request->input('id_customer');
        $this->status = $request->input('status_invoice');
        $this->bulan = $request->input('bulan_picker_val');
        $this->start = $request->input('tanggal_picker_start');
        $this->end = $request->input('tanggal_picker_end');
    }

    public function view(): View
    {
        $salesOrder = "";
        $txt = "";
        $idCust = $this->idCust;
        $status = $this->status;
        $bulan = $this->bulan;
        $start = $this->start;
        $end = $this->end;

        $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

        $data['taxSettings'] = $taxSettings;
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        $ppnExcl = $taxSettings->ppn_percentage/100;

        $salesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                            ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                            ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                            ->leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', '=', 'delivery.id')
                            ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                            ->leftJoin('sales_order_detail', function($join) {
                                $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                $join->on('delivery.id_so', '=', 'sales_order_detail.id_so');
                            })
                            ->leftJoin('product_unit', 'delivery_detail.id_satuan', '=', 'product_unit.id')
                            ->select(
                                'customer.nama_customer',
                                'delivery.kode_pengiriman',
                                'sales_order.no_so',
                                'sales_order.metode_pembayaran',
                                'sales_order.no_po_customer',
                                'sales_order.nominal_dp',
                                'sales_invoice.id',
                                'sales_invoice.kode_invoice',
                                'sales_invoice.dpp',
                                'sales_invoice.ppn',
                                'sales_invoice.grand_total',
                                'sales_invoice.ttl_qty',
                                'sales_invoice.tanggal_invoice',
                                'sales_invoice.tanggal_jt',
                                'sales_invoice.durasi_jt',
                                'sales_invoice.flag_revisi',
                                'sales_invoice.flag_tf',
                                'sales_invoice.flag_ppn',
                                'sales_invoice.flag_pembayaran',
                                'sales_invoice.status_invoice',
                                'delivery_detail.qty_item',
                                'product.kode_item',
                                'product.nama_item',
                                'sales_order_detail.harga_jual',
                                'product_unit.nama_satuan')
                            ->when($start != "", function($q) use ($start, $end) {
                                $q->whereBetween('sales_invoice.tanggal_invoice', [$start, $end]);
                            })
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idCust != "", function($q) use ($idCust) {
                                $q->where('sales_order.id_customer', $idCust);
                            })
                            // ->when($status != "", function($q) use ($status) {
                            //     $q->where('sales_invoice.status_invoice', strtolower($status));
                            // })
                            ->where([
                                ['sales_invoice.status_invoice', '=', 'posted']
                            ])
                            ->orderBy('sales_invoice.kode_invoice', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $salesInvoice;

        return View('pages.sales.invoice.InvoiceExport', $data);
    }
}
