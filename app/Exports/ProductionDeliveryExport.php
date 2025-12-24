<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Production\ProductionDelivery;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionDeliveryExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idSupp = $request->input('id_supplier');
        $this->status = $request->input('status_pengiriman');
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

        $delivery = ProductionDelivery::leftJoin('supplier', 'production_delivery.id_supplier', '=', 'supplier.id')
                            ->leftJoin('production_delivery_detail', 'production_delivery_detail.id_delivery', '=', 'production_delivery.id')
                            ->leftJoin('product', 'production_delivery_detail.id_item', '=', 'product.id')
                            ->leftJoin('product_unit', 'production_delivery_detail.id_satuan', '=', 'product_unit.id')
                            ->select(
                                'production_delivery.id',
                                'production_delivery.kode_pengiriman',
                                'production_delivery.tanggal_sj',
                                'production_delivery_detail.qty_item',
                                'production_delivery.status_pengiriman',
                                'product_unit.nama_satuan',
                                'supplier.nama_supplier',
                                'product.kode_item',
                                'product.nama_item',
                            )
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('production_delivery.tanggal_sj', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('production_delivery.tanggal_sj', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idSupp != "", function($q) use ($idSupp) {
                                $q->where('production_delivery.id_supplier', $idSupp);
                            })
                            ->when($status != "", function($q) use ($status) {
                                $q->where('production_delivery.status_pengiriman', strtolower($status));
                            })
                            ->orderBy('production_delivery.kode_pengiriman', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $delivery;

        return View('pages.production.production_delivery.ProductionDeliveryExport', $data);
    }
}
