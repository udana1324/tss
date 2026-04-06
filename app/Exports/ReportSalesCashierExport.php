<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Sales\SalesCashier;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class ReportSalesCashierExport extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithColumnFormatting
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

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0.00_);(#,##0.00)',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) >= 16) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
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

            $transaction = SalesCashier::leftJoin('customer', 'sales_cashier.id_customer', '=', 'customer.id')
                                        ->select(
                                            'sales_cashier.*',
                                            'customer.nama_customer'
                                        )
                                        ->when($jenis == "customer" && $idCust != "", function($q) use ($idCust) {
                                            $q->where('customer.id', '=', $idCust);
                                        })
                                        // ->when($jenis == "grup" && $grup != "", function($q) use ($grup) {
                                        //     $q->whereIn('customer.id', function($subQuery) use ($grup) {
                                        //         $subQuery->select('id_customer')->from('customer_group_detail')
                                        //         ->where('id_group', '=', $grup);
                                        //     });
                                        // })
                                        ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                            $q->whereBetween('sales_cashier.tanggal_penjualan', [$tglStart, $tglEnd]);
                                        })
                                        ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                            $q->whereMonth('sales_cashier.tanggal_penjualan', Carbon::parse($bulan)->format('m'));
                                            $q->whereYear('sales_cashier.tanggal_penjualan', Carbon::parse($bulan)->format('Y'));
                                        })
                                        ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                            $q->whereYear('sales_cashier.tanggal_penjualan', Carbon::parse($tahun)->format('Y'));
                                        })
                                        ->where([
                                            ['sales_cashier.status_sales', '=', 'posted'],
                                        ])
                                        ->orderBy('sales_cashier.id', 'asc')
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

        return View('pages.report.reportSalesCashierExport', $data);
    }
}
