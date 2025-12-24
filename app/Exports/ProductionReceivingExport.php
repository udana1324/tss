<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Production\ProductionReceiving;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionReceivingExport implements FromView
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

        $receiving = ProductionReceiving::leftJoin('production_order', 'production_receiving.id_po', '=', 'production_order.id')
                            ->leftJoin('supplier', 'production_order.id_supplier', '=', 'supplier.id')
                            ->leftJoin('production_receiving_detail', 'production_receiving_detail.id_penerimaan', '=', 'production_receiving.id')
                            ->leftJoin('product', 'production_receiving_detail.id_item', '=', 'product.id')
                            ->leftJoin('product_unit', 'production_receiving_detail.id_satuan', '=', 'product_unit.id')
                            ->select(
                                'production_receiving.id',
                                'production_receiving.kode_penerimaan',
                                'production_receiving.no_sj_supplier',
                                'production_receiving.tanggal_sj',
                                'production_receiving_detail.qty_item',
                                'product_unit.nama_satuan',
                                'supplier.nama_supplier',
                                'product.kode_item',
                                'product.nama_item',
                            )
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('production_receiving.tanggal_sj', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('production_receiving.tanggal_sj', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idSupp != "", function($q) use ($idSupp) {
                                $q->where('production_order.id_supplier', $idSupp);
                            })
                            ->when($status != "", function($q) use ($status) {
                                $q->where('production_receiving.status_pengiriman', strtolower($status));
                            })
                            ->orderBy('production_receiving.kode_penerimaan', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $receiving;

        return View('pages.production.production_receiving.ProductionReceivingExport', $data);
    }
}
