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
use Carbon\Carbon;

class HelperSalesOrder
{
    public static function CancelSO($id)
    {
        $user = Auth::user()->user_name;
        $so = SalesOrder::find($id);
        if ($so != null) {
            try {
                DB::beginTransaction();

                $listSJ = Delivery::select(
                                        'id',
                                        'kode_pengiriman'
                                    )
                                    ->where([
                                        ['id_so', '=', $id],
                                        ['status_pengiriman', '=', 'posted']
                                    ])
                                    ->get();

                if (count($listSJ) > 0) {
                    return "failSJ";
                }
                else {
                    $so->status_so = "batal";
                    $so->updated_by = $user;
                    $so->save();
                }

                DB::commit();
                return "success";
            }
            catch (\Exception $e) {
                DB::rollBack();

                return "failed";
            }
        }
        else {
            return "notFound";
        }
    }

    public static function cetakPdfSO($data)
    {
        $dataSalesOrder = $data['dataSalesOrder'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailSalesOrder = $data['detailSalesOrder'];
        $taxSettings = $data['taxSettings'];

        $persenPPNInclude = (100 + $taxSettings->ppn_percentage) / 100;

        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran);
        }
        else {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran)." ".$dataSalesOrder->durasi_jt." Hari";
        }
        $txtDiskon = "";
        $nominalDiskon = $dataSalesOrder->nominal_so_dpp * ($dataSalesOrder->persentase_diskon / 100);
        if ($dataSalesOrder->persentase_diskon > 0)
        {
            $txtDiskon = number_format($nominalDiskon);
        }
        else {
            $txtDiskon = "-";
        }

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        $txtPPn = "";
        if ($dataSalesOrder->nominal_so_ppn > 0)
        {
            $txtPPn = number_format(($dataSalesOrder->nominal_so_ppn),2,",",".");
        }
        else {
            $txtPPn = "-";
        }

        $txtDp = "";
        if ($dataSalesOrder->nominal_dp > 0)
        {
            $txtDp = number_format($dataSalesOrder->nominal_dp, 0, ',', '.');
        }
        else {
            $txtDp = "-";
        }

        if ($dataAlamat->nama_outlet == "" || $dataAlamat->nama_outlet == "-"){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = "TUNAI";
        }
        else {
            $txtTerm = "Kredit ".$dataSalesOrder->durasi_jt." Hari";
        }

        $nominalTerbilang = $dataSalesOrder->nominal_so_ttl - $dataSalesOrder->nominal_dp;
        $txtTerbilang = Helper::number_to_words($nominalTerbilang);
        $txtTerbilang = "#".ucwords($txtTerbilang)." Rupiah";

        //header sales order
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataSalesOrder->no_so));

        //Blok perusahaan
        $fpdf->SetFont('Arial','BU',13);
        $fpdf->Cell(80,6,strtoupper($dataPreference->nama_pt),0,1,'L');
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(80,4,ucwords($dataPreference->alamat_pt),0,1, 'L' );
        $fpdf->Cell(80,4,ucwords($dataPreference->kelurahan_pt).", ".ucwords($dataPreference->kecamatan_pt).", ".ucwords($dataPreference->kota_pt),0,1, 'L' );
        $fpdf->Cell(33,4,"Telp. ".$dataPreference->telp_pt,0,1, 'L' );
        //$fpdf->Cell(17,4,"| Website :",0,0, 'L' );
        //$fpdf->Cell(35,4,"https://tokohoreka.com ",0,1, 'L' );
        $fpdf->ln(-18);
        $fpdf->Cell(80,18,"",1,1, 'L' );
        //End of Blok perusahaan

        //Blok Detail SO
        $fpdf->ln(1);
        $fpdf->Cell(40,4,"",0,0,"L" );
        $fpdf->Cell(40,4,"",0,1,"L" );
        $fpdf->Cell(40,4," No. SO ",0,0,"L" );
        $fpdf->Cell(40,4,": ".strtoupper($dataSalesOrder->no_so),0,1,"L" );
        $fpdf->Cell(40,4," Tanggal SO ",0,0,"L" );
        $fpdf->Cell(40,4,": ".date("d M Y", strtotime($dataSalesOrder->tanggal_so)),0,1,"L" );
        $fpdf->Cell(40,4," Tanggal Perkiraan Kirim ",0,0,"L" );
        $fpdf->Cell(40,4,": ".date("d M Y", strtotime($dataSalesOrder->tanggal_request)),0,1,"L" );
        $fpdf->Cell(40,4,"",0,1,"L" );
        $fpdf->ln(-21);
        $fpdf->Cell(80,21,"",1,1, 'L' );
        //End of Blok Detail SO

        //Blok Alamat penagihan
        $fpdf->ln(-39);
        $fpdf->SetFont('Arial','B',13);
        $fpdf->SetX(90);
        $fpdf->Cell(115,6,"SALES ORDER",1,1, 'C' );
        $fpdf->SetFont('Arial','BU',10);
        $fpdf->SetX(91);
        $fpdf->Cell(115,5,strtoupper($dataSalesOrder->nama_customer).$outlet,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(91);
        $fpdf->MultiCell(110,4,$alamat,0,'L' );
        $fpdf->SetXY(91,33);
        $fpdf->Cell(25,4,"Telp ",0,0,"L" );
        $fpdf->Cell(90,4,": ".$dataSalesOrder->telp_customer,0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"Kontak ",0,0,"L" );
        $fpdf->Cell(90,4,": ".ucwords($dataAlamat->pic_alamat)." / ".$dataAlamat->telp_pic,0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"No. PO ",0,0,"L" );
        $fpdf->Cell(90,4,": ".strtoupper($dataSalesOrder->no_po_customer),0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"Pembayaran ",0,0,"L" );
        $fpdf->Cell(90,4,": ".$txtTerm,0,1,"L" ); // jika cash, maka cash. jika kredit maka keluar tanggal *udanahelp*
        $fpdf->SetXY(90,16);
        $fpdf->Cell(115,33,"",1,1, 'L' );
        //End of Blok Alamat penagihan

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->Cell(8,5.5,"NO.",1,0, 'C' );
        $fpdf->Cell(87,5.5,"NAMA BARANG","TRB",0, 'C' );
        $fpdf->Cell(17,5.5,"JUMLAH","TRB",0, 'C' );
        $fpdf->Cell(20,5.5,"SATUAN","TRB",0, 'C' );
        $fpdf->Cell(30,5.5,"HARGA SATUAN","TRB",0, 'C' );
        $fpdf->Cell(33,5.5,"HARGA TOTAL","TRB",1, 'C' );
        $nmr = 1;
        foreach ($detailSalesOrder as $dataItem) {
            $fpdf->Cell(8,5.1,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(87,5.1,$dataItem->nama_item,"RB",0, 'L' );
            $fpdf->Cell(17,5.1,number_format(($dataItem->qty_item),2,",","."),"RB",0, 'R' );
            $fpdf->Cell(20,5.1,$dataItem->nama_satuan,"RB",0, 'L' );
            $fpdf->Cell(5,5.1,"Rp","B",0, 'L' );
            if ($dataSalesOrder->flag_ppn == "I") {
                $fpdf->Cell(25,4.7,number_format($dataItem->harga_jual / $persenPPNInclude),"RB",0, 'R' );
                $fpdf->Cell(5,4.7,"Rp","B",0, 'L' );
                $fpdf->Cell(28,4.7,number_format($dataItem->subtotal / $persenPPNInclude),"RB",1, 'R' );
            }
            else {
                $fpdf->Cell(25,5.1,number_format(($dataItem->harga_jual),2,",","."),"RB",0, 'R' );
                $fpdf->Cell(5,5.1,"Rp","B",0, 'L' );
                $fpdf->Cell(28,5.1,number_format(($dataItem->subtotal),2,",","."),"RB",1, 'R' );
            }
            $nmr = $nmr + 1;
        }
        //End of blok produk

        //blok grand total
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"SUBTOTAL",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,number_format(($dataSalesOrder->nominal_so_dpp),2,",","."),0,1,"R" );
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"DISKON ".$dataSalesOrder->persentase_diskon."%",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtDiskon,0,1,"R" );
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"PPn",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtPPn,0,1,"R" );
        $fpdf->SetX(128);

        if ($dataSalesOrder->nominal_dp > 0) {
            $fpdf->Cell(35,5,"UANG MUKA",0,0,"L" );
            $fpdf->Cell(10,5,"Rp",0,0,"C" );
            $fpdf->Cell(32,5,$txtDp,0,1,"R" );
        }

        $fpdf->SetFont('Arial','B',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"JUMLAH TOTAL","T",0,"L" );
        $fpdf->Cell(10,5,"Rp","T",0,"C" );
        if ($dataSalesOrder->nominal_dp == 0) {
            $fpdf->Cell(32,5,number_format(($dataSalesOrder->nominal_so_ttl),2,",","."),"T",1,"R" );
        }
        else {
            $fpdf->Cell(32,5,number_format(($dataSalesOrder->nominal_so_ttl - $dataSalesOrder->nominal_dp),2,",","."),"T",1,"R" );
        }
        //end of blok grand total

        //Blok Terbilang
        if ($dataSalesOrder->nominal_dp > 0) {
            $fpdf->ln(-23);
        }
        else{
            $fpdf->ln(-18);
        }
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(100,5,"TERBILANG",1,1, 'L' );
        $fpdf->MultiCell(100,5,"$txtTerbilang",1,'L');
        //end of blok terbilang

        //REKENING
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','BU',9);
        $fpdf->Cell(60,5,$dataPreference->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,4.5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,4.5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,4.5,": ".strtoupper($dataPreference->atas_nama),0,1, 'L' );
        //END OF REKENING

        //Blok TTD
        $fpdf->ln(-9);
        $fpdf->SetX(115);
        $fpdf->Cell(35,5,"Dibuat Oleh,",0,0,"L" );
        $fpdf->Cell(10,5,"",0,0, 'L' );
        $fpdf->Cell(35,5,"Disetujui Oleh,",0,1,"L" );
        $fpdf->SetX(115);
        $fpdf->Cell(35,30,"","B",0, 'L' );
        $fpdf->Cell(10,30,"",0,0, 'L' );
        $fpdf->Cell(35,30,"","B",1,"L" );
        //END OF Blok TTD

        //Information
        $fpdf->ln(-23);
        $fpdf->Cell(85,6,'CATATAN SO',1,1,'L');
        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(85,6, " - ".$terms->terms_and_cond,'LR','L');
        }
        if ($countList == 0) {
             $fpdf->Cell(85,22,'','LR',1,'L');
        }
        $fpdf->Cell(85,1,'','LRB',1,'L');

        if($dataSalesOrder->status_invoice == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of Information


        return $fpdf;
    }

    public static function cetakPdfInvDP($data)
    {
        $dataSalesOrder = $data['dataSalesOrder'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailSalesOrder = $data['detailSalesOrder'];
        $taxSettings = $data['taxSettings'];

        $persenPPNInclude = (100 + $taxSettings->ppn_percentage) / 100;


        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran);
        }
        else {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran)." ".$dataSalesOrder->durasi_jt." Hari";
        }

        $txtDiskon = "";

        if ($dataSalesOrder->jenis_diskon == "P") {
            $nominalDiskon = $dataSalesOrder->nominal_so_dpp * ($dataSalesOrder->persentase_diskon / 100);
            if ($dataSalesOrder->persentase_diskon > 0)
            {
                $txtDiskon = number_format($nominalDiskon,2,",",".");
            }
            else {
                $txtDiskon = "-";
            }
        }
        elseif ($dataSalesOrder->jenis_diskon == "N") {
            $nominalDiskon = $dataSalesOrder->nominal_diskon;
            if ($dataSalesOrder->nominal_diskon > 0)
            {
                $txtDiskon = number_format($nominalDiskon,2,",",".");
            }
            else {
                $txtDiskon = "-";
            }
        }

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        $txtPPn = "";
        if ($dataSalesOrder->nominal_so_ppn > 0)
        {
            $txtPPn = number_format($dataSalesOrder->nominal_so_ppn);
        }
        else {
            $txtPPn = "-";
        }

        $txtDp = "";
        if ($dataSalesOrder->nominal_dp > 0)
        {
            $txtDp = number_format($dataSalesOrder->nominal_dp, 0, ',', '.');
        }
        else {
            $txtDp = "-";
        }

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = "TUNAI";
        }
        else {
            $txtTerm = "Kredit ".$dataSalesOrder->durasi_jt." Hari (".date("d M Y", strtotime($dataSalesOrder->tanggal_jt)).")";
        }

        $no_inv = str_replace("so", "invdp", $dataSalesOrder->no_so);

        $nominalTerbilang = $dataSalesOrder->nominal_so_ttl - $dataSalesOrder->nominal_dp;
        $txtTerbilang = Helper::number_to_words($nominalTerbilang);
        $txtTerbilang = "#".ucwords($txtTerbilang)." Rupiah";

        //header sales order
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($no_inv));

        //Blok perusahaan
        $fpdf->SetFont('Arial','BU',13);
        $fpdf->Cell(80,6,strtoupper($dataPreference->nama_pt),0,1,'L');
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(80,4,ucwords($dataPreference->alamat_pt),0,1, 'L' );
        $fpdf->Cell(80,4,ucwords($dataPreference->kelurahan_pt).", ".ucwords($dataPreference->kecamatan_pt).", ".ucwords($dataPreference->kota_pt),0,1, 'L' );
        $fpdf->Cell(33,4,"Telp. ".$dataPreference->telp_pt,0,1, 'L' );
        //$fpdf->Cell(17,4,"| Website :",0,0, 'L' );
        //$fpdf->Cell(35,4,"https://tokohoreka.com ",0,1, 'L' );
        $fpdf->ln(-18);
        $fpdf->Cell(80,18,"",1,1, 'L' );
        //End of Blok perusahaan

        //Blok Detail SO
        $fpdf->ln(1);
        $fpdf->Cell(40,4,"",0,0,"L" );
        $fpdf->Cell(40,4,"",0,1,"L" );
        $fpdf->Cell(40,4," No. Proforma Invoice ",0,0,"L" );
        $fpdf->Cell(40,4,": ".strtoupper($no_inv),0,1,"L" );
        $fpdf->Cell(37,4," Tanggal SO ",0,0,"L" );
        $fpdf->Cell(40,4,": ".date("d M Y", strtotime($dataSalesOrder->tanggal_so)),0,1,"L" );
        $fpdf->Cell(37,4," Tanggal Perkiraan Kirim ",0,0,"L" );
        $fpdf->Cell(40,4,": ".date("d M Y", strtotime($dataSalesOrder->tanggal_request)),0,1,"L" );
        $fpdf->Cell(40,4,"",0,1,"L" );
        $fpdf->ln(-21);
        $fpdf->Cell(80,21,"",1,1, 'L' );
        //End of Blok Detail SO

        //Blok Alamat penagihan
        $fpdf->ln(-39);
        $fpdf->SetFont('Arial','B',13);
        $fpdf->SetX(90);
        $fpdf->Cell(115,6,"PROFORMA INVOICE",1,1, 'C' );
        $fpdf->SetFont('Arial','BU',10);
        $fpdf->SetX(91);
        $fpdf->Cell(115,5,strtoupper($dataSalesOrder->nama_customer).$outlet,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(91);
        $fpdf->MultiCell(110,4,$alamat,0,'L' );
        $fpdf->SetXY(91,33);
        $fpdf->Cell(25,4,"Telp ",0,0,"L" );
        $fpdf->Cell(90,4,": ".$dataSalesOrder->telp_customer,0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"Kontak ",0,0,"L" );
        $fpdf->Cell(90,4,": ".ucwords($dataAlamat->pic_alamat)." / ".$dataAlamat->telp_pic,0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"No. PO ",0,0,"L" );
        $fpdf->Cell(90,4,": ".strtoupper($dataSalesOrder->no_po_customer),0,1,"L" );
        $fpdf->SetX(91);
        $fpdf->Cell(25,4,"Pembayaran ",0,0,"L" );
        $fpdf->Cell(90,4,": ".$txtTerm,0,1,"L" ); // jika cash, maka cash. jika kredit maka keluar tanggal *udanahelp*
        $fpdf->SetXY(90,16);
        $fpdf->Cell(115,33,"",1,1, 'L' );
        //End of Blok Alamat penagihan

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->Cell(8,5,"NO.",1,0, 'C' );
        $fpdf->Cell(87,5,"NAMA BARANG","TRB",0, 'C' );
        $fpdf->Cell(17,5,"JUMLAH","TRB",0, 'C' );
        $fpdf->Cell(20,5,"SATUAN","TRB",0, 'C' );
        $fpdf->Cell(30,5,"HARGA SATUAN","TRB",0, 'C' );
        $fpdf->Cell(33,5,"HARGA TOTAL","TRB",1, 'C' );
        $nmr = 1;
        $fpdf->SetFont('Arial','',8);
        foreach ($detailSalesOrder as $dataItem) {
            $fpdf->Cell(8,4.7,$nmr,"LRB",0, 'C' );
            $fpdf->Cell(87,4.7,$dataItem->nama_item,"RB",0, 'L' );
            $fpdf->Cell(17,4.7,number_format($dataItem->qty_item),"RB",0, 'R' );
            $fpdf->Cell(20,4.7,$dataItem->nama_satuan,"RB",0, 'L' );
            $fpdf->Cell(5,4.7,"Rp","B",0, 'L' );
            if ($dataSalesOrder->flag_ppn == "I") {
                $fpdf->Cell(25,4.7,number_format($dataItem->harga_jual / $persenPPNInclude),"RB",0, 'R' );
                $fpdf->Cell(5,4.7,"Rp","B",0, 'L' );
                $fpdf->Cell(28,4.7,number_format($dataItem->subtotal / $persenPPNInclude),"RB",1, 'R' );
            }
            else {
                $fpdf->Cell(25,4.7,number_format($dataItem->harga_jual),"RB",0, 'R' );
                $fpdf->Cell(5,4.7,"Rp","B",0, 'L' );
                $fpdf->Cell(28,4.7,number_format($dataItem->subtotal),"RB",1, 'R' );
            }
            $nmr = $nmr + 1;
        }
        //End of blok produk

        //blok grand total
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"SUBTOTAL",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,number_format($dataSalesOrder->nominal_so_dpp),0,1,"R" );
        $fpdf->SetX(128);
        if ($dataSalesOrder->persentase_diskon == "P") {
            $fpdf->Cell(35,5,"DISKON ".$dataSalesOrder->persentase_diskon."%",0,0,"L" );
        }
        else {
            $fpdf->Cell(35,5,"DISKON ",0,0,"L" );
        }
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtDiskon,0,1,"R" );
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"PPn",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtPPn,0,1,"R" );
        $fpdf->SetFont('Arial','B',9);
        $fpdf->SetX(128);

        if ($dataSalesOrder->nominal_dp > 0) {
            $fpdf->Cell(35,5,"UANG MUKA",0,0,"L" );
            $fpdf->Cell(10,5,"Rp",0,0,"C" );
            $fpdf->Cell(32,5,$txtDp,0,1,"R" );
        }

        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"JUMLAH TOTAL","T",0,"L" );
        $fpdf->Cell(10,5,"Rp","T",0,"C" );
        if ($dataSalesOrder->nominal_dp == 0) {
            $fpdf->Cell(32,5,number_format($dataSalesOrder->nominal_so_ttl),"T",1,"R" );
        }
        else {
            $fpdf->Cell(32,5,number_format($dataSalesOrder->nominal_so_ttl - $dataSalesOrder->nominal_dp),"T",1,"R" );
        }
        //end of blok grand total

        //Blok Terbilang
        if ($dataSalesOrder->nominal_dp > 0) {
            $fpdf->ln(-23);
        }
        else{
            $fpdf->ln(-18);
        }
        $fpdf->Cell(100,5,"TERBILANG (UANG MUKA)",1,1, 'L' );
        $fpdf->MultiCell(100,5,"$txtTerbilang",1,'L');
        //end of blok terbilang

        //REKENING
        $fpdf->ln(2);
        $fpdf->SetFont('Arial','BU',9);
        $fpdf->Cell(60,5,$dataPreference->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,4.5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,4.5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,4.5,": ".strtoupper($dataPreference->atas_nama),0,1, 'L' );
        //END OF REKENING

        //Blok TTD
        $fpdf->ln(-9);
        $fpdf->SetX(115);
        $fpdf->Cell(35,5,"Dibuat Oleh,",0,0,"L" );
        $fpdf->Cell(10,5,"",0,0, 'L' );
        $fpdf->Cell(35,5,"Disetujui Oleh,",0,1,"L" );
        $fpdf->SetX(115);
        $fpdf->Cell(35,30,"","B",0, 'L' );
        $fpdf->Cell(10,30,"",0,0, 'L' );
        $fpdf->Cell(35,30,"","B",1,"L" );
        //END OF Blok TTD

        //Information
        $fpdf->ln(-23);
        $fpdf->Cell(85,6,'CATATAN SO',1,1,'L');
        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(85,6, " - ".$terms->terms_and_cond,'LR','L');
        }
        if ($countList == 0) {
             $fpdf->Cell(85,22,'','LR',1,'L');
        }
        $fpdf->Cell(85,1,'','LRB',1,'L');

        if($dataSalesOrder->status_invoice == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of Information

        return $fpdf;
    }

    public static function cetakPdfInvPelunasan($data)
    {
        $dataSalesOrder = $data['dataSalesOrder'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $dataAlamatTagih = $data['dataAlamatPenagihan'];
        $detailSalesOrder = $data['detailSalesOrder'];
        $taxSettings = $data['taxSettings'];
        $dataShipDate = $data['shipDate'];

        $persenPPNInclude = (100 + $taxSettings->ppn_percentage) / 100;

        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        if ($dataAlamatTagih != null) {
            $alamatTagih = $dataAlamatTagih->alamat_customer.', '.$dataAlamatTagih->kelurahan.', '.$dataAlamatTagih->kecamatan.', '.$dataAlamatTagih->kota.' - '.$dataAlamatTagih->kode_pos;
            $picTagih = $dataAlamatTagih->pic_alamat;
            $telpPicTagih = $dataAlamatTagih->telp_pic;
            if ($dataAlamatTagih->nama_outlet == ""){
                $outletTagih = "";
            }
            else {
                $outletTagih = " - ".$dataAlamatTagih->nama_outlet;
            }

            if ($dataAlamatTagih->pic_alamat == ""){
                $picTagih = "-";
            }
            else {
                $picTagih = $dataAlamatTagih->pic_alamat;
            }

            if ($dataAlamatTagih->telp_pic == ""){
                $telpPicTagih = "-";
            }
            else {
                $telpPicTagih = $dataAlamatTagih->telp_pic;
            }
        }
        else {
            $alamatTagih = "-";
            $picTagih = "-";
            $telpPicTagih = "-";
            $outletTagih = "";
        }

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran);
        }
        else {
            $txtTerm = strtoupper($dataSalesOrder->metode_pembayaran)." ".$dataSalesOrder->durasi_jt." Hari";
        }
        
        $txtDiskon = "";

        if ($dataSalesOrder->jenis_diskon == "P") {
            $nominalDiskon = $dataSalesOrder->nominal_so_dpp * ($dataSalesOrder->persentase_diskon / 100);
            if ($dataSalesOrder->persentase_diskon > 0)
            {
                $txtDiskon = number_format($nominalDiskon,2,",",".");
            }
            else {
                $txtDiskon = "-";
            }
        }
        elseif ($dataSalesOrder->jenis_diskon == "N") {
            $nominalDiskon = $dataSalesOrder->nominal_diskon;
            if ($dataSalesOrder->nominal_diskon > 0)
            {
                $txtDiskon = number_format($nominalDiskon,2,",",".");
            }
            else {
                $txtDiskon = "-";
            }
        }

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        $txtPPn = "";
        if ($dataSalesOrder->nominal_so_ppn > 0)
        {
            $txtPPn = number_format($dataSalesOrder->nominal_so_ppn);
        }
        else {
            $txtPPn = "-";
        }
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);

        $txtDp = "";
        if ($dataSalesOrder->nominal_dp > 0)
        {
            $txtDp = number_format($dataSalesOrder->nominal_dp, 0, ',', '.');
        }
        else {
            $txtDp = "-";
        }

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }

        $txtTerm = "";
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $txtTerm = "TUNAI";
        }
        else {
            $txtTerm = "Kredit ".$dataSalesOrder->durasi_jt." Hari (".date("d M Y", strtotime($dataSalesOrder->tanggal_jt)).")";
        }

        $no_inv = str_replace("so", "PINV", $dataSalesOrder->no_so);

        $nominalTerbilang = $dataSalesOrder->nominal_so_ttl - $dataSalesOrder->nominal_dp;
        $txtTerbilang = Helper::number_to_words($nominalTerbilang);
        $txtTerbilang = "#".ucwords($txtTerbilang)." Rupiah";

        //header PROFORMA INVOICE
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($no_inv));
        //end of header PROFORMA INVOICE

        //Blok perusahaan
        $fpdf->SetFont('Arial','B',17);
        $fpdf->ln(1);
        $fpdf->Cell(130,7,strtoupper($dataPreference->nama_pt),0,1,'L');
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(130,4,ucwords($dataPreference->alamat_pt.", ".$dataPreference->kelurahan_pt).", ".ucwords($dataPreference->kecamatan_pt).", ".ucwords($dataPreference->kota_pt),0,1, 'L' );
        $fpdf->Cell(130,4,"Telp. ".$dataPreference->telp_pt,0,1, 'L' );
        $fpdf->Cell(130,4,"Email : ".$dataPreference->email_pt.", ".$dataPreference->website_pt,0,1, 'L' );
        $fpdf->ln(-20);
        $fpdf->Cell(130,21,"","TLR",1, 'L' );
        //End of Blok perusahaan

        //Blok Alamat
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(65,5,"Alamat Kirim",'LTR',0, 'C' );
        $fpdf->Cell(65,5,"Alamat Penagihan",'RTB',1, 'C' );
        //End of Blok Alamat

        //Blok Detail Alamat
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(65,5,$dataSalesOrder->nama_customer.$outlet,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->MultiCell(65,4,$alamat,0,'L' );
        $fpdf->SetY(59);
        $fpdf->Cell(65,5,"U.P. ".$dataAlamat->pic_alamat." | Tlp. ".$dataAlamat->telp_pic,0,1, 'L' );
        $fpdf->SetY(36);
        $fpdf->Cell(65,29,"",1,1, 'L' );

        $fpdf->SetXY(75,36);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(65,5,$dataSalesOrder->nama_customer.$outletTagih,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(75);
        $fpdf->MultiCell(65,4,$alamatTagih,0,'L' );
        $fpdf->SetXY(75,59);
        $fpdf->Cell(65,5,"U.P. ".$picTagih." | Tlp. ".$telpPicTagih,0,1, 'L' );
        $fpdf->SetXY(75,36);
        $fpdf->Cell(65,29,"",'RB',1, 'L' );
        //End of Blok Detail Alamat

        //Blok Detail Faktur Penjualan
        $fpdf->SetFont('Arial','B',16);
        $fpdf->SetXY(140,11);
        $fpdf->Cell(65,7,"PROFORMA INVOICE",0,1, 'C' );
        $fpdf->ln(-8);
        $fpdf->SetX(140);
        $fpdf->Cell(65,8,"","TRB",1, 'C' );
        $fpdf->ln(1);
        $fpdf->SetX(140);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(24,5.5," Nomor ",0,0,"L" );
        $fpdf->Cell(41,5.5,": ".strtoupper($no_inv),0,1,"L" );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(140);
        $fpdf->Cell(24,5.5," Tanggal Invoice ",0,0,"L" );
        $fpdf->Cell(41,5.5,": ".Carbon::parse($dataSalesOrder->tanggal_so)->isoFormat('D MMMM Y'),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(24,5.5," Perkiraan Kirim ",0,0,"L" );
        $fpdf->Cell(41,5.5,": ".Carbon::parse($dataSalesOrder->tanggal_request)->isoFormat('D MMMM Y'),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(24,5.5," No. PO ",0,0,"L" );
        $fpdf->Cell(2,5.5,":",0,0,"L" );
        $fpdf->MultiCell(41,5.5,strtoupper($dataSalesOrder->no_po_customer),0,'L' );
        $fpdf->SetX(140);
        $fpdf->Cell(24,5.5," Pembayaran",0,0,"L" );
        $fpdf->SetFont('Arial','B',9);
        if ($dataSalesOrder->metode_pembayaran == "cash") {
            $fpdf->Cell(41,5.5,": Tunai",0,1,"L" );
        }
        else {
            $fpdf->Cell(41,5.5,": Kredit ".$dataSalesOrder->durasi_jt." Hari",0,1,"L" );

        }
        $fpdf->SetXY(140,18);
        $fpdf->Cell(65,47,"","RB",1,"L" );
        //End of Blok Detail Faktur Penjualan

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(8,7,"No.",'LTB',0, 'C' );
        $fpdf->Cell(87,7,"Deskripsi Barang",'LTB',0, 'C' );
        $fpdf->Cell(19,7,"Qty",'LTB',0, 'C' );
        $fpdf->Cell(18,7,"Satuan",'LTB',0, 'C' );
        $fpdf->Cell(28,7,"Harga Satuan",'LTB',0, 'C' );
        $fpdf->Cell(35,7,"Jumlah",'LTRB',1, 'C' );
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($detailSalesOrder);
        if($countItem > 0){
            foreach ($detailSalesOrder as $dataItem) {
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->Cell(8,5,$nmr,0,0, 'C' );
                    $fpdf->Cell(87,5,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(19,5,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' );
                    $fpdf->Cell(18,5,$dataItem->nama_satuan,0,0, 'C' );
                    if ($dataSalesOrder->flag_ppn == "I") {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem->harga_jual / $ppnPercentage),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format(($dataItem->subtotal / $ppnPercentage),2,",","."),0,1, 'R' );
                    }
                    else {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem->harga_jual),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format(($dataItem->subtotal),2,",","."),0,1, 'R' );
                    }
                    $nmr++;
                }
                else {
                    $fpdf->Cell(10,4,'',0,0, 'C' );
                    $fpdf->Cell(90,4,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(25,4,'',0,0, 'R' );
                    $fpdf->Cell(25,4,'',0,0, 'L' );
                    $fpdf->Cell(45,4,'',0,1, 'L' );
                }
            }
            $fpdf->SetXY(10,75);
            $fpdf->Cell(8,120,'','LRB',0, 'C' );
            $fpdf->Cell(87,120,'','RB',0, 'L' );
            $fpdf->Cell(19,120,'','RB',0, 'R' );
            $fpdf->Cell(18,120,'','RB',0, 'L' );
            $fpdf->Cell(28,120,'','RB',0, 'L' );
            $fpdf->Cell(35,120,'','RB',1, 'L' );
        }
        //End of Blok Produk

        //blok grand total
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"Jumlah Total",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,number_format(($dataSalesOrder->nominal_so_dpp),2,",","."),0,1,"R" );
        $fpdf->SetX(128);
        if ($dataSalesOrder->jenis_diskon == "P") {
            $fpdf->Cell(35,5,"Diskon ".$dataSalesOrder->persentase_diskon."%",0,0,"L" );
        }
        else {
            $fpdf->Cell(35,5,"Diskon",0,0,"L" );
        }
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtDiskon,0,1,"R" );
        $fpdf->SetX(128);
        if ($dataSalesOrder->ppn > 0){
            $fpdf->Cell(35,5,"PPn ".$taxSettings->ppn_percentage."%",0,0,"L" );
        }
        else{
            $fpdf->Cell(35,5,"PPn ",0,0,"L" );
        }
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtPPn,0,1,"R" );
        $fpdf->SetFont('Arial','B',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"Total Tagihan","T",0,"L" );
        $fpdf->Cell(10,5,"Rp","T",0,"C" );
        $fpdf->Cell(32,5,number_format(($dataSalesOrder->nominal_so_ttl),2,",","."),"T",1,"R" );
        //end of blok grand total

        //Blok TTD
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(165);
        $fpdf->Cell(35,5,"Tangerang, ".Carbon::parse($dataSalesOrder->tanggal_so)->isoFormat('D MMMM Y'),0,1,"R" );
        $fpdf->SetX(165);
        $fpdf->Cell(35,30,"",0,1,"C" );
        $fpdf->SetX(165);
        $fpdf->Cell(35,5,strtoupper($dataPreference->nama_pt),0,1,"R" );
        //END OF Blok TTD

        //Blok Terbilang
        $fpdf->ln(-64);
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(100,5,"TERBILANG",1,1, 'L' );
        $fpdf->MultiCell(100,5,"$txtTerbilang",0,'L');
        $fpdf->SetXY(10,202);
        $fpdf->Cell(100,11,"",1,1, 'L' );
        //end of blok terbilang

        //REKENING
        $fpdf->SetXY(10,236);
        $fpdf->MultiCell(100,5,"Pembayaran dengan Giro, Cheque dan atau transfer melalui :",0,'L');
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','BU',9);
        $fpdf->Cell(60,5,$dataPreference->nama_bank,0,1, 'L' );
        $fpdf->SetFont('Arial','',9);
        $fpdf->Cell(30,5,"KODE BANK",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->kode_bank,0,1, 'L' );
        $fpdf->Cell(30,4.5,"NO. REKENING",0,0, 'L' );
        $fpdf->Cell(60,4.5,': '.$dataPreference->nomor_rekening,0,1, 'L' );
        $fpdf->Cell(30,4.5,"ATAS NAMA",0,0, 'L' );
        $fpdf->Cell(60,4.5,": ".strtoupper($dataPreference->atas_nama),0,1, 'L' );
        //END OF REKENING

        if($dataSalesOrder->status_so == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }

        return $fpdf;
    }
}
