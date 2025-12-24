<?php

namespace App\Classes\BusinessManagement;

use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\SalesTaxInvoiceDetail;
use App\Models\Accounting\TaxSerialNumber;
use App\Models\Accounting\TaxSettings;
use App\Models\ActionLog;
use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;

class HelperSalesTaxInvoice
{
    public static function AutoGenerateTaxInvoice($dataTaxInvoice, $flagPengganti, $idFaktur) {
        $dataSalesInvoice = $dataTaxInvoice['dataSalesInvoice'];
        $detailSalesInvoice = $dataTaxInvoice['detailSalesInvoice'];

        // $taxSerialNumber = HelperSalesTaxInvoice::findTaxSerial($dataSalesInvoice->tanggal_invoice);
        $taxSettings = TaxSettings::find(1);


        if ($taxSettings != null) {
            $idParent = null;
            $parentFP = SalesTaxInvoice::find($idFaktur);
            if ($parentFP != null)
            {
                $idParent = $parentFP->id;
            }

            // $latestNumber = SalesTaxInvoice::select('nomor_faktur')
            //                                 ->where([
            //                                     ['id_seri', '=', $taxSerialNumber->id],
            //                                     ['pembetulan', '=', 0]
            //                                 ])
            //                                 ->orderBy('created_at', 'desc')
            //                                 ->orderBy('id', 'desc')
            //                                 ->first();
            // if ($latestNumber != null) {
            //     $getNomorFaktur = explode(".", $latestNumber->nomor_faktur);
            //     $nmr = $getNomorFaktur[2] + 1;
            // }
            // else {
            //     $getNomorFaktur = explode(".", $taxSerialNumber->nomor_seri_dari);
            //     $nmr = $getNomorFaktur[2];
            // }


            // $nmrFaktur = $getNomorFaktur[0].'.'.$getNomorFaktur[1].'.'.str_pad($nmr , 8 , "0" , STR_PAD_LEFT);
            $jenisFaktur = $taxSettings->generate_non_ppn == 1 && $dataSalesInvoice->flag_ppn == 'N' ? '08' : '01';
            //dd($latestNumber, $nmrFaktur);

            if ($flagPengganti == 0) {

                $salesTax = SalesTaxInvoice::firstOrCreate(
                    ['nomor_faktur' => $dataSalesInvoice->kode_invoice],
                    [
                        'jenis_faktur' => $jenisFaktur,
                        'pembetulan' => $flagPengganti,
                        // 'id_seri' => $taxSerialNumber->id,
                        'id_invoice' => $dataSalesInvoice->id,
                        'dpp' => $dataSalesInvoice->dpp,
                        'ppn' => $dataSalesInvoice->ppn,
                        'grand_total' => $dataSalesInvoice->grand_total,
                        'ttl_qty' => $dataSalesInvoice->ttl_qty,
                        'tanggal_faktur' => $dataSalesInvoice->tanggal_invoice,
                        'id_parent' => $idParent,
                        'flag_export' => 0,
                        'created_by' => Auth::user()->user_name
                    ]
                );
            }
            else {
                $salesTax = new SalesTaxInvoice();
                $salesTax->nomor_faktur = $parentFP->nomor_faktur;
                $salesTax->jenis_faktur = $jenisFaktur;
                $salesTax->pembetulan = $flagPengganti;
                // $salesTax->id_seri = $taxSerialNumber->id;
                $salesTax->id_invoice = $dataSalesInvoice->id;
                $salesTax->dpp = $dataSalesInvoice->dpp;
                $salesTax->ppn = $dataSalesInvoice->ppn;
                $salesTax->grand_total = $dataSalesInvoice->grand_total;
                $salesTax->ttl_qty = $dataSalesInvoice->ttl_qty;
                $salesTax->tanggal_faktur = $dataSalesInvoice->tanggal_invoice;
                $salesTax->id_parent = $idParent;
                $salesTax->flag_export = 0;
                $salesTax->created_by = Auth::user()->user_name;
                $salesTax->save();
            }

            if ($detailSalesInvoice != "") {
                $listDetails = [];
                foreach ($detailSalesInvoice as $items) {
                    $dataDetails = [
                        'id_faktur' => $salesTax->id,
                        'id_item' => $items->id_item,
                        'id_satuan' => $items->id_satuan,
                        'qty' => $items->qty_item,
                        'harga_jual' => $items->harga_jual,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name
                    ];
                    array_push($listDetails, $dataDetails);
                }
                SalesTaxInvoiceDetail::insert($listDetails);
            }

            // if ($salesTax->nomor_faktur == $taxSerialNumber->nomor_seri_sampai) {
            //     HelperSalesTaxInvoice::closeTaxSerial($taxSerialNumber->id);
            // }

            if ($salesTax->wasRecentlyCreated) {
                return "success";
            }
        }
        else {
            return "failNoTaxSeries";
        }
    }
    // public static function AutoGenerateTaxInvoice($dataTaxInvoice, $flagPengganti, $idFaktur) {
    //     $dataSalesInvoice = $dataTaxInvoice['dataSalesInvoice'];
    //     $detailSalesInvoice = $dataTaxInvoice['detailSalesInvoice'];

