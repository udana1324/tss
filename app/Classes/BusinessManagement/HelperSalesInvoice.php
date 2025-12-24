<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use Carbon\Carbon;

class HelperSalesInvoice
{
    public static function CancelInvoice($id)
    {
        $user = Auth::user()->user_name;
        $inv = SalesInvoice::find($id);
        if ($inv != null) {
            try {
                DB::beginTransaction();

                $inv->status_invoice = "batal";
                $inv->updated_by = $user;
                $inv->save();

                $salesInvoiceDetail = SalesInvoiceDetail::select('id_sj')->where('id_invoice','=',$id)->get();
                $lastDlvResult = "";
                foreach ($salesInvoiceDetail As $detail) {

                    $cancelDlv = HelperDelivery::CancelDlv($detail->id_sj);

                    $lastDlvResult = $cancelDlv;

                    if ($cancelDlv != "success") {
                        break;
                    }
                }

                $cancelSO = HelperSalesOrder::CancelSO($inv->id_so);

                if($lastDlvResult == "success" && $cancelSO == "success") {
                    DB::commit();
                    return ['error' => 'success'];
                }
                elseif ($lastDlvResult != "success") {
                    DB::rollBack();

                    return ['error' => $lastDlvResult];
                }
                elseif ($cancelSO != "success") {
                    DB::rollBack();

                    return ['error' => $cancelSO];
                }
                else {
                    DB::rollBack();

                    return ['error' => 'Failed'];
                }
            }
            catch (\Exception $e) {
                DB::rollBack();

                return ['error' => $e->getMessage()];
            }
        }
    }

    public static function CheckSJ($id)
    {
        $listSJ = Delivery::select('kode_pengiriman', 'status_pengiriman', 'flag_invoiced')
                ->whereIn('delivery.id', function($subQuery) use ($id) {
                    $subQuery->select('id_sj')->from('sales_invoice_detail')
                    ->where([
                        ['id_invoice', '=', $id],
                        ['deleted_at', '=', null]
                    ]);
                })
                ->get();

        if (count($listSJ) > 0) {
            foreach ($listSJ as $sj) {
                if ($sj->status_pengiriman == 'draft') {
                    return 'failedDraft|'.$sj->kode_pengiriman;
                }
                else if ($sj->flag_invoiced == 1) {
                    return 'failedInvoiced|'.$sj->kode_pengiriman;
                }
            }
            return 'ok';
        }
        else {
            return 'NoSJ';
        }
    }

