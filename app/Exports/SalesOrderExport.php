<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesOrderExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->idCust = $request->input('id_customer');
        $this->status = $request->input('status_so');
        $this->bulan = $request->input('bulan_picker_val');
    }

    public function view(): View
    {
        $salesOrder = "";
        $txt = "";
        $idCust = $this->idCust;
        $status = $this->status;
        $bulan = $this->bulan;



        $salesOrder = SalesOrder::leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                            ->select(
                                'customer.nama_customer',
                                DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                'sales_order.id',
                                'sales_order.id_customer',
                                'sales_order.no_so',
                                'sales_order.nominal_dp',
                                'sales_order.no_po_customer',
                                'sales_order.jumlah_total_so',
                                'sales_order.outstanding_so',
                                'sales_order.tanggal_so',
                                'sales_order.tanggal_request',
                                'sales_order.nominal_so_ttl',
                                'sales_order.flag_revisi',
                                'sales_order.metode_pembayaran',
                                'sales_order.durasi_jt',
                                'sales_order.status_so')
                            ->when($bulan != "", function($q) use ($bulan) {
                                $q->whereMonth('sales_order.tanggal_so', Carbon::parse($bulan)->format('m'));
                                $q->whereYear('sales_order.tanggal_so', Carbon::parse($bulan)->format('Y'));
                            })
                            ->when($idCust != "", function($q) use ($idCust) {
                                $q->where('sales_order.id_customer', $idCust);
                            })
                            ->when($status != "", function($q) use ($status) {
                                $q->where('sales_order.status_so', strtolower($status));
                            })
                            ->orderBy('sales_order.id', 'desc')
                            ->get();

        $data['periode'] = $txt;
        $data['dataLaporan'] = $salesOrder;

        return View('pages.sales.sales_order.SalesOrderExport', $data);
    }
}
