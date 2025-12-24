<?php

namespace App\Exports;

use App\Models\Accounting\TaxSettings;
use App\Models\Product\Product;
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

class ProductExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->bulan = $request->input('bulan_picker_val');
        $this->start = $request->input('tanggal_picker_start');
        $this->end = $request->input('tanggal_picker_end');
    }

    public function view(): View
    {
        $txt = "";
        $bulan = $this->bulan;
        $start = $this->start;
        $end = $this->end;

        $taxSettings = TaxSettings::find(1);

        $data['taxSettings'] = $taxSettings;
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        $ppnExcl = $taxSettings->ppn_percentage/100;

        $product = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                            ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                            ->leftJoin('product_detail', 'product_detail.id_product', '=', 'product.id')
                            ->leftJoin('product_unit', 'product_unit.id', '=', 'product_detail.id_satuan')
                            ->select(
                                'product.*',
                                'product_brand.nama_merk',
                                'product_category.nama_kategori',
                                'product_unit.nama_satuan',
                            )
                            ->where([
                                ['product_detail.deleted_at', '=', null]
                            ])
                            ->orderBy('product.kode_item', 'asc')
                            ->orderBy('product_brand.nama_merk', 'asc')
                            ->orderBy('product_category.nama_kategori', 'asc')
                            ->get();

        $data['periode'] = $txt;
        $data['ppnPercentage'] = $ppnPercentage;
        $data['ppnExcl'] = $ppnExcl;
        $data['dataLaporan'] = $product;

        return View('pages.product.product.productExport', $data);
    }
}