    //     $taxSerialNumber = HelperSalesTaxInvoice::findTaxSerial($dataSalesInvoice->tanggal_invoice);
    //     $taxSettings = TaxSettings::find(1);


    //     if ($taxSerialNumber != null) {
    //         $idParent = null;
    //         $parentFP = SalesTaxInvoice::find($idFaktur);
    //         if ($parentFP != null)
    //         {
    //             $idParent = $parentFP->id;
    //         }

    //         $latestNumber = SalesTaxInvoice::select('nomor_faktur')
    //                                         ->where([
    //                                             ['id_seri', '=', $taxSerialNumber->id],
    //                                             ['pembetulan', '=', 0]
    //                                         ])
    //                                         ->orderBy('created_at', 'desc')
    //                                         ->orderBy('id', 'desc')
    //                                         ->first();
    //         if ($latestNumber != null) {
    //             $getNomorFaktur = explode(".", $latestNumber->nomor_faktur);
    //             $nmr = $getNomorFaktur[2] + 1;
    //         }
    //         else {
    //             $getNomorFaktur = explode(".", $taxSerialNumber->nomor_seri_dari);
    //             $nmr = $getNomorFaktur[2];
    //         }


    //         $nmrFaktur = $getNomorFaktur[0].'.'.$getNomorFaktur[1].'.'.str_pad($nmr , 8 , "0" , STR_PAD_LEFT);
    //         $jenisFaktur = $taxSettings->generate_non_ppn == 1 && $dataSalesInvoice->flag_ppn == 'N' ? '08' : '01';
    //         //dd($latestNumber, $nmrFaktur);

    //         if ($flagPengganti == 0) {

    //             $salesTax = SalesTaxInvoice::firstOrCreate(
    //                 ['nomor_faktur' => $nmrFaktur],
    //                 [
    //                     'jenis_faktur' => $jenisFaktur,
    //                     'pembetulan' => $flagPengganti,
    //                     'id_seri' => $taxSerialNumber->id,
    //                     'id_invoice' => $dataSalesInvoice->id,
    //                     'dpp' => $dataSalesInvoice->dpp,
    //                     'ppn' => $dataSalesInvoice->ppn,
    //                     'grand_total' => $dataSalesInvoice->grand_total,
    //                     'ttl_qty' => $dataSalesInvoice->ttl_qty,
    //                     'tanggal_faktur' => $dataSalesInvoice->tanggal_invoice,
    //                     'id_parent' => $idParent,
    //                     'flag_export' => 0,
    //                     'created_by' => Auth::user()->user_name
    //                 ]
    //             );
    //         }
    //         else {
    //             $salesTax = new SalesTaxInvoice();
    //             $salesTax->nomor_faktur = $parentFP->nomor_faktur;
    //             $salesTax->jenis_faktur = $jenisFaktur;
    //             $salesTax->pembetulan = $flagPengganti;
    //             $salesTax->id_seri = $taxSerialNumber->id;
    //             $salesTax->id_invoice = $dataSalesInvoice->id;
    //             $salesTax->dpp = $dataSalesInvoice->dpp;
    //             $salesTax->ppn = $dataSalesInvoice->ppn;
    //             $salesTax->grand_total = $dataSalesInvoice->grand_total;
    //             $salesTax->ttl_qty = $dataSalesInvoice->ttl_qty;
    //             $salesTax->tanggal_faktur = $dataSalesInvoice->tanggal_invoice;
    //             $salesTax->id_parent = $idParent;
    //             $salesTax->flag_export = 0;
    //             $salesTax->created_by = Auth::user()->user_name;
    //             $salesTax->save();
    //         }

