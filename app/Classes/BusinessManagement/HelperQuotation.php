<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;

class HelperQuotation
{
    public static function cetakPdfQuotation($data)
    {
        $dataQuotation = $data['dataQuotation'];
        $dataTerms = $data['dataTerms'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailQuotation = $data['detailQuotation'];
        $taxSettings = $data['taxSettings'];


        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);

        $txt_alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan. ', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.', '.$dataAlamat->kode_pos;

        $fpdf = new Fpdf;
        $user = Auth::user()->user_name;

        // $txtTerbilang = terbilang($dataQuotation->nominal_quotation);
        // $txtTerbilang = strtoupper("#".$txtTerbilang." Rupiah");

        $txtTerbilang = Helper::number_to_words($dataQuotation->nominal_quotation);
        $txtTerbilang = "#".ucwords($txtTerbilang)." Rupiah,-#";

        $bullet = chr(149);

        $fpdf->SetMargins(13,10,13);
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataQuotation->no_quotation));
        $fpdf->Image('images/ajpm.png',20,7);

        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetFont('helvetica','BU',17);
        $fpdf->SetX(50);
        $fpdf->Cell(100,7,strtoupper($dataPreference->nama_pt),0,1,'L');

        //Sub Header
        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetFont('arial','',9.5);
        $fpdf->SetX(50);
        $fpdf->Cell(130,5,strtoupper($dataPreference->alamat_pt),0,1,'L');
        $fpdf->SetX(50);
        $fpdf->Cell(130,5,strtoupper($dataPreference->kelurahan_pt).", ".strtoupper($dataPreference->kecamatan_pt).", ".strtoupper($dataPreference->kota_pt),0,1,'L');
        $fpdf->SetX(50);
        $fpdf->Cell(102,6,strtoupper($dataPreference->telp_pt),0,1,'L');
        $fpdf->SetX(50);
        $fpdf->Cell(50,4,"Website : ".$dataPreference->website_pt,0,1,'L');

        //Table Purchase Order
        $fpdf->ln(2);
        $fpdf->SetFillColor(109,110,113);
        $fpdf->SetFont('arial','B',24);
        $fpdf->Multicell(185,10,'',0,0,true);
        $fpdf->SetTextColor(255,255,255);
        $fpdf->ln(-10);
        $fpdf->Cell(84,10,'QUOTATION',0,0,'L');
        $fpdf->SetFont('arial','B',10);
        $fpdf->Cell(17,5,'',0,0,'L');
        $fpdf->Cell(32,05,'DATE',0,0,'L');
        $fpdf->Cell(2,5,':',0,0,'L');
        $fpdf->Cell(40,5,date("d M Y", strtotime($dataQuotation->tanggal_quotation)),0,1,'L');
        $fpdf->Cell(101,5,'',0,0,'L');
        $fpdf->Cell(32,5,'QUOTATION#',0,0,'L');
        $fpdf->Cell(2,5,':',0,0,'L');
        $fpdf->Cell(40,5, strtoupper($dataQuotation->no_quotation),0,1,'L');
        //Table Purchase Order

        //BILLING ADDRESS
        $fpdf->SetFont('arial','B',11);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->Cell(95,7,'ALAMAT PENAGIHAN : ',0,1,'L');
        $fpdf->SetFont('arial','BU',10);
        $fpdf->Cell(95,6,strtoupper($dataQuotation->nama_customer),0,1,'L');
        $fpdf->SetFont('arial','',9);
        $fpdf->Multicell(95,4.5,$txt_alamat,0,'L');
        $fpdf->Cell(95,4.5,'Telp. '.$dataQuotation->telp_customer,0,1,'L');

        //FOR ATTENTION
        $fpdf->SetFont('arial','BU',11);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->SetXY(115,49);
        $fpdf->Cell(38,7,'UNTUK PERHATIAN : ',0,1,'L');
        $fpdf->SetX(115);
        $fpdf->SetFont('arial','',10);
        $fpdf->Cell(15,6, 'Nama',0,0,'L');
        $fpdf->Cell(40,6, ': '.$dataAlamat->pic_alamat,0,1,'L');
        $fpdf->SetX(115);
        $fpdf->Cell(15,5, 'No. HP',0,0,'L');
        $fpdf->Cell(40,5, ': '.$dataAlamat->telp_pic,0,1,'L');
        $fpdf->SetX(115);
        $fpdf->Cell(15,5, 'E-mail',0,0,'L');
        $fpdf->Cell(40,5, ': '.$dataQuotation->email_customer,0,1,'L');

        //Blok Produk
        $fpdf->ln(7);
        $fpdf->SetFillColor(62,128,194);
        $fpdf->SetFont('arial','B',10);
        $fpdf->SetTextColor(255,255,255);
        $fpdf->Cell(10,7,"NO",'LTB',0, 'C',true);
        $fpdf->Cell(85,7,"SPESIFIKASI",'LTB',0, 'C',true);
        $fpdf->Cell(25,7,"SATUAN",'LTB',0, 'C',true);
        $fpdf->Cell(30,7,"HARGA",'LTB',0, 'C',true);
        $fpdf->Cell(35,7,"KETERANGAN",'LTBR',1, 'C',true);
        $fpdf->SetTextColor(0,0,0);
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($detailQuotation);
        if($countItem > 0){
            foreach ($detailQuotation as $dataItem) {
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->ln(0.5);
                    $fpdf->Cell(10,5,$nmr,0,0, 'C' );
                    $fpdf->Cell(85,5,$dataItem->id_item,0,0, 'L' );
                    $fpdf->Cell(25,5,$dataItem->nama_satuan,0,0, 'C' );
                    if ($dataQuotation->flag_ppn == "I") {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(25,5,number_format(($dataItem->harga_jual / $ppnPercentage),2,",","."),0,0, 'R' );
                    }
                    else {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(25,5,number_format(($dataItem->harga_jual),2,",","."),0,0, 'R' );
                    }
                    $fpdf->SetFont('arial','',9);
                    $fpdf->Cell(35,5,$dataItem->keterangan,0,1, 'L' );
                    $fpdf->SetFont('arial','',9);
                    $nmr++;
                }
                else {
                    $fpdf->Cell(10,4,'',0,0, 'C' );
                    $fpdf->Cell(95,4,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(30,4,'',0,0, 'R' );
                    $fpdf->Cell(20,4,'',0,0, 'L' );
                    $fpdf->Cell(30,4,'',0,1, 'L' );
                }
            }
            $fpdf->SetXY(13,86);
            $panjang = 95;
            foreach ($dataTerms as $terms) {
                $panjang = $panjang - 5;
            }
            $fpdf->Cell(10,$panjang,'','LRB',0, 'C' );
            $fpdf->Cell(85,$panjang,'','RB',0, 'L' );
            $fpdf->Cell(25,$panjang,'','RB',0, 'R' );
            $fpdf->Cell(30,$panjang,'','RB',0, 'L' );
            $fpdf->Cell(35,$panjang,'','RB',1, 'L' );
        }
        // //End of Blok Produk

        $fpdf->ln(3);
        $fpdf->SetFont('arial','',10);
        $fpdf->MultiCell(175,6,'Demikian surat penawaran ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.','','L');

        //Terms & Condition
        $fpdf->SetFillColor(255,255,255);
        $fpdf->ln(4);
        $fpdf->SetFont('arial','B',11);
        $fpdf->Cell(20,6, '', '0', 0, 'C');
        $countTerms = count($dataTerms);
        $fpdf->Cell(85,6,'NOTE :',"B",1,'L');
        $fpdf->SetFont('arial','',9);
        if ($dataQuotation->metode_pembayaran == "credit") {
            $fpdf->Cell(20,6, '', '0', 0, 'C');
            $fpdf->Cell(5,6, $bullet, '0', 0, 'C');
            $fpdf->Cell(135, 6, ' Pembayaran Kredit '.$dataQuotation->durasi_jt.' Hari',0,1,'L');
        }
        else {
            $fpdf->Cell(20,6, '', '0', 0, 'C');
            $fpdf->Cell(5,6, $bullet, '0', 0, 'C');
            $fpdf->Cell(135, 6, ' Pembayaran Tunai',0,1,'L');
        }
        foreach ($dataTerms as $terms) {
            $fpdf->Cell(20,6, '', '0', 0, 'C');
            $fpdf->Cell(5,6,$bullet.' ', '0', 0, 'C');
            $fpdf->MultiCell(135,6, $terms->terms_and_cond,'','L');
        }
        //End of terms & condition

        //Signature
        $fpdf->SetXY(10,271);
        $fpdf->Cell(130,5,"*)) Ini adalah dokumen yang disetujui oleh sistem. Tanda tangan tidak diperlukan.",'B',1, 'L' );
        //End of signature



        if($dataQuotation->status_quotation == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }


        return $fpdf;
    }
}
