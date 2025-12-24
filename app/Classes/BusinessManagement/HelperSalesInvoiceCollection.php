<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceCollectionDetail;

class HelperSalesInvoiceCollection
{
    public static function UpdateInvoice($id, $flag)
    {
        try {
            DB::beginTransaction();
            $listInv = SalesInvoiceCollectionDetail::select('id_invoice')
                        ->where([
                            ['id_tf', '=', $id],
                            ['deleted_at', '=', null]
                        ])
                        ->get();

            if (count($listInv) > 0) {
                foreach ($listInv as $inv) {
                    $dlv = SalesInvoice::find($inv->id_invoice);
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
        $dataSalesInvoice = $data['dataSalesInvoiceCollection'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailSalesInvoice = $data['detailSalesInvoiceCollection'];
        $CompanyAccount = $data['CompanyAccount'];
        $BiayaEkspedisi = $data['BiayaEkspedisi'];

        $fpdf = new Fpdf;

        $txtTerbilang = Helper::number_to_words($dataSalesInvoice->nominal + $BiayaEkspedisi->Biaya);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah");
        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        //header Tanda Terima Faktur
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataSalesInvoice->kode_tf));
        $fpdf->SetFont('Arial','U',14);
        $fpdf->Cell(190,7,"TANDA TERIMA FAKTUR",0,1, 'C' );
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(130,6,"Kepada Yth,",0,0, 'L' );
        $fpdf->Cell(65,6,"Nomor : ".strtoupper($dataSalesInvoice->kode_tf),0,1, 'L' );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(130,6,strtoupper($dataSalesInvoice->nama_customer),0,0, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(65,6,"Tanggal : ".date("d M Y", strtotime($dataSalesInvoice->tanggal)),0,1, 'L' );
        $fpdf->MultiCell(100,5.5,ucwords($alamat),"",'L' );
        //end of header tanda terima faktur

        //isi tanda terima faktur
        $fpdf->ln(5);
        $fpdf->Cell(190,6,"Dengan ini telah menerima kwitansi/ faktur dari ".strtoupper($dataPreference->nama_pt)." dengan perincian sebagai berikut :",0,1, 'L' );

        //tabel header
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(9,6,"NO.",1,0, 'C' );
        $fpdf->Cell(33,6,"NO. INVOICE","TRB",0, 'C' );
        $fpdf->Cell(26,6,"OUTLET","TRB",0, 'C' );
        $fpdf->Cell(28,6,"TANGGAL INV","TRB",0, 'C' );
        $fpdf->Cell(28,6,"JATUH TEMPO","TRB",0, 'C' );
        $fpdf->Cell(37,6,"NOMOR PO","TRB",0, 'C' );
        $fpdf->Cell(30,6,"NOMINAL","TRB",1, 'C' );
        //end of tabel header

        //tabel isi
        $nmr = 1;
        $fpdf->SetFont('Arial','',8);
        foreach ($detailSalesInvoice as $dataItem) {
            $fpdf->Cell(9,5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(33,5,strtoupper($dataItem->kode_invoice),"RB",0, 'L' );
            if($dataItem->nama_outlet == null){
                $fpdf->Cell(26,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(26,5,ucwords($dataItem->nama_outlet),"RB",0, 'L' );
            }
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_invoice)),"RB",0, 'C' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_jt)),"RB",0, 'C' );
            if($dataItem->no_po_customer == null){
                $fpdf->Cell(37,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(37,5,strtoupper($dataItem->no_po_customer),"RB",0, 'L' );
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
        $fpdf->Cell(30,7,number_format($dataSalesInvoice->nominal),"RB",1,"R" );
        //end of isi tanda terima faktur

        //TTD
        $fpdf->ln(5);
        if($dataSalesInvoice->status == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(115);
        $fpdf->Cell(33,5,"Diserahkan Oleh,",0,0,"L" );
        $fpdf->Cell(20,5,"",0,0,"L" );
        $fpdf->Cell(32,5,"Diterima Oleh,",0,1,"L" );
        $fpdf->SetX(115);
        $fpdf->Cell(33,30,"","B",0,"L" );
        $fpdf->Cell(20,30,"",0,0,"L" );
        $fpdf->Cell(33,30,"","B",1,"L" );
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
        $dataSalesInvoice = $data['dataSalesInvoiceCollection'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailSalesInvoice = $data['detailSalesInvoiceCollection'];
        $no_kw = str_replace("tf", "KW", $dataSalesInvoice->kode_tf);

        $fpdf = new Fpdf;

        $txtTerbilang = Helper::number_to_words($dataSalesInvoice->nominal);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah");
        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        //header Tanda Terima Faktur
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataSalesInvoice->kode_tf));
        $fpdf->SetFont('Arial','U',14);
        $fpdf->Cell(190,7,"KWITANSI",0,1, 'C' );
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(130,6,"Kepada Yth,",0,0, 'L' );
        $fpdf->Cell(15,6,"Nomor",0,0, 'L' );
        $fpdf->Cell(65,6,": ".strtoupper($no_kw),0,1, 'L' );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(130,6,strtoupper($dataSalesInvoice->nama_customer),0,0, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(15,6,"Tanggal",0,0,'L' );
        $fpdf->Cell(65,6,": ".date("d M Y", strtotime($dataSalesInvoice->tanggal)),0,1, 'L' );
        $fpdf->MultiCell(100,5.5,ucwords($alamat),"",'L' );
        //end of header tanda terima faktur

        //isi tanda terima faktur
        $fpdf->ln(5);
        $fpdf->Cell(190,6,"Dengan ini telah menerima kwitansi/ faktur dari ".strtoupper($dataPreference->nama_pt)." dengan perincian sebagai berikut :",0,1, 'L' );

        //tabel header
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(9,6,"NO.",1,0, 'C' );
        $fpdf->Cell(33,6,"NO. INVOICE","TRB",0, 'C' );
        $fpdf->Cell(26,6,"OUTLET","TRB",0, 'C' );
        $fpdf->Cell(28,6,"TANGGAL INV","TRB",0, 'C' );
        $fpdf->Cell(28,6,"JATUH TEMPO","TRB",0, 'C' );
        $fpdf->Cell(37,6,"NOMOR PO","TRB",0, 'C' );
        $fpdf->Cell(30,6,"NOMINAL","TRB",1, 'C' );
        //end of tabel header

        //tabel isi
        $nmr = 1;
        $fpdf->SetFont('Arial','',8);
        foreach ($detailSalesInvoice as $dataItem) {
            $fpdf->Cell(9,5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(33,5,strtoupper($dataItem->kode_invoice),"RB",0, 'L' );
            if($dataItem->nama_outlet == null || $dataItem->nama_outlet == "-"){
                $fpdf->Cell(26,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(26,5,ucwords($dataItem->nama_outlet),"RB",0, 'L' );
            }
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_invoice)),"RB",0, 'C' );
            $fpdf->Cell(28,5,date("d M Y", strtotime($dataItem->tanggal_jt)),"RB",0, 'C' );
            if($dataItem->no_po_customer == null){
                $fpdf->Cell(37,5,"-","RB",0, 'C' );
            }
            else{
                $fpdf->Cell(37,5,strtoupper($dataItem->no_po_customer),"RB",0, 'L' );
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
        $fpdf->Cell(30,7,number_format($dataSalesInvoice->nominal),"RB",1,"R" );
        //end of isi tanda terima faktur

        //TTD
        $fpdf->ln(5);
        if($dataSalesInvoice->status == "draft"){
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

    public static function cetakPdfKwitansi($data)
    {
        $dataSalesInvoice = $data['dataSalesInvoiceCollection'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailSalesInvoice = $data['detailSalesInvoiceCollection'];
        $no_kw = str_replace("ttf", "KW", $dataSalesInvoice->kode_tf);

        $fpdf = new Fpdf;


        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;
        if($dataAlamat->kode_pos == null){
            $kodepos = '';
        }
        else{
            $kodepos = ' - '.$dataAlamat->kode_pos;
        }

        //new Page
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($no_kw));
        //end of new Page

        //kop Surat
        $fpdf->Image('images/ajpm.png',25,10);
        $fpdf->SetFont('Arial','B',24);
        $fpdf->SetX(60);
        $fpdf->Cell(110,10,strtoupper($dataPreference->nama_pt),0,1,"C" );
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(60);
        $fpdf->Cell(110,5,"Jalan Imam Bonjol, Gg. Eretan No. 10",0,1,"C" );
        $fpdf->SetX(60);
        $fpdf->Cell(110,5,"RT. 04 RW 06 - Karawaci - Tangerang 15113",0,1,"C" );
        $fpdf->SetX(60);
        $fpdf->Cell(110,5,"Telp. (021) 5514166, 5514149 Fax. (021) 5522402",0,1,"C" );
        $fpdf->SetX(60);
        $fpdf->Cell(110,5,"Website : www.ajputramandiri.com",0,1,"C" );
        $fpdf->Cell(190,5,"","B",1,"C" );



        //end of kop Surat

        //header Tanda Terima Faktur
        $fpdf->SetFont('Arial','BU',14);
        $fpdf->ln(5);
        $fpdf->Cell(190,7,"KWITANSI",0,1, 'C' );
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(130,6,"Kepada Yth,",0,0, 'L' );
        $fpdf->Cell(65,6,"Nomor : ".strtoupper($no_kw),0,1, 'L' );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(130,6,strtoupper($dataSalesInvoice->nama_customer),0,1, 'L' );
        $fpdf->SetFont('Arial','',10);
        $fpdf->MultiCell(100,5.5,ucwords($dataAlamat->alamat_customer),"",'L' );
        $fpdf->MultiCell(100,5.5,ucwords($dataAlamat->kelurahan.', '.$dataAlamat->kecamatan),"",'L' );
        $fpdf->MultiCell(100,5.5,ucwords($dataAlamat->kota.$kodepos),"",'L' );
        //end of header tanda terima faktur

        //tabel header
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(10,6,"NO.",1,0, 'C' );
        $fpdf->Cell(40,6,"TANGGAL","TRB",0, 'C' );
        $fpdf->Cell(50,6,"NO. SURAT JALAN","TRB",0, 'C' );
        $fpdf->Cell(50,6,"NO. INVOICE","TRB",0, 'C' );
        $fpdf->Cell(40,6,"NOMINAL","TRB",1, 'C' );
        //end of tabel header

        //tabel isi
        $nmr = 1;
        $dp = 0;
        foreach ($detailSalesInvoice as $dataItem) {
            $fpdf->Cell(10,5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(40,5,date("d M Y", strtotime($dataItem->tanggal_invoice)),"RB",0, 'C' );
            $fpdf->Cell(50,5,strtoupper($dataItem->kode_pengiriman),"RB",0, 'C' );
            $fpdf->Cell(50,5,strtoupper($dataItem->kode_invoice),"RB",0, 'C' );
            $fpdf->Cell(5,5," Rp ","B",0, 'L' );
            $fpdf->Cell(35,5,number_format($dataItem->grand_total),"RB",1, 'R' );
            $nmr = $nmr + 1;
            $dp = $dp + $dataItem->nominal_dp;
        }
        //end of tabel isi
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(145,7,"                                                                                                                        DP","LB",0, 'L' );
        $fpdf->Cell(5,7,"","B",0, 'R' );
        $fpdf->Cell(5,7,"Rp","B",0, 'L' );
        $fpdf->Cell(35,7,number_format($dp),"RB",1,"R" );
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(145,7,"GRAND TOTAL","LB",0, 'R' );
        $fpdf->Cell(5,7,"","B",0, 'R' );
        $fpdf->Cell(5,7,"Rp","B",0, 'L' );
        $fpdf->Cell(35,7,number_format($dataSalesInvoice->nominal - $dp),"RB",1,"R" );
        //end of isi tanda terima faktur

        $txtTerbilang = Helper::number_to_words($dataSalesInvoice->nominal - $dp);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah#");

        //table terbilang
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(19,5,"Terbilang : ",0,0, 'L' );
        $fpdf->MultiCell(150,5,$txtTerbilang,0,'L');
        //end of table terbilang

        //TTD
        $fpdf->ln(5);
        if($dataSalesInvoice->status == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(145);
        $fpdf->Cell(55,5,"Tangerang, ".date("d F Y", strtotime($dataSalesInvoice->tanggal)),0,1,"C" );
        $fpdf->ln(27);
        $fpdf->SetX(145);
        $fpdf->Cell(55,5,strtoupper($dataPreference->nama_pt),0,1,"C" );
        //END OF TTD

        //REKENING
        $fpdf->ln(-32);
        $fpdf->SetFont('Arial','',8);
        $fpdf->Cell(100,5,"Pembayaran dengan Giro, Cheque dan atau transfer melalui :",0,1, 'L' );
        $fpdf->SetFont('Arial','BU',10);
        $fpdf->Cell(60,5,$dataPreference->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,5,': '.$dataPreference->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,5,': '.$dataPreference->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,5,": ".strtoupper($dataPreference->atas_nama),0,1, 'L' );
        //END OF REKENING


        return $fpdf;
    }
}
