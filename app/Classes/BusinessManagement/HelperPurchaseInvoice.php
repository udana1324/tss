<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\Receiving;

class HelperPurchaseInvoice
{
    public static function CancelInvoice($id)
    {
        $user = Auth::user()->user_name;
        $inv = PurchaseInvoice::find($id);
        if ($inv != null) {
            try {
                DB::beginTransaction();

                $inv->status_invoice = "batal";
                $inv->updated_by = $user;
                $inv->save();

                $invoiceDetail = PurchaseInvoiceDetail::select('id_sj')->where('id_invoice','=',$id)->get();
                $lastRcvResult = "";
                foreach ($invoiceDetail As $detail) {

                    $cancelRcv = HelperReceiving::CancelRcv($detail->id_sj);

                    $lastRcvResult = $cancelRcv;

                    if ($cancelRcv != "success") {
                        break;
                    }
                }

                $cancelPO = HelperPurchaseOrder::CancelPO($inv->id_po);

                if($lastRcvResult == "success" && $cancelPO == "success") {
                    DB::commit();
                    return ['error' => 'success'];
                }
                elseif ($lastRcvResult != "success") {
                    DB::rollBack();

                    return ['error' => $lastRcvResult];
                }
                elseif ($cancelPO != "success") {
                    DB::rollBack();

                    return ['error' => $cancelPO];
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
        $listSJ = Receiving::select('kode_penerimaan', 'status_penerimaan', 'flag_invoiced')
                ->whereIn('receiving.id', function($subQuery) use ($id) {
                    $subQuery->select('id_sj')->from('purchase_invoice_detail')
                    ->where([
                        ['id_invoice', '=', $id],
                        ['deleted_at', '=', null]
                    ]);
                })
                ->get();

        if (count($listSJ) > 0) {
            foreach ($listSJ as $sj) {
                if ($sj->status_penerimaan == 'draft') {
                    return 'failedDraft|'.$sj->kode_penerimaan;
                }
                else if ($sj->flag_invoiced == 1) {
                    return 'failedInvoiced|'.$sj->kode_penerimaan;
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
            $listSJ = PurchaseInvoiceDetail::select('id_sj')
                        ->where([
                            ['id_invoice', '=', $id],
                            ['deleted_at', '=', null]
                        ])
                        ->get();

            if (count($listSJ) > 0) {
                foreach ($listSJ as $sj) {
                    $rcv = Receiving::find($sj->id_sj);
                    $rcv->flag_invoiced = $flag;
                    $rcv->updated_by = Auth::user()->user_name;
                    $rcv->save();
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
        $dataPurchaseInvoice = $data['dataPurchaseInvoice'];
        $dataTerms = $data['dataTerms'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailPurchaseInvoice = $data['detailPurchaseInvoice'];
        $taxSettings = $data['taxSettings'];
        $dataShipDate = $data['shipDate'];
        $detailSJ = $data['detailSJ'];
        $dataSupplier = $data['dataSupplier'];

        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        $txtTerm = "";
        if ($dataPurchaseInvoice->metode_pembayaran == "cash") {
            $txtTerm = "TUNAI";
        }
        else {
            $txtTerm = "Kredit ".$dataPurchaseInvoice->durasi_jt." Hari (".date("d M Y", strtotime($dataPurchaseInvoice->tanggal_jt)).")";
        }

        $txtDiskon = "";

        $ppnPercentage = 1+($taxSettings->ppn_percentage/100);

        if ($dataPurchaseInvoice->persentase_diskon > 0)
        {
            $txtDiskon = number_format($dataPurchaseInvoice->diskon);
        }
        else {
            $txtDiskon = "-";
        }

        // $txtDp = "";
        // if ($dataPurchaseInvoice->nominal_dp > 0)
        // {
        //     $txtDp = number_format($dataPurchaseInvoice->nominal_dp, 0, ',', '.');
        // }
        // else {
        //     $txtDp = "-";
        // }

        $txtPPn = "";
        if ($dataPurchaseInvoice->ppn > 0)
        {
            $txtPPn = number_format($dataPurchaseInvoice->ppn);
        }
        else {
            $txtPPn = "-";
        }

        $txtTerbilang = Helper::number_to_words($dataPurchaseInvoice->grand_total);
        $txtTerbilang = ucwords("#".$txtTerbilang." Rupiah");

        $txtAlamat = $dataSupplier->alamat_supplier.', '.$dataSupplier->kelurahan.', '.$dataSupplier->kecamatan.', '.$dataSupplier->kota.' - '.$dataSupplier->kode_pos;

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }
        $sisaPembayaran = number_format($dataPurchaseInvoice->grand_total - $dataPurchaseInvoice->nominal_dp, 0, ',', '.');

        //Title Faktur Pembelian
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataPurchaseInvoice->kode_invoice));
        //End of Title Faktur Pembelian

        //Header Faktur Pembelian
        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetFont('helvetica','BU',15);
        $fpdf->SetX(12);
        $fpdf->Cell(98,7,strtoupper($dataPreference->nama_pt),0,1,'L');
        $fpdf->SetFont('arial','',9);
        $fpdf->SetX(12);
        $fpdf->Cell(88,5,strtoupper($dataPreference->alamat_pt),0,1,'L');
        $fpdf->SetX(12);
        $fpdf->Cell(88,5,strtoupper($dataPreference->kelurahan_pt).", ".strtoupper($dataPreference->kecamatan_pt).", ".strtoupper($dataPreference->kota_pt),0,1,'L');
        $fpdf->SetX(12);
        $fpdf->Cell(88,5,strtoupper("TELP : ".$dataPreference->telp_pt),0,1,'L');
        $fpdf->SetX(12);
        $fpdf->Cell(88,5,"Website : ".$dataPreference->website_pt,0,1,'L');
        $fpdf->SetXY(125,8);
        $fpdf->SetFillColor(109,110,113);
        $fpdf->SetTextColor(109,110,113);
        $fpdf->SetDrawColor(109,110,113);
        $fpdf->SetLineWidth(1);
        $fpdf->Multicell(80,41,'',1,1);
        $fpdf->SetXY(125,10);
        $fpdf->SetFont('arial','B',18);
        $fpdf->Cell(80,8,'FAKTUR PEMBELIAN','B',1,'C');
        $fpdf->ln(1);
        $fpdf->SetFont('arial','B',9);
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' Faktur Pembelian',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataPurchaseInvoice->kode_invoice),0,1,'L'); //udanahelp Done
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' No. Penerimaan',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataPurchaseInvoice->kode_penerimaan),0,1,'L'); //udanahelp Done
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' Nomor Ref. PO',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataPurchaseInvoice->no_po),0,1,'L');
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' Ref. Surat Jalan',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataPurchaseInvoice->no_sj_supplier),0,1,'L');
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' Tgl. Terima Barang',0,0,'L');
        $fpdf->Cell(42,5,": ".date("d M Y", strtotime($dataPurchaseInvoice->tanggal_sj)),0,1,'L');
        $fpdf->SetX(125);
        $fpdf->Cell(32,5,' Tgl. Jatuh Tempo',0,0,'L');
        $fpdf->Cell(42,5,": ".date("d M Y", strtotime($dataPurchaseInvoice->tanggal_jt)),0,1,'L');
        //End of Header Faktur Pembelian

        //Alamat supplier / vendor
        $fpdf->ln(-11);
        $fpdf->SetDrawColor(109,110,113);
        $fpdf->Cell(115,2,'','T',1,'R');
        $fpdf->SetFont('arial','',10);
        $fpdf->ln(0);
        $fpdf->SetTextColor(109,110,113);
        $fpdf->Cell(20,4,'Telah diterima dari : ',0,1,'L');

        $fpdf->SetFont('arial','BU',10);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->Cell(180,6,strtoupper($dataPurchaseInvoice->nama_supplier),0,1,'L');
        $fpdf->SetFont('arial','',10);
        $fpdf->Multicell(180,5,$txtAlamat,'R','L');
        $fpdf->Cell(180,6,'Telp. '.$dataPurchaseInvoice->telp_supplier.' || No. Rek : '.$dataPreference->nomor_rekening.' A/N. '.strtoupper($dataPreference->atas_nama),'RB',1,'L'); //udanahelp Done
        $fpdf->SetLineWidth(0.2);
        $fpdf->SetDrawColor(0,0,0);
        //End of alamat supplier / vendor

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
        $countItem = count($detailPurchaseInvoice);
        if($countItem > 0){
            foreach ($detailPurchaseInvoice as $dataItem) {
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->ln(0.5);
                    $fpdf->Cell(8,5,$nmr,0,0, 'C' );
                    $fpdf->Cell(87,5,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(19,5,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' ); //udanahelp Done
                    $fpdf->Cell(18,5,$dataItem->nama_satuan,0,0, 'C' );
                    if ($dataPurchaseInvoice->flag_ppn == "I") {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem->harga_beli / $ppnPercentage),2,",","."),0,0, 'R' );
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(30,5,number_format(($dataItem->subtotal / $ppnPercentage),2,",","."),0,1, 'R' );
                    }
                    else {
                        $fpdf->Cell(5,5,"Rp",0,0, 'L' );
                        $fpdf->Cell(23,5,number_format(($dataItem->harga_beli),2,",","."),0,0, 'R' );
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
            $fpdf->SetXY(10,70);
            $fpdf->Cell(8,105,'','LRB',0, 'C' );
            $fpdf->Cell(87,105,'','RB',0, 'L' );
            $fpdf->Cell(19,105,'','RB',0, 'R' );
            $fpdf->Cell(18,105,'','RB',0, 'L' );
            $fpdf->Cell(28,105,'','RB',0, 'L' );
            $fpdf->Cell(35,105,'','RB',1, 'L' );
        }
        //End of Blok Produk


        //blok Keterangan
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(80,5,'CATATAN FAKTUR PEMBELIAN','B',1,'L');
        $fpdf->Cell(80,30,'',0,1,'L');
        $fpdf->ln(-30);

        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(80,5, " - ".$terms->terms_and_cond,0,'L');
        }
        if ($countList == 0) {

        }
        //end of blok keterangan

        //Marking
        $fpdf->SetXY(10,230);
        $fpdf->Cell(23,5,'Dibuat Oleh :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,$dataPurchaseInvoice->created_by,0,1,'L');

        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(25,5,'Dicetak Oleh :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,Auth::user()->user_name,0,1,'L');

        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(27,5,'Tanggal Cetak :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        //$fpdf->Cell(80,5,Carbon::now()->isoFormat('D MMMM Y'),0,1,'L'); udanahelp
        //Marking


        //Signature
        $fpdf->SetXY(10,271);
        $fpdf->SetFont('Arial','',8);
        $fpdf->Cell(170,5,"*)) Faktur pembelian ini merupakan dokumen yang dibuat dan disetujui oleh sistem. Tanda tangan tidak diperlukan.",'B',1, 'L' );
        if($dataPurchaseInvoice->status_penerimaan == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of signature

        return $fpdf;
    }
}
