<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesOrder;

class HelperKasBank
{
    public static function cetakPdf($data)
    {
        $dataKasBank = $data['dataKasBank'];
        $dataPreference = $data['dataPreference'];
        $detailkasBank = $data['detailkasBank'];
        $kasBank = $dataKasBank->id_account == 1 ? "Kas" : "Bank";
        $jenisTransaksi = $dataKasBank->jenis_transaksi == 1 ? "Masuk" : "Keluar";

        $fpdf = new Fpdf;

        $nominalTerbilang = $dataKasBank->nominal_transaksi;
        $txtTerbilang = Helper::number_to_words($nominalTerbilang);
        $txtTerbilang = ucwords($txtTerbilang)." Rupiah";
        $title = str_replace("/", "_", $dataKasBank->no_kas_bank);

        //Title
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($title));

        //Blok Header
        $fpdf->SetFont('Arial','B',15);
        $fpdf->Cell(190,6,strtoupper($dataPreference->nama_pt), 'LTR',1,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(190,4,ucwords($dataPreference->alamat_pt),'LR',1, 'L' );
        $fpdf->Cell(190,4,ucwords($dataPreference->kelurahan_pt).", ".ucwords($dataPreference->kecamatan_pt).", ".ucwords($dataPreference->kota_pt),'LR',1, 'L' );
        $fpdf->Cell(190,4,"Telp. ".$dataPreference->telp_pt,'LR',1, 'L' );
        $fpdf->Cell(190,4,"",'LR',1, 'L' );
        $fpdf->SetFont('Arial','UB',20);
        $fpdf->Cell(190,4,"Bukti ".$kasBank." ".$jenisTransaksi,'LR',1, 'C' );

        //End of Blok Header

        //Blok Detail
        $fpdf->Cell(190,7,"",'LBR',1,"L" );
        $fpdf->SetFont('Arial','',12);
        $fpdf->Cell(40,5,"Nomor Akun ",'LT',0,"L" );
        $fpdf->Cell(60,5,": ".ucwords($dataKasBank->account_number),'T',0,"L" );
        $fpdf->Cell(40,5,"Tanggal Transaksi ", 'T',0,"L" );
        $fpdf->Cell(50,5,": ".date("d M Y", strtotime($dataKasBank->tanggal_transaksi)), 'TR',1,"L" );
        $fpdf->Cell(40,5,"Nama Akun ", 'L',0,"L" );
        $fpdf->Cell(60,5,": ".ucwords($dataKasBank->account_name),0,0,"L" );
        $fpdf->Cell(40,5,"No. Transaksi ", 0,0,"L" );
        $fpdf->Cell(50,5,": ".strtoupper($dataKasBank->nomor_kas_bank), 'R',1,"L" );
        $fpdf->Cell(40,5,"Nominal ",'L',0,"L" );
        $fpdf->Cell(150,5,": ".number_format(($dataKasBank->nominal_transaksi),2,",","."),'R',1,"L" );
        $fpdf->Cell(40,5,"Terbilang ",'L',0,"L" );
        $fpdf->Cell(150,5,": ".$txtTerbilang, 'R',1,"L" );
        $fpdf->Cell(190,7,"",'LBR',1,"L" );
        //End of Blok Detail

        //Blok Account
        $fpdf->ln(7);
        $fpdf->Cell(10,5.5,"No.",1,0, 'C' );
        $fpdf->Cell(30,5.5,"Nomor Akun","TRB",0, 'C' );
        $fpdf->Cell(50,5.5,"Nama Akun","TRB",0, 'C' );
        $fpdf->Cell(60,5.5,"Keterangan","TRB",0, 'C' );
        $fpdf->Cell(40,5.5,"Nominal","TRB",1, 'C' );
        $nmr = 1;
        $subtotal = 0;
        foreach ($detailkasBank as $dataItem) {
            $fpdf->Cell(10,5.5,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(30,5.5,$dataItem->account_number,"RB",0, 'L' );
            $fpdf->Cell(50,5.5,ucwords($dataItem->account_name),"RB",0, 'L' );
            $fpdf->Cell(60,5.5,$dataItem->keterangan,"RB",0, 'L' );
            $fpdf->Cell(40,5.5,number_format(($dataItem->nominal),2,",","."),"RB",1, 'R' );
            $nmr = $nmr + 1;
            $subtotal = $subtotal + $dataItem->nominal;
        }
        $fpdf->Cell(10,5.5,'',"LRB",0, 'C' );
        $fpdf->Cell(30,5.5,'',"RB",0, 'L' );
        $fpdf->Cell(50,5.5,'',"RB",0, 'L' );
        $fpdf->Cell(60,5.5,'Total',"RB",0, 'C' );
        $fpdf->Cell(40,5.5,number_format(($subtotal),2,",","."),"RB",1, 'R' );
        //End of blok account

        //Blok TTD
        $fpdf->ln(5);
        $fpdf->SetX(115);
        $fpdf->Cell(35,5,"Dibuat Oleh,",0,0,"L" );
        $fpdf->Cell(10,5,"",0,0, 'L' );
        $fpdf->Cell(35,5,"Disetujui Oleh,",0,1,"L" );
        $fpdf->SetX(115);
        $fpdf->Cell(35,30,"","B",0, 'L' );
        $fpdf->Cell(10,30,"",0,0, 'L' );
        $fpdf->Cell(35,30,"","B",1,"L" );
        //END OF Blok TTD



        return $fpdf;
    }
}
