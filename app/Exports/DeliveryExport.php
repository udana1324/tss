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

class DeliveryExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idCust = $request->input('id_customer');
        $this->status = $request->input('status_pengiriman');
        $this->bulan = $request->input('bulan_picker_val');
    }

    public function view(): View
    {
        $salesOrder = "";
        $txt = "";
        $idCust = $this->idCust;
        $status = $this->status;
        $bulan = $this->bulan;

        $taxSettings = TaxSettings::find(1);

        $data['taxSettings'] = $taxSettings;
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        $ppnExcl = $taxSettings->ppn_percentage/100;

        $delivery = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                            ->leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', '=', 'delivery.id')
                            ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                            ->leftJoin('sales_order_detail', function($join) {
                                $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                $join->on('delivery.id_so', '=', 'sales_order_detail.id_so');
                            })
                            ->leftJoin('product_unit', 'delivery_detail.id_satuan', '=', 'product_unit.id')
                            ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                            ->leftJoin('sales_invoice', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                            ->select(
                                'delivery.id',
                                'delivery.kode_pengiriman',
                                'delivery.tanggal_sj',
                                'delivery_detail.qty_item',
                                'product_unit.nama_satuan',
                                'customer.nama_customer',
                                'product.kode_item',
                                'product.nama_item',
                                'sales_order_detail.harga_jual',
                                'sales_invoice.dpp',
                                'sales_invoice.ppn',
                                'sales_invoice.grand_total',
                            )
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('delivery.tanggal_sj', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('delivery.tanggal_sj', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idCust != "", function($q) use ($idCust) {
                                $q->where('sales_order.id_customer', $idCust);
                            })
                            ->when($status != "", function($q) use ($status) {
                                $q->where('delivery.status_pengiriman', strtolower($status));
                            })
                            ->orderBy('delivery.kode_pengiriman', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $delivery;

        return View('pages.sales.delivery.DeliveryExport', $data);
    }
}
