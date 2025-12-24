<?php

namespace App\Exports;

use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OutstandingSOExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->nmCustomer = $request->input('id_customer');
        $this->periode = $request->input('bulan_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $periode = $this->periode;
        $cust = $this->nmCustomer;

        $dataOutstanding = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->leftJoin('sales_order', 'sales_order_detail.id_so', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->select(
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'sales_order_detail.harga_jual',
                                                'sales_order_detail.qty_outstanding',
                                                'sales_order.tanggal_so',
                                                'sales_order.tanggal_request',
                                                'sales_order.no_so',
                                                'customer.nama_customer'
                                            )
                                            ->whereNotIn('sales_order.status_so', ['draft', 'batal', 'close'])
                                            ->when($cust != "", function($q) use ($cust) {
                                                $q->where([
                                                    ['customer.nama_customer', '=', $cust]
                                                ]);
                                            })
                                            ->when($periode != "", function($q) use ($periode) {
                                                $q->whereMonth('sales_order.tanggal_so', Carbon::parse($periode)->format('m'));
                                                $q->whereYear('sales_order.tanggal_so', Carbon::parse($periode)->format('Y'));
                                            })
                                            ->where([
                                                ['sales_order_detail.qty_outstanding', '>', 0],
                                                ['sales_order.outstanding_so', '>', 0],
                                            ])
                                            ->get();

        $data['dataOutstanding'] = $dataOutstanding;

        return View('pages.stock.outstanding_so.exportOutstanding', $data);
    }
}