    public static function UpdateSJ($id, $flag)
    {
        try {
            DB::beginTransaction();
            $listSJ = SalesInvoiceDetail::select('id_sj')
                        ->where([
                            ['id_invoice', '=', $id],
                            ['deleted_at', '=', null]
                        ])
                        ->get();

            if (count($listSJ) > 0) {
                foreach ($listSJ as $sj) {
                    $dlv = Delivery::find($sj->id_sj);
                    $dlv->flag_invoiced = $flag;
                    $dlv->updated_by = Auth::user()->user_name;
                    $dlv->save();
                }
                DB::commit();
                return 'ok';

            }
            else {
                DB::rollBack();
                return 'NoSJ';
            }
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function cetakPdfInv($data)
    {
        $dataSalesInvoice = $data['dataSalesInvoice'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $dataAlamatTagih = $data['dataAlamatPenagihan'];
        $detailSalesInvoice = $data['detailSalesInvoice'];
        $dataShipDate = $data['shipDate'];
        $taxSettings = $data['taxSettings'];

        $fpdf = new Fpdf('P','mm','A4');

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

        $txtDiskon = "";
        $dpp= $dataSalesInvoice->dpp;
        if ($dataSalesInvoice->jenis_diskon == "P") {
            $nominalDiskon = $dataSalesInvoice->dpp * ($dataSalesInvoice->persentase_diskon / 100);
            if ($dataSalesInvoice->persentase_diskon > 0)
            {
                $dpp = $dataSalesInvoice->dpp - $nominalDiskon;
                $txtDiskon = number_format($nominalDiskon,2,",",".");
            }
            else {
                $txtDiskon = "-";
            }
        }
        elseif ($dataSalesInvoice->jenis_diskon == "N") {
            $nominalDiskon = $dataSalesInvoice->nominal_diskon;
            if ($dataSalesInvoice->nominal_diskon > 0)
            {
                $txtDiskon = number_format($nominalDiskon,2,",",".");
                $dpp = $dataSalesInvoice->dpp - $nominalDiskon;
            }
            else {
                $txtDiskon = "-";
            }
        }

        $txtDp = "";
        if ($dataSalesInvoice->dpp > 0)
        {
            $txtDp = number_format($dataSalesInvoice->dpp, 2, ',', '.');
        }
        else {
            $txtDp = "-";
        }

        $txtPPn = "";
        if ($dataSalesInvoice->ppn > 0)
        {
            $txtPPn = number_format(($dataSalesInvoice->ppn),2,",",".");
        }
        else {
            $txtPPn = "-";
        }
        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);
        
        $ppnPercentageInc = 1+($taxSettings->ppn_percentage/100);
        $ppnPercentageExc = $taxSettings->ppn_percentage/100;


        $txtTerbilang = Helper::number_to_words($dataSalesInvoice->grand_total);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah.#");

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        if ($dataAlamat->nama_outlet == "" || $dataAlamat->nama_outlet == "-"){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }
        $sisaPembayaran = number_format($dataSalesInvoice->grand_total - $dataSalesInvoice->nominal_dp, 0, ',', '.');

        //header INVOICE PENJUALAN
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataSalesInvoice->kode_invoice));
        //end of header INVOICE PENJUALAN

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
        $fpdf->MultiCell(65,4,$dataSalesInvoice->nama_customer.$outlet,0, 'L' );
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->MultiCell(65,4,$alamat,0,'L' );
        $fpdf->SetY(63);
        $fpdf->Cell(65,5,"U.P. ".$dataAlamat->pic_alamat." | Tlp. ".$dataAlamat->telp_pic,0,1, 'L' );
        $fpdf->SetY(36);
        $fpdf->Cell(65,34,"",1,1, 'L' );

        $fpdf->SetXY(75,36);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->MultiCell(65,4,$dataSalesInvoice->nama_customer.$outletTagih,0, 'L' );
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(75);
        $fpdf->MultiCell(65,4,$alamatTagih,0,'L' );
        $fpdf->SetXY(75,63);
        $fpdf->Cell(65,5,"U.P. ".$picTagih." | Tlp. ".$telpPicTagih,0,1, 'L' );
        $fpdf->SetXY(75,36);
        $fpdf->Cell(65,34,"",'RB',1, 'L' );
        //End of Blok Detail Alamat

        //Blok Detail Faktur Penjualan
        $fpdf->SetFont('Arial','B',16);
        $fpdf->SetXY(140,11);
        $fpdf->Cell(65,7,"FAKTUR PENJUALAN",0,1, 'C' );
        $fpdf->ln(-8);
        $fpdf->SetX(140);
        $fpdf->Cell(65,8,"","TRB",1, 'C' );
        $fpdf->ln(1);
        $fpdf->SetX(140);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(22,5.5," Nomor ",0,0,"L" );
        $fpdf->Cell(43,5.5,": ".strtoupper($dataSalesInvoice->kode_invoice),0,1,"L" );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(140);
        $fpdf->Cell(22,5.5," Tanggal Kirim ",0,0,"L" );
        $fpdf->Cell(43,5.5,": ".Carbon::parse($dataSalesInvoice->tanggal_invoice)->isoFormat('D MMMM Y'),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(22,5.5," Sales Order",0,0,"L" );
        $fpdf->Cell(43,5.5,": ".strtoupper($dataSalesInvoice->no_so),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(22,5.5," Surat Jalan",0,0,"L" );
        $fpdf->Cell(43,5.5,": ".strtoupper($dataShipDate->kode_pengiriman),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(22,5.5," No. PO ",0,0,"L" );
        $fpdf->Cell(2,5.5,":",0,0,"L" );
        $fpdf->MultiCell(41,5.5,strtoupper($dataSalesInvoice->no_po_customer),0,'L' );
        $fpdf->SetX(140);
        $fpdf->Cell(22,5.5," Pembayaran",0,0,"L" );
        $fpdf->SetFont('Arial','B',9);
        if ($dataSalesInvoice->metode_pembayaran == "cash") {
            $fpdf->Cell(43,5.5,": Tunai",0,1,"L" );
        }
        else {
            $fpdf->Cell(43,5.5,": Kredit ".$dataSalesInvoice->durasi_jt." Hari",0,1,"L" );
        $fpdf->SetX(140);
            $fpdf->Cell(22,5.5,"",0,0,"L" );
            $fpdf->Cell(43,5.5,"  (".date("d M Y", strtotime($dataSalesInvoice->tanggal_jt)).")",0,1,"L" );
        }
        $fpdf->SetXY(140,18);
        $fpdf->Cell(65,52,"","RB",1,"L" );
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
        $countItem = count($detailSalesInvoice);
        $hargaSatuan = 0;
        $dppProduk = 0;
        $ppnProduk = 0;
        $dppLain = 0;
        $ppnLain = 0;
        $subDpp = 0;
        $subDppLain = 0;
        $subPpn = 0;
        $gt = 0;
        if($countItem > 0){
            foreach ($detailSalesInvoice as $dataItem) {
                
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->Cell(8,5,$nmr,0,0, 'C' );
                    $fpdf->Cell(87,5,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(19,5,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' );
                    $fpdf->Cell(18,5,$dataItem->nama_satuan,0,0, 'C' );
                    if ($dataSalesInvoice->flag_ppn == "I") {
                        $hargaSatuan = round($dataItem->harga_jual / $ppnPercentageInc, 2);
                        $hargaTotal = $dataItem->qty_item * $hargaSatuan;
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format($hargaSatuan,2,",","."),0,0, 'R' );
                        // $fpdf->Cell(23,5,number_format(($dataItem->harga_jual / $ppnPercentage),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format($hargaTotal,2,",","."),0,1, 'R' );
                        // $fpdf->Cell(30,5,number_format(($dataItem->subtotal / $ppnPercentage),2,",","."),0,1, 'R' );
                    }
                    else {
                        $hargaSatuan = round($dataItem->harga_jual, 2);
                        $hargaTotal = $dataItem->qty_item * $hargaSatuan;
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format($hargaSatuan,2,",","."),0,0, 'R' );
                        // $fpdf->Cell(23,5,number_format(($dataItem->harga_jual),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        // $fpdf->Cell(30,5,number_format(($dataItem->subtotal),2,",","."),0,1, 'R' );
                        $fpdf->Cell(30,5,number_format($hargaTotal,2,",","."),0,1, 'R' );
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
                $dppProduk = $hargaTotal;
                $ppnProduk = $dppProduk * $ppnPercentageExc;
                        
                $dppLain = floor($dppProduk  / 12 * 11);
                
                $ppnLain = round($dppLain * 12 / 100);
                
                $subDpp += $dppProduk;
                $subDppLain += $dppLain;
                $subPpn += $ppnLain;
                
                
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
        $gt = ($subDpp + $subPpn);
        $txtTerbilang = Helper::number_to_words($gt);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah.#");
        //blok grand total
        $dppnilailain = $dpp * 11 / 12;
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"Jumlah Total",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5,number_format(round($subDpp),2,",","."),0,1,"R" );
        $fpdf->Cell(32,5,number_format(($dataSalesInvoice->dpp),2,",","."),0,1,"R" );
        $fpdf->SetX(128);
        $fpdf->Cell(35,5.5,"DPP Nilai Lain",0,0,"L" );
        $fpdf->Cell(10,5.5,"Rp",0,0,"C" );
        // $fpdf->Cell(32,5.5,number_format($subDppLain,2,",","."),0,1,"R" );
        $fpdf->Cell(32,5.5,number_format($dppnilailain,2,",","."),0,1,"R" );
        $fpdf->SetX(128);
        if ($dataSalesInvoice->jenis_diskon == "P") {
            $fpdf->Cell(35,5,"Potongan Harga ".$dataSalesInvoice->persentase_diskon."%",0,0,"L" );
        }
        else {
            $fpdf->Cell(35,5,"Potongan Harga ",0,0,"L" );
        }
        //$fpdf->Cell(35,5,"Potongan Harga ".$dataSalesInvoice->persentase_diskon."%",0,0,"L" );
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtDiskon,0,1,"R" );
        $fpdf->SetX(128);
        if ($dataSalesInvoice->ppn > 0){
            $fpdf->Cell(35,5,"PPn ",0,0,"L" );
            // $fpdf->Cell(35,5,"PPn ".$taxSettings->ppn_percentage."%",0,0,"L" );
        }
        else{
            $fpdf->Cell(35,5,"PPn ",0,0,"L" );
        }
        $fpdf->Cell(10,5,"Rp",0,0,"C" );
        $fpdf->Cell(32,5,$txtPPn,0,1,"R" );
        // $fpdf->Cell(32,5,number_format($subPpn,2,",","."),0,1,"R" );
        $fpdf->SetFont('Arial','B',9);
        $fpdf->SetX(128);
        $fpdf->Cell(35,5,"Total Tagihan","T",0,"L" );
        $fpdf->Cell(10,5,"Rp","T",0,"C" );
        // $fpdf->Cell(32,5,number_format(round($gt),2,",","."),"T",1,"R" );
        $fpdf->Cell(32,5,number_format(($dataSalesInvoice->grand_total),2,",","."),"T",1,"R" );
        //end of blok grand total

        //Blok TTD
        $fpdf->ln(5);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(165);
        $fpdf->Cell(35,5,"Tangerang, ".Carbon::parse($dataSalesInvoice->tanggal_invoice)->isoFormat('D MMMM Y'),0,1,"R" );
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

        if($dataSalesInvoice->status_invoice == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of Information

        return $fpdf;
    }
}
