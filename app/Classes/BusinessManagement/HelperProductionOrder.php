<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;

class HelperProductionOrder
{
    public static function cetakPdfPO($data)
    {
        $dataProductionOrder = $data['dataProductionOrder'];
        $dataTerms = $data['dataTerms'];
        $dataPreference = $data['dataPreference'];
        $alamatKirim = $data['alamatKirim'];
        $dataAlamat = $data['dataAlamat'];
        $detailProductionOrder = $data['detailProductionOrder'];
        $dataDetails = $data['dataDetails'];
        $taxSettings = $data['taxSettings'];

        $alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        $txtTerm = "";
        if ($dataProductionOrder->metode_pembayaran == "cash") {
            $txtTerm = "TUNAI";
        }
        else {
            $txtTerm = "KREDIT ".$dataProductionOrder->durasi_jt." Hari";
        }

        $txtDiskon = "";
        $nominalDiskon = $dataProductionOrder->nominal_po_dpp * ($dataProductionOrder->persentase_diskon / 100);
        if ($dataProductionOrder->persentase_diskon > 0)
        {
            $txtDiskon = number_format($nominalDiskon);
        }
        else {
            $txtDiskon = "-";
        }

        $fpdf = new Fpdf;
        if ($dataProductionOrder->nominal_po_ppn == 0 ) {
            $txtPPn = "-";
        }
        else {
            $txtPPn = number_format(($dataProductionOrder->nominal_po_ppn),2,",",".");
        }

        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);

        $txt_alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan. ', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.', '.$dataAlamat->kode_pos;
        $txtAlamatKirim = $alamatKirim->alamat_pt.", ".$alamatKirim->kelurahan_pt.", ".$alamatKirim->kecamatan_pt.", ".$alamatKirim->kota_pt;


        if ($dataProductionOrder->nominal_po_ttl > 0) {
            $txtTerbilang = Helper::number_to_words($dataProductionOrder->nominal_po_ttl);
            $txtTerbilang = "#".ucwords($txtTerbilang)." Rupiah,-#";
        }
        else {
            $txtTerbilang = "#Nol Rupiah,-#";
        }


