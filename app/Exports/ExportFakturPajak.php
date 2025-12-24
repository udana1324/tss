<?php

namespace App\Exports;

use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\SalesTaxInvoiceDetail;
use App\Models\Accounting\TaxSettings;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use App\Models\Setting\Preference;
use Illuminate\Support\Carbon;

class ExportFakturPajak implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->bulan = $request->input('bulan_picker_val');
        $this->ids = $request->input('id_invoices');
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) >= 16) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function view(): View
    {
        $dataFP = [];
        $txt = "";
        $bulan = $this->bulan;
        $ids = $this->ids;

        $arrayIDs = explode(',', $ids);

        if ($bulan != null || $ids != null) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

            $transaction = SalesTaxInvoice::leftJoin('sales_invoice', 'sales_tax_invoice.id_invoice', 'sales_invoice.id')
                                            ->leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('customer_detail',function($qJoin) {
                                                $qJoin->on('customer.id', '=', 'customer_detail.id_customer')
                                                //->where('customer_detail.jenis_alamat', "NPWP");
                                                ->whereRaw(
                                                    "customer_detail.jenis_alamat = CASE WHEN customer.jenis_customer = 'C' THEN 'NPWP' ELSE 'Gudang/Pengiriman' END"
                                                );
                                            })
                                            ->select(
                                                'sales_tax_invoice.id',
                                                'sales_tax_invoice.id_invoice',
                                                'sales_tax_invoice.nomor_faktur',
                                                'sales_tax_invoice.jenis_faktur',
                                                'sales_tax_invoice.pembetulan',
                                                'sales_tax_invoice.tanggal_faktur',
                                                'sales_tax_invoice.diskon',
                                                'sales_tax_invoice.dpp',
                                                'sales_tax_invoice.ppn',
                                                'sales_tax_invoice.grand_total',
                                                'sales_tax_invoice.ttl_qty',
                                                'sales_order.persentase_diskon',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                DB::raw("CONCAT(customer_detail.alamat_customer, ', ', customer_detail.kelurahan, ', ', customer_detail.kecamatan, ', ', customer_detail.kota) AS txtAlamat"),
                                                'sales_invoice.kode_invoice',
                                                'sales_invoice.flag_ppn',
                                            )
                                            // ->where([
                                            //     ['sales_tax_invoice.flag_export', '=', 0]
                                            // ])
                                            ->when($bulan != "", function($q) use ($bulan) {
                                                $q->whereMonth('sales_tax_invoice.tanggal_faktur', Carbon::parse($bulan)->format('m'));
                                                $q->whereYear('sales_tax_invoice.tanggal_faktur', Carbon::parse($bulan)->format('Y'));
                                            })
                                            ->when($ids != "", function($q) use ($arrayIDs) {
                                                $q->whereIn('sales_tax_invoice.id', $arrayIDs);
                                            })
                                            ->orderBy('sales_tax_invoice.nomor_faktur', 'desc')
                                            ->get();

            foreach ($transaction as $dataTransaction) {
                $salesTaxInvoice = SalesTaxInvoice::find($dataTransaction->id);
                $salesTaxInvoice->flag_export = 1;
                $salesTaxInvoice->save();

                $detailItem = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                        ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                                        ->select(
                                                            'delivery_detail.id_item',
                                                            'product_category.kode_kategori_pajak',
                                                            'product_unit.kode_satuan_pajak',
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $dataTransaction->id_invoice]
                                                        ]);

                $detailTemp = SalesTaxInvoiceDetail::leftJoin('product', 'sales_tax_invoice_detail.id_item', 'product.id')
                                                    ->leftJoinSub($detailItem, 'detailItem', function($detailItem) {
                                                        $detailItem->on('product.id', '=', 'detailItem.id_item');
                                                    })
                                                    ->select(
                                                        DB::raw("'OF' AS HeadRow"),
                                                        'product.nama_item',
                                                        'detailItem.kode_kategori_pajak',
                                                        'detailItem.kode_satuan_pajak',
                                                        'sales_tax_invoice_detail.qty',
                                                        'sales_tax_invoice_detail.harga_jual'
                                                    )
                                                    ->where([
                                                        ['sales_tax_invoice_detail.id_faktur', '=', $dataTransaction->id]
                                                    ])
                                                    ->get();

                if (count($detailTemp) > 0) {
                    $dataFaktur = [
                        'HeadRow' => 'FK',
                        'HeadJenis' => $dataTransaction->jenis_faktur,
                        'FKPengganti' => $dataTransaction->pembetulan,
                        'nomor_faktur' => str_replace('.', '',$dataTransaction->nomor_faktur),
                        'masa_pajak' => Carbon::parse($dataTransaction->tanggal_faktur)->format('m'),
                        'tahun_pajak' => Carbon::parse($dataTransaction->tanggal_faktur)->format('Y'),
                        'tanggal_faktur' => $dataTransaction->tanggal_faktur,
                        'flag_ppn' => $dataTransaction->flag_ppn,
                        'diskon' => $dataTransaction->persentase_diskon,
                        'dpp' => $dataTransaction->dpp,
                        'ppn' => $dataTransaction->ppn,
                        'grand_total' => $dataTransaction->grand_total,
                        'ttl_qty' => $dataTransaction->ttl_qty,
                        'nama_customer' => $dataTransaction->nama_customer,
                        'npwp_customer' => $dataTransaction->npwp_customer,
                        'txtAlamat' => $dataTransaction->txtAlamat,
                        'kode_invoice' => $dataTransaction->kode_invoice,
                        'detailFaktur' => $detailTemp,
                    ];
                    array_push($dataFP, $dataFaktur);
                }
            }
        }

        $ppnPercentageInc = 1+($taxSettings->ppn_percentage/100);
        $ppnPercentageExc = $taxSettings->ppn_percentage/100;
        $data['dataExport'] = $dataFP;
        $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
        $data['taxSettings'] = $taxSettings;
        $data['ppnPercentageInc'] = $ppnPercentageInc;
        $data['ppnPercentageExc'] = $ppnPercentageExc;


        return View('pages.accounting.tax_serial_number.exportFakturPajak', $data);
    }
}
