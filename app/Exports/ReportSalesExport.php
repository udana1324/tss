<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Accounting\AccountReceiveableDetail;
use Illuminate\Support\Carbon;

class ReportSalesExport implements FromView
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
        $this->idCust = $request->input('customer');
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
        $idCust = $this->idCust;
        $jenis = $this->jenis;
        $grup = $this->grup;

        if ($jenisPeriode != null) {
            $totalPembayaran = AccountReceiveableDetail::leftJoin('account_receiveable', 'account_receiveable_detail.id_ar', '=', 'account_receiveable.id')
                                                        ->select(
                                                            'account_receiveable_detail.id_invoice',
                                                            'account_receiveable_detail.nominal_bayar',
                                                            'account_receiveable.tanggal'
                                                        )

                                                        ->orderBy('account_receiveable.tanggal', 'asc');

            $transaction = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                            ->leftJoinSub($totalPembayaran, 'totalPembayaran', function($totalPembayaran) {
                                                $totalPembayaran->on('sales_invoice.id', '=', 'totalPembayaran.id_invoice');
                                            })
                                            ->select(
                                                'sales_invoice.*',
                                                'sales_order.no_so',
                                                'customer.nama_customer',
                                                'customer_detail.nama_outlet',
                                                'totalPembayaran.nominal_bayar',
                                                'totalPembayaran.tanggal'
                                            )
                                            ->when($jenis == "customer" && $idCust != "", function($q) use ($idCust) {
                                                $q->where('customer.id', '=', $idCust);
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
                                            ->where([
                                                ['sales_invoice.status_invoice', '=', 'posted'],
                                            ])
                                            ->orderBy('sales_invoice.id', 'asc')
                                            ->get();

            if ($jenisPeriode == "harian") {
                $txt = Carbon::parse($tglStart)->isoFormat('D MMM Y'). " - ". Carbon::parse($tglEnd)->isoFormat('D MMM Y');
            }
            else if ($jenisPeriode == "bulanan") {
                $txt = Carbon::parse($bulan)->isoFormat('MMM Y');
            }
            else {
                $txt = Carbon::parse($tahun)->isoFormat('Y');
            }
        }

        $data['periode'] = $txt;
        $data['dataLaporan'] = $transaction;

        return View('pages.report.reportSalesExport', $data);
    }
}
