<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;

class HelperExpeditionCost
{
    public static function cetakPdfCost($data)
    {
        $dataExpeditionCost = $data['dataExpeditionCost'];
        $dataPreference = $data['dataPreference'];
        $detailExpeditionCost = $data['detailExpeditionCost'];


        $fpdf = new Fpdf;

        //Title Receiving
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper("Cetak Biaya Ekspedisi"));
        //End of Title Receiving

        //Body
        $nmr = 1;

        foreach ($detailExpeditionCost as $dataItem) {
            $alamat = $dataItem->alamat_customer;
            $kelurahan = ', '.$dataItem->kelurahan;
            $kecamatan = ', '.$dataItem->kecamatan;
            $kota = ', '.$dataItem->kota;
            $kodepos = "";
            
            if($dataItem->kode_pos != null){
                $kodepos = ' - '.$dataItem->kode_pos;
            }
            $txt_alamat = $alamat.$kelurahan.$kecamatan.$kota.$kodepos;
            
            $fpdf->ln(5);
            $fpdf->SetFont('Arial','B',11);
            $fpdf->Cell(100,5,$nmr.".  ".strtoupper($dataPreference->nama_pt),0,0, 'L');
            $fpdf->Cell(90,5,"KIRIMAN KE ".$dataItem->kota_tujuan,0,1, 'R');

            $fpdf->SetFont('Arial','B',10);
            $fpdf->Cell(100,6,"NAMA",'LTB',0, 'C' );
            $fpdf->Cell(45,6,"KOLI",'LTB',0, 'C' );
            $fpdf->Cell(45,6,"BERAT/VOL",'1',1, 'C' );
            //VARIABLE
            //$dataItem->tarif;
            //$dataItem->jumlah;
            //$dataItem->berat;
            //$dataItem->subtotal;
            //$dataItem->kota_tujuan;
            //$dataItem->nama_resi;
            $fpdf->SetFont('Arial','',10);
            $fpdf->Cell(100,6,$dataItem->nama_resi,'LB',0, 'C' );
            $fpdf->Cell(45,6,number_format(($dataItem->jumlah),0,",","."),'LB',0, 'C' );
            $fpdf->Cell(35,6,number_format(($dataItem->berat),0,",","."),'LB',0, 'C' );
            $fpdf->Cell(10,6,"",'BR',1, 'R' );

            $fpdf->SetLineWidth(0.6);
            $fpdf->Cell(100,6,"TOTAL KOLI",'TLB',0, 'R' );
            $fpdf->Cell(45,6,number_format(($dataItem->jumlah),0,",","."),'TB',0, 'C' );
            $fpdf->Cell(35,6,number_format(($dataItem->berat),0,",","."),'TB',0, 'C' );
            $fpdf->Cell(10,6,"KG",'TBR',1, 'R' );

            $fpdf->SetLineWidth(0.2);
            $fpdf->Cell(150,6,strtoupper($dataItem->nama_customer)." / ".$dataItem->telp_pic,0,0, 'L' );
            $fpdf->Cell(40,6,number_format(($dataItem->tarif),0,",","."),0,1, 'L' );
            $fpdf->Multicell(100,5,$txt_alamat,'','L');
            $fpdf->ln(-5);
            $fpdf->SetFont('Arial','B',12);
            $fpdf->Cell(145,6,"SUBTOTAL",0,0, 'R');
            $fpdf->Cell(15,6,"Rp",0,0, 'R' );
            $fpdf->Cell(30,6,number_format(($dataItem->subtotal),0,",","."),0,0, 'R' );
            $fpdf->ln(2);
            $fpdf->Cell(190,5,"",'B',1, '');

            $nmr = $nmr + 1;
        }
        //End of Body

        //footer
        if($dataExpeditionCost->status_biaya == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of footer

        return $fpdf;
    }
}