    //         if ($detailSalesInvoice != "") {
    //             $listDetails = [];
    //             foreach ($detailSalesInvoice as $items) {
    //                 $dataDetails = [
    //                     'id_faktur' => $salesTax->id,
    //                     'id_item' => $items->id_item,
    //                     'qty' => $items->qty_item,
    //                     'harga_jual' => $items->harga_jual,
    //                     'created_at' => now(),
    //                     'created_by' => Auth::user()->user_name
    //                 ];
    //                 array_push($listDetails, $dataDetails);
    //             }
    //             SalesTaxInvoiceDetail::insert($listDetails);
    //         }

    //         if ($salesTax->nomor_faktur == $taxSerialNumber->nomor_seri_sampai) {
    //             HelperSalesTaxInvoice::closeTaxSerial($taxSerialNumber->id);
    //         }

    //         if ($salesTax->wasRecentlyCreated) {
    //             return "success";
    //         }
    //     }
    //     else {
    //         return "failNoTaxSeries";
    //     }
    // }

    public static function RefreshTaxInvoice($dataTaxInvoice, $idFaktur) {
        $dataSalesInvoice = $dataTaxInvoice['dataSalesInvoice'];
        $detailSalesInvoice = $dataTaxInvoice['detailSalesInvoice'];

        $taxSerialNumber = HelperSalesTaxInvoice::findTaxSerial($dataSalesInvoice->tanggal_invoice);
        $taxSettings = TaxSettings::find(1);


        if ($idFaktur != null) {
            $idParent = null;
            $fp = SalesTaxInvoice::find($idFaktur);

            $fp->dpp = $dataSalesInvoice->dpp;
            $fp->ppn = $dataSalesInvoice->ppn;
            $fp->grand_total = $dataSalesInvoice->grand_total;
            $fp->ttl_qty = $dataSalesInvoice->ttl_qty;
            $fp->tanggal_faktur = $dataSalesInvoice->tanggal_invoice;
            $fp->updated_by = Auth::user()->user_name;
            $fp->save();

            if ($detailSalesInvoice != "") {
                $delete = DB::table('sales_tax_invoice_detail')->where('id_faktur', '=', $fp->id)->delete();
                $listDetails = [];
                foreach ($detailSalesInvoice as $items) {
                    $dataDetails = [
                        'id_faktur' => $fp->id,
                        'id_item' => $items->id_item,
                        'qty' => $items->qty_item,
                        'harga_jual' => $items->harga_jual,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name
                    ];
                    array_push($listDetails, $dataDetails);
                }
                SalesTaxInvoiceDetail::insert($listDetails);
            }

            if ($fp->isDirty()) {
                return "success";
            }
        }
        else {
            return "failNoTaxSeries";
        }
    }

    static function findTaxSerial($tglInv) {

        $thnPeriode = date("Y", strtotime($tglInv));

        $taxSerialNumber = TaxSerialNumber::where([
                                            ['status', '=', 'posted'],
                                            ['tahun_berlaku_seri', '=', $thnPeriode],
                                        ])
                                        ->orderBy('tanggal_pemberitahuan_djp', 'asc')
                                        ->first();

        return $taxSerialNumber;

    }

    static function closeTaxSerial($id) {

        $taxSerialNumber = TaxSerialNumber::find($id);
        $taxSerialNumber->status = "close";
        $taxSerialNumber->updated_by = Auth::user()->user_name;
        $taxSerialNumber->save();

        $log = ActionLog::create([
            'module' => 'Tax Serial Number',
            'action' => 'Tutup',
            'desc' => 'Tutup Tax Serial Number Karena sudah habis terpakai',
            'username' => Auth::user()->user_name
        ]);
    }
}