        //Title Production Order
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataProductionOrder->no_production_order));
        //End of title Production Order

        //Header Production Order
        $fpdf->Image('images/ajpm.png',13,7);

        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetFont('helvetica','BU',17);
        $fpdf->SetX(42);
        $fpdf->Cell(98,7,strtoupper($dataPreference->nama_pt),0,1,'L');
        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetFont('arial','',9);
        $fpdf->SetX(42);
        $fpdf->Cell(88,5,strtoupper($dataPreference->alamat_pt),0,1,'L');
        $fpdf->SetX(42);
        $fpdf->Cell(88,5,strtoupper($dataPreference->kelurahan_pt).", ".strtoupper($dataPreference->kecamatan_pt).", ".strtoupper($dataPreference->kota_pt),0,1,'L');
        $fpdf->SetX(42);
        $fpdf->Cell(88,5,strtoupper("TELP : ".$dataPreference->telp_pt),0,1,'L');
        $fpdf->SetX(42);
        $fpdf->Cell(88,5,"Website : ".$dataPreference->website_pt,0,1,'L');
        $fpdf->SetXY(138,10);
        $fpdf->SetFillColor(109,110,113);
        $fpdf->SetTextColor(255,255,255);
        $fpdf->SetDrawColor(255,255,255);
        $fpdf->SetLineWidth(1);
        $fpdf->Multicell(67,51,'',1,1,true);
        $fpdf->SetXY(135,12);
        $fpdf->SetFont('arial','B',24);
        $fpdf->Cell(69,10,'PRODUCTION',0,1,'R');
        $fpdf->SetX(135);
        $fpdf->Cell(69,10,'ORDER',"B",1,'R');
        $fpdf->SetFont('arial','B',9);
        $fpdf->ln(2);
        $fpdf->SetX(139);
        $fpdf->Cell(37,5,' Production Order No.',0,0,'L');
        $fpdf->Cell(33,5,": ".strtoupper($dataProductionOrder->no_production_order),0,1,'L');
        $fpdf->SetX(139);
        $fpdf->Cell(37,5,' Production Order Date',0,0,'L');
        $fpdf->Cell(33,5,": ".date("d M Y", strtotime($dataProductionOrder->tanggal)),0,1,'L');
        $fpdf->SetX(139);
        $fpdf->Cell(37,5,' Required Date',0,0,'L');
        $fpdf->Cell(33,5,": ".date("d M Y", strtotime($dataProductionOrder->tanggal_request)),0,1,'L');
        $fpdf->SetX(139);
        $fpdf->Cell(37,5,' Expired Date',0,0,'L');
        $fpdf->Cell(33,5,": ".date("d M Y", strtotime($dataProductionOrder->tanggal_deadline)),0,1,'L');
        $fpdf->SetX(139);
        $fpdf->Cell(37,5,'',0,0,'L');
        $fpdf->Cell(33,5,"",0,1,'L');

        //End of header Production Order

        //Alamat supplier / vendor
        $fpdf->ln(-20);
        $fpdf->SetDrawColor(109,110,113);
        $fpdf->Cell(128,2,'','T',1,'R');
        $fpdf->SetFont('arial','B',11);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->Cell(64,6,'ALAMAT VENDOR : ',0,1,'L');
        $fpdf->SetFont('arial','BU',10);
        $fpdf->Cell(64,6,strtoupper($dataProductionOrder->nama_supplier),0,1,'L');
        $fpdf->SetFont('arial','',9);
        $fpdf->Multicell(64,5,$txt_alamat,0,'L');
        $fpdf->Cell(64,5,'Telp. '.$dataProductionOrder->telp_supplier,0,1,'L');
        //End of alamat supplier / vendor

        //Alamat kirim
        $fpdf->setXY(73,41);
        $fpdf->SetFont('arial','B',11);
        $fpdf->Cell(64,6,'ALAMAT KIRIM : ',0,1,'L');
        $fpdf->SetFont('arial','BU',10);
        $fpdf->setX(73);
        $fpdf->Cell(64,6,strtoupper($alamatKirim->nama_pt),0,1,'L');
        $fpdf->SetFont('arial','',9);
        $fpdf->setX(73);
        $fpdf->Multicell(64,5,$txtAlamatKirim,0,'L');
        $fpdf->setX(73);
        $fpdf->Multicell(58,5,'Telp. '.$alamatKirim->telp_pt,0,'L');
        $fpdf->ln(-13);
        $fpdf->Cell(165,15,'','RB',1,'R');
        $fpdf->SetLineWidth(0);
        $fpdf->setXY(72,39);
        $fpdf->Cell(64,36,'','L',1,'L');
        //End of alamat kirim

        //Blok Produk
        $fpdf->SetDrawColor(0,0,0);
        $fpdf->ln(4);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(8,7,"No.",'LTB',0, 'C' );
        $fpdf->Cell(87,7,"Deskripsi Barang",'LTB',0, 'C' );
        $fpdf->Cell(19,7,"Qty",'LTB',0, 'C' );
        $fpdf->Cell(18,7,"Satuan",'LTB',0, 'C' );
        $fpdf->Cell(28,7,"Biaya",'LTB',0, 'C' );
        $fpdf->Cell(35,7,"Subtotal",'LTRB',1, 'C' );
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($dataDetails);
        if($countItem > 0){
            foreach ($dataDetails as $dataItem) {
                if ($dataItem['jenis_item'] != "cetak"){
                    $fpdf->Cell(8,5,$nmr,0,0, 'C' );
                    $fpdf->Cell(87,5,$dataItem['nama_item'],0,0, 'L' );
                    $fpdf->Cell(19,5,number_format(($dataItem['qty_order']),2,",","."),0,0, 'R' );
                    $fpdf->Cell(18,5,$dataItem['nama_satuan'],0,0, 'C' );
                    if ($dataProductionOrder->flag_ppn == "I") {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem['harga'] / $ppnPercentage),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format(($dataItem['subtotal'] / $ppnPercentage),2,",","."),0,1, 'R' );
                    }
                    else {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem['harga']),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format(($dataItem['subtotal']),2,",","."),0,1, 'R' );
                    }
                    $nmr++;
                    foreach($dataItem['spesifikasi'] as $dataSpek) {
                        $fpdf->Cell(11,5,"",0,0, 'C' );
                        $fpdf->Cell(27,5,'** '.$dataSpek['nama_spesifikasi'],0,0, 'L' );
                        $fpdf->Cell(57,5,': '.$dataSpek['value_spesifikasi'],0,1, 'L' );
                    }
                }
                else {
                    $fpdf->Cell(11,4,'',0,0, 'C' );
                    $fpdf->Cell(84,4,$dataItem['nama_item'],0,1, 'L' );
                }
            }
            $fpdf->SetXY(10,86);
            $fpdf->Cell(8,105,'','LRB',0, 'C' );
            $fpdf->Cell(87,105,'','RB',0, 'L' );
            $fpdf->Cell(19,105,'','RB',0, 'R' );
            $fpdf->Cell(18,105,'','RB',0, 'L' );
            $fpdf->Cell(28,105,'','RB',0, 'L' );
            $fpdf->Cell(35,105,'','RB',1, 'L' );
        }
        //End of Blok Produk

        // //blok grand total
        // $fpdf->ln(1);
        // $fpdf->SetFont('Arial','',9);
        // $fpdf->SetX(128);
        // $fpdf->Cell(35,5,"Jumlah Total",0,0,"L" );
        // $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,number_format(($dataProductionOrder->nominal_po_dpp),2,",","."),0,1,"R" );
        // $fpdf->SetX(128);
        // $fpdf->Cell(35,5,"Potongan Harga ".$dataProductionOrder->persentase_diskon."%",0,0,"L" );
        // $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,$txtDiskon,0,1,"R" );
        // $fpdf->SetX(128);
        // if ($dataProductionOrder->ppn > 0){
        //     $fpdf->Cell(35,5,"PPn ".$taxSettings->ppn_percentage."%",0,0,"L" );
        // }
        // else{
        //     $fpdf->Cell(35,5,"PPn ",0,0,"L" );
        // }
        // $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,$txtPPn,0,1,"R" );
        // $fpdf->SetX(128);
        // $fpdf->Cell(35,5,"Ongkos Kirim",0,0,"L" );
        // $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,"-",0,1,"R" );
        // $fpdf->SetX(128);
        // $fpdf->Cell(35,5,"Biaya Lainnya",0,0,"L" );
        // $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,"-",0,1,"R" );
        // $fpdf->SetFont('Arial','B',9);
        // $fpdf->SetX(128);
        // $fpdf->Cell(35,5,"Total Tagihan","T",0,"L" );
        // $fpdf->Cell(10,5,"Rp","T",0,"C" );
        // $fpdf->Cell(32,5,number_format(($dataProductionOrder->nominal_po_ttl),2,",","."),"T",1,"R" );
        // //end of blok grand total

        // //Blok Terbilang
        // $fpdf->ln(-27);
        // $fpdf->SetFont('Arial','',9);
        // $fpdf->Cell(100,5,"TERBILANG",1,1, 'L' );
        // $fpdf->MultiCell(100,5,"$txtTerbilang",0,'L');
        // $fpdf->SetXY(10,200);
        // $fpdf->Cell(100,11,"","LRB",1, 'L' );
        // //end of blok terbilang

        //Keterangan
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(195,5,"Keterangan Tambahan : ",0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(195,5, " - ".$terms->terms_and_cond,'','L');
        }

        if($dataProductionOrder->status == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of keterangan

        //Signature
        $fpdf->SetXY(10,271);
        $fpdf->Cell(130,5,"*)) Ini adalah dokumen yang disetujui oleh sistem. Tanda tangan tidak diperlukan.",'B',1, 'L' );
        //End of signature

        return $fpdf;
    }
}
