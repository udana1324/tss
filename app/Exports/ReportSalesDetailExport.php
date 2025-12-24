<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Carbon;

class ReportSalesDetailExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
        $this->idCustomer = $request->input('customer');
        $this->jenis = $request->input('jenis');
        $this->grup = $request->input('grup');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;
        $idCustomer = $this->idCustomer;
        $jenis = $this->jenis;
        $grup = $this->grup;

        if ($jenisPeriode != null) {
            if ($idCustomer == "All") {
                $idCustomer = "";
            }

            $transaction = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                            ->leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', '=', 'delivery.id')
                                            ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                            ->leftJoin('sales_order_detail', function($join) {
                                                $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                                $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                                $join->on('sales_order.id', '=', 'sales_order_detail.id_so');
                                            })
                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->select(
                                                'delivery.kode_pengiriman',
                                                'sales_order.no_so',
                                                'sales_invoice.kode_invoice',
                                                'sales_invoice.tanggal_invoice',
                                                'delivery_detail.qty_item',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'product_category.nama_kategori',
                                                'sales_order_detail.harga_jual',
                                                'customer.nama_customer'
                                            )
                                            ->when($jenis == "customer" && $idCustomer != "", function($q) use ($idCustomer) {
                                                $q->where('customer.id', '=', $idCustomer);
                                            })
                                            ->when($jenis == "grup" && $grup != "", function($q) use ($grup) {
                                                $q->whereIn('customer.id', function($subQuery) use ($grup) {
                                                    $subQuery->select('id_customer')->from('customer_group_detail')
                                                    ->where('id_group', '=', $grup);
                                                });
                                            })
                                            ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                $q->whereBetween('sales_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                            })
                                            ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                            })
                                            ->orderBy('sales_invoice.tanggal_invoice', 'desc')
                                            ->get();

            if ($jenisPeriode == "harian") {
                $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
            }
            else if ($jenisPeriode == "bulanan") {
                $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
            }
            else {
                $txt = Carbon::parse($bulan)->isoFormat('Y');
            }
        }

        $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.report.reportSalesDetailExport', $data);
    }
}
