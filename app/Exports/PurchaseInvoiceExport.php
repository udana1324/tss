<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idSupp = $request->input('id_supplier');
        $this->status = $request->input('status_invoice');
        $this->bulan = $request->input('bulan_picker_val');
        $this->start = $request->input('tanggal_picker_start');
        $this->end = $request->input('tanggal_picker_end');
    }

    public function view(): View
    {
        $purchaseOrder = "";
        $txt = "";
        $idSupp = $this->idSupp;
        $status = $this->status;
        $bulan = $this->bulan;
        $start = $this->start;
        $end = $this->end;

        $taxSettings = TaxSettings::find(1);

        $data['taxSettings'] = $taxSettings;
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        $ppnExcl = $taxSettings->ppn_percentage/100;

        $purchaseInvoice = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', 'purchase_order.id')
                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                            ->leftJoin('purchase_invoice_detail', 'purchase_invoice_detail.id_invoice', '=', 'purchase_invoice.id')
                            ->leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                            ->leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', '=', 'receiving.id')
                            ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                            ->leftJoin('purchase_order_detail', function($join) {
                                $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                $join->on('receiving_detail.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                $join->on('receiving.id_po', '=', 'purchase_order_detail.id_po');
                            })
                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
                            ->select(
                                'supplier.nama_supplier',
                                'receiving.kode_penerimaan',
                                'purchase_order.no_po',
                                'purchase_order.metode_pembayaran',
                                'purchase_invoice.id',
                                'purchase_invoice.kode_invoice',
                                'purchase_invoice.dpp',
                                'purchase_invoice.ppn',
                                'purchase_invoice.grand_total',
                                'purchase_invoice.ttl_qty',
                                'purchase_invoice.tanggal_invoice',
                                'purchase_invoice.tanggal_jt',
                                'purchase_invoice.durasi_jt',
                                'purchase_invoice.flag_revisi',
                                'purchase_invoice.flag_pembayaran',
                                'purchase_invoice.status_invoice',
                                'receiving_detail.qty_item',
                                'product.kode_item',
                                'product.nama_item',
                                'purchase_order_detail.harga_beli',
                                'product_unit.nama_satuan')
                            ->when($start != "", function($q) use ($start, $end) {
                                $q->whereBetween('purchase_invoice.tanggal_invoice', [$start, $end]);
                            })
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idSupp != "", function($q) use ($idSupp) {
                                $q->where('supplier.nama_supplier', $idSupp);
                            })
                            // ->when($status != "", function($q) use ($status) {
                            //     $q->where('purchase_invoice.status_invoice', strtolower($status));
                            // })
                            ->where([
                                ['purchase_invoice.status_invoice', '=', 'posted']
                            ])
                            ->orderBy('purchase_invoice.kode_invoice', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $purchaseInvoice;

        return View('pages.purchasing.invoice.InvoiceExport', $data);
    }
}
