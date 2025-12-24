<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Purchasing\Receiving;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceivingExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idSupp = $request->input('id_supplier');
        $this->status = $request->input('status_penerimaan');
        $this->bulan = $request->input('bulan_picker_val');
    }

    public function view(): View
    {
        $salesOrder = "";
        $txt = "";
        $idSupp = $this->idSupp;
        $status = $this->status;
        $bulan = $this->bulan;

        $taxSettings = TaxSettings::find(1);

        $data['taxSettings'] = $taxSettings;
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        $ppnExcl = $taxSettings->ppn_percentage/100;

        $receiving = Receiving::leftJoin('purchase_order', 'receiving.id_po', '=', 'purchase_order.id')
                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                            ->leftJoin('receiving_detail', 'receiving_detail.id_penerimaan', '=', 'receiving.id')
                            ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                            ->leftJoin('purchase_order_detail', function($join) {
                                $join->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item');
                                $join->on('receiving_detail.id_satuan', '=', 'purchase_order_detail.id_satuan');
                                $join->on('receiving.id_po', '=', 'purchase_order_detail.id_po');
                            })
                            ->leftJoin('product_unit', 'receiving_detail.id_satuan', '=', 'product_unit.id')
                            ->leftJoin('purchase_invoice_detail', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                            ->leftJoin('purchase_invoice', 'purchase_invoice_detail.id_invoice', '=', 'purchase_invoice.id')
                            ->select(
                                'receiving.id',
                                'receiving.kode_penerimaan',
                                'receiving.tanggal_sj',
                                'receiving_detail.qty_item',
                                'product_unit.nama_satuan',
                                'supplier.nama_supplier',
                                'product.kode_item',
                                'product.nama_item',
                                'purchase_order_detail.harga_beli',
                                'purchase_invoice.dpp',
                                'purchase_invoice.ppn',
                                'purchase_invoice.grand_total',
                            )
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('receiving.tanggal_sj', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('receiving.tanggal_sj', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idSupp != "", function($q) use ($idSupp) {
                                $q->where('purchase_order.id_supplier', $idSupp);
                            })
                            ->when($status != "", function($q) use ($status) {
                                $q->where('receiving.status_penerimaan', strtolower($status));
                            })
                            ->orderBy('receiving.kode_penerimaan', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $receiving;

        return View('pages.purchasing.receiving.ReceivingExport', $data);
    }
}
