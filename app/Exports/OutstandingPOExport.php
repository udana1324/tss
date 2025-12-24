<?php

namespace App\Exports;

use App\Models\Product\Product;
use App\Models\Purchasing\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OutstandingPOExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->nmSupplier = $request->input('id_supplier');
        $this->periode = $request->input('bulan_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $periode = $this->periode;
        $supplier = $this->nmSupplier;

        $dataOutstanding = PurchaseOrderDetail::leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->leftJoin('purchase_order', 'purchase_order_detail.id_po', 'purchase_order.id')
                                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                            ->select(
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'purchase_order_detail.harga_beli',
                                                'purchase_order_detail.outstanding_qty',
                                                'purchase_order.tanggal_po',
                                                'purchase_order.tanggal_request',
                                                'purchase_order.no_po',
                                                'purchase_order.status_po',
                                                'supplier.nama_supplier'
                                            )
                                            ->whereNotIn('purchase_order.status_po', ['draft', 'batal', 'close'])
                                            ->when($supplier != "", function($q) use ($supplier) {
                                                $q->where([
                                                    ['supplier.nama_supplier', '=', $supplier]
                                                ]);
                                            })
                                            ->when($periode != "", function($q) use ($periode) {
                                                $q->whereMonth('purchase_order.tanggal_po', Carbon::parse($periode)->format('m'));
                                                $q->whereYear('purchase_order.tanggal_po', Carbon::parse($periode)->format('Y'));
                                            })
                                            ->where([
                                                ['purchase_order_detail.outstanding_qty', '>', 0],
                                                ['purchase_order.outstanding_po', '>', 0],
                                            ])
                                            ->get();

        $data['dataOutstanding'] = $dataOutstanding;

        return View('pages.stock.outstanding_po.exportOutstanding', $data);
    }
}
