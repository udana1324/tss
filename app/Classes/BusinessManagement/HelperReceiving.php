<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use Carbon\Carbon;

class HelperReceiving
{
    public static function CancelRcv($id)
    {
        $user = Auth::user()->user_name;
        $rcv = Receiving::find($id);
        if ($rcv != null) {
            try {
                DB::beginTransaction();

                $rcv->status_penerimaan = "batal";
                $rcv->updated_by = $user;
                $rcv->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $rcv->kode_penerimaan)->delete();

                $detailRcv = ReceivingDetail::leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', '=', 'receiving_detail.id_item')
                                            ->select(
                                                'receiving_detail.id',
                                                'receiving_detail.id_item',
                                                'receiving_detail.qty_item'
                                            )
                                            ->where([
                                                ['receiving_detail.id_penerimaan', '=', $id],
                                                ['purchase_order_detail.id_po', '=', $rcv->id_po]
                                            ])
                                            ->get();

                foreach ($detailRcv As $detail) {

                    $detailOuts = PurchaseOrderDetail::where([
                                                        ['id_po', '=', $rcv->id_po],
                                                        ['id_item', '=', $detail->id_item]
                                                    ])
                                                    ->first();

                    $detailOuts->outstanding_qty = $detailOuts->outstanding_qty + $detail->qty_item;
                    $detailOuts->save();

                }

                $totalOuts = PurchaseOrder::where([
                                                ['id', '=', $rcv->id_po],
                                            ])
                                            ->first();

                $totalOuts->outstanding_po = $totalOuts->outstanding_po + $rcv->jumlah_total_sj;
                if ($totalOuts->outstanding_po == 0) {
                    $totalOuts->status_po = 'full';
                }
                else {
                    $totalOuts->status_po = 'posted';
                }
                $totalOuts->save();

                DB::commit();

                return "success";

            } catch (\Exception $e) {
                DB::rollBack();

                return "failed";
            }
        }
        else {
            return "notFound";
        }
    }

    public static function cetakPdfDlv($data)
    {
        $dataReceiving = $data['dataRcv'];
        $dataTerms = $data['dataTerms'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $detailReceiving = $data['detailReceiving'];

        $fpdf = new Fpdf;

        $countList = count($dataTerms);
        $txt_alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan. ', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.', '.$dataAlamat->kode_pos;

        $alamat = $dataAlamat->alamat_supplier.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }

        //Title Receiving
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataReceiving->kode_penerimaan));
        //End of Title Receiving

        //Header Receiving
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
        $fpdf->Multicell(80,36,'',1,1);
        $fpdf->SetXY(125,10);
        $fpdf->SetFont('arial','B',18);
        $fpdf->Cell(80,8,'PENERIMAAN BARANG','B',1,'C');
        $fpdf->ln(2);
        $fpdf->SetFont('arial','B',9);
        $fpdf->SetX(130);
        $fpdf->Cell(28,5,' No. Penerimaan',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataReceiving->kode_penerimaan),0,1,'L');
        $fpdf->SetX(130);
        $fpdf->Cell(28,5,' Nomor Ref. PO',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataReceiving->no_po),0,1,'L');
        $fpdf->SetX(130);
        $fpdf->Cell(28,5,' No. Surat Jalan',0,0,'L');
        $fpdf->Cell(42,5,": ".strtoupper($dataReceiving->no_sj_supplier),0,1,'L');
        $fpdf->SetX(130);
        $fpdf->Cell(28,5,' Tanggal Terima',0,0,'L');
        $fpdf->Cell(42,5,": ".date("d M Y", strtotime($dataReceiving->tanggal_sj)),0,1,'L');
        //End of Header Receiving

        //Alamat supplier / vendor
        $fpdf->ln(-2);
        $fpdf->SetDrawColor(109,110,113);
        $fpdf->Cell(115,2,'','T',1,'R');
        $fpdf->SetFont('arial','',10);
        $fpdf->ln(-1);
        $fpdf->SetTextColor(109,110,113);
        $fpdf->Cell(20,4,'Telah diterima dari : ',0,1,'L');

        $fpdf->SetFont('arial','BU',10);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->ln(1);
        $fpdf->Cell(180,6,strtoupper($dataReceiving->nama_supplier),'R',1,'L');
        $fpdf->SetFont('arial','',10);
        $fpdf->Multicell(180,5,$txt_alamat,'R','L');
        $fpdf->Cell(180,6,'Telp. '.$dataReceiving->telp_supplier,'RB',1,'L');
        $fpdf->SetLineWidth(0.2);
        $fpdf->SetDrawColor(0,0,0);
        //End of alamat supplier / vendor

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(10,7,"No.",'LTB',0, 'C' );
        $fpdf->Cell(100,7,"Deskripsi Barang",'LTB',0, 'C' );
        $fpdf->Cell(25,7,"Qty",'LTB',0, 'C' );
        $fpdf->Cell(25,7,"Satuan",'LTB',0, 'C' );
        $fpdf->Cell(35,7,"Keterangan",1,1, 'C' );
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($detailReceiving);
        if($countItem > 0){
            foreach ($detailReceiving as $dataItem) {
                $fpdf->ln(0.5);
                $fpdf->Cell(10,5,$nmr,0,0, 'C' );
                $fpdf->Cell(100,5,$dataItem->nama_item,0,0, 'L' );
                $fpdf->Cell(25,5,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' ); //udanahelp *Done
                $fpdf->Cell(25,5,$dataItem->nama_satuan,0,0, 'C' );
                $fpdf->Cell(35,5,$dataItem->keterangan_item,0,0, 'C' ); //udanahelp *Done
                $nmr++;
            }
            $fpdf->SetXY(10,70);
            $fpdf->Cell(10,105,'','LRB',0, 'C' );
            $fpdf->Cell(100,105,'','RB',0, 'L' );
            $fpdf->Cell(25,105,'','RB',0, 'R' );
            $fpdf->Cell(25,105,'','RB',0, 'L' );
            $fpdf->Cell(35,105,'','RB',1, 'L' );
        }
        //End of Blok Produk

        //blok ttd
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','',10);
        $fpdf->SetX(150);
        $fpdf->Cell(45,5,"Dikirim Oleh,",0,0,"R" );
        $fpdf->SetX(150);
        $fpdf->Cell(45,35,"","B",1,"L" );
        //end of blok ttd

        //blok Keterangan
        $fpdf->ln(-35);
        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(80,5,'CATATAN PENERIMAAN BARANG','B',1,'L');
        $fpdf->Cell(80,30,'',0,1,'L');
        $fpdf->ln(-30);

        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(80,5, " - ".$terms->terms_and_cond,0,'L'); //udanahelp ? udah ok?
        }
        if ($countList == 0) {

        }
        //end of blok keterangan

        //Marking
        $fpdf->SetXY(10,230);
        $fpdf->Cell(23,5,'Dibuat Oleh :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,$dataReceiving->created_by,0,1,'L'); //udanahelp *Done

        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(25,5,'Dicetak Oleh :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,Auth::user()->user_name,0,1,'L'); //udanahelp *Done

        $fpdf->SetFont('Arial','B',10);
        $fpdf->Cell(27,5,'Tanggal Cetak :',0,0,'L');
        $fpdf->SetFont('Arial','',10);
        $fpdf->Cell(80,5,Carbon::now()->isoFormat('D MMMM Y'),0,1,'L'); //udanahelp *Done
        //Marking


        //Signature
        $fpdf->SetXY(10,271);
        $fpdf->SetFont('Arial','',8);
        $fpdf->Cell(170,5,"*)) Bukti penerimaan barang ini merupakan dokumen yang dibuat dan disetujui oleh sistem. Tanda tangan tidak diperlukan.",'B',1, 'L' );
        if($dataReceiving->status_penerimaan == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }
        //End of signature

        return $fpdf;
    }
}
