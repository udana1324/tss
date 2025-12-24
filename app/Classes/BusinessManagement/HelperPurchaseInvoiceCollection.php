<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceCollectionDetail;

class HelperPurchaseInvoiceCollection
{
    public static function UpdateInvoice($id, $flag)
    {
        try {
            DB::beginTransaction();
            $listInv = PurchaseInvoiceCollectionDetail::select('id_invoice')
                        ->where([
                            ['id_tf', '=', $id],
                            ['deleted_at', '=', null]
                        ])
                        ->get();

            if (count($listInv) > 0) {
                foreach ($listInv as $inv) {
                    $dlv = PurchaseInvoice::find($inv->id_invoice);
                    $dlv->flag_tf = $flag;
                    $dlv->updated_by = Auth::user()->user_name;
                    $dlv->save();
                }
                DB::commit();
                return 'ok';

            }
            else {
                DB::rollBack();
                return 'NoInv';
            }
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function cetakPdfInvCollection($data)
    {
        $dataPurchaseInvoice = $data['dataPurchaseInvoiceCollection'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailPurchaseInvoice = $data['detailPurchaseInvoiceCollection'];
        $CompanyAccount = $data['CompanyAccount'];

        $fpdf = new Fpdf;

        $txtTerbilang = Helper::number_to_words($dataPurchaseInvoice->nominal);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah");
        $alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        //header Tanda Terima Faktur
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataPurchaseInvoice->kode_tf));
        $fpdf->SetFont('Arial','U',14);
        $fpdf->Cell(190,7,"TANDA TERIMA FAKTUR",0,1, 'C' );
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(130,6,"Kepada Yth,",0,0, 'L' );
        $fpdf->Cell(65,6,"Nomor : ".strtoupper($dataPurchaseInvoice->kode_tf),0,1, 'L' );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(130,6,strtoupper($dataPurchaseInvoice->nama_supplier),0,0, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(65,6,"Tanggal : ".date("d M Y", strtotime($dataPurchaseInvoice->tanggal)),0,1, 'L' );
        $fpdf->MultiCell(100,5.5,ucwords($alamat),"",'L' );
        //end of header tanda terima faktur

        //isi tanda terima faktur
        $fpdf->ln(5);
        $fpdf->Cell(190,6,"Dengan ini kami ".strtoupper($dataPreference->nama_pt)." telah menerima kwitansi/faktur dengan perincian sebagai berikut :",0,1, 'L' );

        //tabel header
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(9,6,"NO.",1,0, 'C' );
        $fpdf->Cell(49,6,"NO. INVOICE","TRB",0, 'C' );
        $fpdf->Cell(28,6,"TANGGAL INV","TRB",0, 'C' );
        $fpdf->Cell(28,6,"JATUH TEMPO","TRB",0, 'C' );
        $fpdf->Cell(47,6,"NOMOR PO","TRB",0, 'C' );
        $fpdf->Cell(30,6,"NOMINAL","TRB",1, 'C' );
        //end of tabel header

        //tabel isi
        $nmr = 1;
        $fpdf->SetFont('Arial','',8);
        foreach ($detailPurchaseInvoice as $dataItem) {
            $fpdf->Cell(9,5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(49,5,strtoupper($dataItem->kode_invoice),"RB",0, 'L' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_invoice)),"RB",0, 'C' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_jt)),"RB",0, 'C' );
            if($dataItem->no_po_supplier == null){
                $fpdf->Cell(47,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(47,5,strtoupper($dataItem->no_po_supplier),"RB",0, 'L' );
            }
            $fpdf->Cell(5,5," Rp","B",0, 'C' );
            $fpdf->Cell(25,5,number_format($dataItem->grand_total),"RB",1, 'R' );
            $nmr = $nmr + 1;
        }
        //end of tabel isi
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(150,7,"GRAND TOTAL","LB",0, 'R' );
        $fpdf->Cell(5,7,"","B",0, 'R' );
        $fpdf->Cell(6,7,"Rp","B",0, 'L' );
        $fpdf->Cell(30,7,number_format($dataPurchaseInvoice->nominal),"RB",1,"R" );
        //end of isi tanda terima faktur

        //TTD
        $fpdf->ln(5);
        if($dataPurchaseInvoice->status == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(145);
        $fpdf->Cell(45,5,"Diterima oleh,",0,1,"R" );
        $fpdf->SetX(145);
        $fpdf->Cell(45,30,$dataPurchaseInvoice->diterima_oleh,"B",1,"L" );
        //END OF TTD

        //table terbilang
        $fpdf->ln(-37);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(100,5,"TERBILANG",1,1, 'L' );
        $fpdf->MultiCell(100,6, $txtTerbilang,'RLB','L');
        //end of table terbilang

        //REKENING
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','BU',10);
        $fpdf->Cell(60,5,$CompanyAccount->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$CompanyAccount->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,4.5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$CompanyAccount->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,4.5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,4.5,": ".strtoupper($CompanyAccount->atas_nama),0,1, 'L' );
        //END OF REKENING


        return $fpdf;
    }

    public static function cetakKwitansiPdfInvCollection($data)
    {
        $dataPurchaseInvoice = $data['dataPurchaseInvoiceCollection'];
        $dataPurchase = $data['dataPurchase'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailPurchaseInvoice = $data['detailPurchaseInvoiceCollection'];
        $no_kw = str_replace("tf", "KW", $dataPurchaseInvoice->kode_tf);

        $fpdf = new Fpdf;

        $txtTerbilang = Helper::number_to_words($dataPurchaseInvoice->nominal);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah");
        $alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        //header Tanda Terima Faktur
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataPurchaseInvoice->kode_tf));
        $fpdf->SetFont('Arial','U',14);
        $fpdf->Cell(190,7,"KWITANSI",0,1, 'C' );
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(130,6,"Kepada Yth,",0,0, 'L' );
        $fpdf->Cell(15,6,"Nomor",0,0, 'L' );
        $fpdf->Cell(65,6,": ".strtoupper($no_kw),0,1, 'L' );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(130,6,strtoupper($dataPurchaseInvoice->nama_supplier),0,0, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(15,6,"Tanggal",0,0,'L' );
        $fpdf->Cell(65,6,": ".date("d M Y", strtotime($dataPurchaseInvoice->tanggal)),0,1, 'L' );
        $fpdf->MultiCell(100,5.5,ucwords($alamat),"",'L' );
        //end of header tanda terima faktur

        //isi tanda terima faktur
        $fpdf->ln(5);
        $fpdf->Cell(190,6,"Dengan ini kami ".strtoupper($dataPreference->nama_pt)." telah menerima kwitansi/faktur dengan perincian sebagai berikut :",0,1, 'L' );

        //tabel header
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(9,6,"NO.",1,0, 'C' );
        $fpdf->Cell(49,6,"NO. INVOICE","TRB",0, 'C' );
        $fpdf->Cell(28,6,"TANGGAL INV","TRB",0, 'C' );
        $fpdf->Cell(28,6,"JATUH TEMPO","TRB",0, 'C' );
        $fpdf->Cell(47,6,"NOMOR PO","TRB",0, 'C' );
        $fpdf->Cell(30,6,"NOMINAL","TRB",1, 'C' );
        //end of tabel header

        //tabel isi
        $nmr = 1;
        $fpdf->SetFont('Arial','',8);
        foreach ($detailPurchaseInvoice as $dataItem) {
            $fpdf->Cell(9,5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(49,5,strtoupper($dataItem->kode_invoice),"RB",0, 'L' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_invoice)),"RB",0, 'C' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_jt)),"RB",0, 'C' );
            if($dataItem->no_po_supplier == null){
                $fpdf->Cell(47,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(47,5,strtoupper($dataItem->no_po_supplier),"RB",0, 'L' );
            }
            $fpdf->Cell(5,5," Rp","B",0, 'C' );
            $fpdf->Cell(25,5,number_format($dataItem->grand_total),"RB",1, 'R' );
            $nmr = $nmr + 1;
        }
        //end of tabel isi
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(150,7,"GRAND TOTAL","LB",0, 'R' );
        $fpdf->Cell(5,7,"","B",0, 'R' );
        $fpdf->Cell(6,7,"Rp","B",0, 'L' );
        $fpdf->Cell(30,7,number_format($dataPurchaseInvoice->nominal),"RB",1,"R" );
        //end of isi tanda terima faktur

        //TTD
        $fpdf->ln(5);
        if($dataPurchaseInvoice->status == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(100);
        $fpdf->Cell(55,5,"Diterima Oleh,",0,0,"L" );
        $fpdf->Cell(45,5,"Hormat Kami,",0,1,"R" );
        $fpdf->Cell(45,30,"",0,1,"L" );
        $fpdf->SetX(100);
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(40,5,"","B",0,"L" );
        $fpdf->Cell(15,5,"",0,0,"L" );
        $fpdf->Cell(45,5,strtoupper($dataPreference->nama_pt),"",1,"R" );
        //END OF TTD

        //table terbilang
        $fpdf->ln(-42);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,"TERBILANG",1,1, 'L' );
        $fpdf->MultiCell(80,6, $txtTerbilang,'RLB','L');
        //end of table terbilang

        //REKENING
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','BU',10);
        $fpdf->Cell(60,5,$dataPreference->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,4.5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,4.5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,4.5,": ".strtoupper($dataPreference->atas_nama),0,1, 'L' );
        //END OF REKENING


        return $fpdf;
    }
}
