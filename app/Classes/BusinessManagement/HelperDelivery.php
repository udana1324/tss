<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Classes\BusinessManagement\Helper;
use App\Models\Product\Product;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Stock\StockTransaction;
use Carbon\Carbon;

class HelperDelivery
{
    public static function CancelDlv($id)
    {
        $user = Auth::user()->user_name;
        $dlv = Delivery::find($id);
        if ($dlv != null) {
            try {
                DB::beginTransaction();

                $dlv->status_pengiriman = "batal";
                $dlv->updated_by = $user;
                $dlv->save();

                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $dlv->kode_pengiriman)->delete();

                $detailDlv = DeliveryDetail::leftJoin('sales_order_detail', 'sales_order_detail.id_item', '=', 'delivery_detail.id_item')
                                            ->select(
                                                'delivery_detail.id',
                                                'delivery_detail.id_item',
                                                'delivery_detail.qty_item'
                                            )
                                            ->where([
                                                ['delivery_detail.id_pengiriman', '=', $id],
                                                ['sales_order_detail.id_so', '=', $dlv->id_so]
                                            ])
                                            ->get();

                foreach ($detailDlv As $detail) {

                    $detailOuts = SalesOrderDetail::where([
                                                        ['id_so', '=', $dlv->id_so],
                                                        ['id_item', '=', $detail->id_item]
                                                    ])
                                                    ->first();

                    $detailOuts->qty_outstanding = $detailOuts->qty_outstanding + $detail->qty_item;
                    $detailOuts->save();

                }

                $totalOuts = SalesOrder::where([
                                                ['id', '=', $dlv->id_so],
                                            ])
                                            ->first();

                $totalOuts->outstanding_so = $totalOuts->outstanding_so + $dlv->jumlah_total_sj;
                if ($totalOuts->outstanding_so == 0) {
                    $totalOuts->status_so = 'full';
                }
                else {
                    $totalOuts->status_so = 'posted';
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
        $dataDelivery = $data['dataDelivery'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $dataAlamatTagih = $data['dataAlamatPenagihan'];
        $detailDelivery = $data['detailDelivery'];
        $dataEkspedisi = $data['dataEkspedisi'];

        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

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

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "";
        }
        else {
            $outlet = " - ".$dataAlamat->nama_outlet;
        }

        //header surat jalan (delivery order)
        $fpdf->AddPage();
        $fpdf->SetTitle(strtoupper($dataDelivery->kode_pengiriman));
        //end of header surat jalan (delivery order)

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
        $fpdf->MultiCell(65,4,$dataDelivery->nama_customer.$outlet,0,'L' );
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->MultiCell(65,4,$alamat,0,'L' );
        if ($dataDelivery->metode_kirim == "ekspedisi") {
            $fpdf->Cell(65,5,"Ekspedisi : ".$dataEkspedisi->nama_cabang,0,1, 'L' );
        }
        $fpdf->SetY(62);
        $fpdf->Cell(65,5,"U.P. ".$dataAlamat->pic_alamat." | Tlp. ".$dataAlamat->telp_pic,0,1, 'L' );
        $fpdf->SetY(36);
        $fpdf->Cell(65,32,"",1,1, 'L' );

        $fpdf->SetXY(75,36);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->MultiCell(65,4,$dataDelivery->nama_customer.$outletTagih,0, 'L' );
        $fpdf->ln(1);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(75);
        $fpdf->MultiCell(65,4,$alamatTagih,0,'L' );
        $fpdf->SetXY(75,62);
        $fpdf->Cell(65,5,"U.P. ".$picTagih." | Tlp. ".$telpPicTagih,0,1, 'L' );
        $fpdf->SetXY(75,36);
        $fpdf->Cell(65,32,"",'RB',1, 'L' );
        //End of Blok Detail Alamat

        //Blok Detail Surat Jalan
        $fpdf->SetFont('Arial','B',17);
        $fpdf->SetXY(140,11);
        $fpdf->Cell(65,7,"SURAT JALAN",0,1, 'C' );
        $fpdf->ln(-8);
        $fpdf->SetX(140);
        $fpdf->Cell(65,8,"","TRB",1, 'C' );
        $fpdf->ln(1);
        $fpdf->SetX(140);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(22,6," Nomor ",0,0,"L" );
        $fpdf->Cell(43,6,": ".strtoupper($dataDelivery->kode_pengiriman),0,1,"L" );
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(140);
        $fpdf->Cell(22,6," Tanggal Kirim ",0,0,"L" );
        $fpdf->Cell(43,6,": ".Carbon::parse($dataDelivery->tanggal_sj)->isoFormat('D MMMM Y'),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(22,6," Sales Order",0,0,"L" );
        $fpdf->Cell(43,6,": ".strtoupper($dataDelivery->no_so),0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(22,6," No. PO ",0,0,"L" );
        $fpdf->Cell(2,6,":",0,0,"L" );
        $fpdf->MultiCell(41,6,strtoupper($dataDelivery->no_po_customer),0,'L' );
        $fpdf->SetXY(140,18);
        $fpdf->Cell(65,36,"","RB",1,"L" );
        $fpdf->SetXY(140,54);
        $fpdf->Cell(22,14," Plat Mobil",0,0,"L" );
        $fpdf->Cell(43,14,": ",0,1,"L" );
        $fpdf->ln(-14);
        $fpdf->SetX(140);
        $fpdf->Cell(65,14,"","RB",1,"L" );
        //End of Blok Detail Surat Jalan

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(10,6,"No.",'LTRB',0, 'C' );
        $fpdf->Cell(90,6,"Deskripsi Barang",'LTB',0, 'C' );
        $fpdf->Cell(25,6,"Qty",'LTB',0, 'C' );
        $fpdf->Cell(25,6,"Satuan",'LTB',0, 'C' );
        $fpdf->Cell(45,6,"Keterangan",'LTRB',1, 'C' );
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($detailDelivery);
        if($countItem > 9){
            foreach ($detailDelivery as $dataItem) {
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->Cell(10,4.7,$nmr,0,0, 'C' );
                    $fpdf->Cell(90,4.7,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(25,4.7,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' );
                    $fpdf->Cell(25,4.7,$dataItem->nama_satuan,0,0, 'C' );
                    $fpdf->Cell(45,4.7,$dataItem->keterangan,0,1, 'L' );
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
            $fpdf->SetXY(10,71);
            $fpdf->Cell(10,150,'','LRB',0, 'C' );
            $fpdf->Cell(90,150,'','RB',0, 'L' );
            $fpdf->Cell(25,150,'','RB',0, 'R' );
            $fpdf->Cell(25,150,'','RB',0, 'L' );
            $fpdf->Cell(45,150,'','RB',1, 'L' );
        }
        else{
            foreach ($detailDelivery as $dataItem) {
                if ($dataItem->jenis_item != "cetak"){
                    $fpdf->Cell(10,4.7,$nmr,0,0, 'C' );
                    $fpdf->Cell(90,4.7,$dataItem->nama_item,0,0, 'L' );
                    $fpdf->Cell(25,4.7,number_format(($dataItem->qty_item),2,",","."),0,0, 'R' );
                    $fpdf->Cell(25,4.7,$dataItem->nama_satuan,0,0, 'C' );
                    $fpdf->Cell(45,4.7,$dataItem->keterangan,0,1, 'L' );
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
            $fpdf->SetXY(10,71);
            $fpdf->Cell(10,50,'','LRB',0, 'C' );
            $fpdf->Cell(90,50,'','RB',0, 'L' );
            $fpdf->Cell(25,50,'','RB',0, 'R' );
            $fpdf->Cell(25,50,'','RB',0, 'L' );
            $fpdf->Cell(45,50,'','RB',1, 'L' );
        }
        //End of Blok Produk

        //blok ttd
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','',9);
        $fpdf->SetX(100);
        $fpdf->Cell(55,5,"Diterima Oleh,",0,0,"L" );
        $fpdf->Cell(55,5,"Dikirim Oleh,",0,1,"L" );
        $fpdf->SetX(90);
        $fpdf->Cell(10,30,"",0,0, 'L' );
        $fpdf->Cell(40,30,"","B",0,"L" );
        $fpdf->Cell(15,30,"",0,0, 'L' );
        $fpdf->Cell(40,30,"","B",1,"L" );
        //end of blok ttd

        //blok Keterangan
        $fpdf->ln(-35);
        $fpdf->Cell(80,5,'CATATAN PENGIRIMAN',1,1,'L');
        $fpdf->Cell(80,30,'','LRB',1,'L');
        $fpdf->ln(-30);

        foreach ($dataTerms as $terms) {
            $fpdf->MultiCell(80,5, " - ".$terms->terms_and_cond,0,'L');
        }
        //end of blok keterangan


        $fpdf->ln(5);
        if($dataDelivery->status_pengiriman == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }

        return $fpdf;
    }

    public static function cetakPdfOrderDlv($data)
    {
        $dataDelivery = $data['dataDelivery'];
        $dataTerms = $data['dataTerms'];
        $dataSales = $data['dataSales'];
        $dataPreference = $data['dataPreference'];
        $dataAlamat = $data['dataAlamat'];
        $dataAlokasiDlv = $data['dataAlokasiDlv'];
        $dataPengiriman = $data['dataPengiriman'];

        $fpdf = new Fpdf;

        $countList = count($dataTerms);

        $alamat = $dataAlamat->alamat_customer.', '.$dataAlamat->kelurahan.', '.$dataAlamat->kecamatan.', '.$dataAlamat->kota.' - '.$dataAlamat->kode_pos;

        if ($dataAlamat->nama_outlet == ""){
            $outlet = "-";
        }
        else {
            $outlet = $dataAlamat->nama_outlet;
        }

        //header ringkasan kirim

        $fpdf->AddPage('P','A4');
        $fpdf->SetTitle(strtoupper("Ringkasan Kirim ".$dataDelivery->kode_pengiriman));
        $fpdf->SetFont('Arial','BI',10);
        $fpdf->Cell(50,6,"NAMA PELANGGAN",'B',0,'C');
        $fpdf->Cell(35,6,"OUTLET",'B',0,'C');
        $fpdf->Cell(35,6,"PENGIRIMAN",'B',0,'C');
        $fpdf->Cell(25,6,"SUPIR",'B',0,'C');
        $fpdf->Cell(50,6,"TGL KIRIM",'B',1,'C');

        $fpdf->SetFont('Arial','I',10);
        $fpdf->Cell(50,6,strtoupper($dataDelivery->nama_customer),0,0,'C');
        $fpdf->Cell(35,6,strtoupper($outlet),0,0,'C');
        $fpdf->SetTextColor(250,37,37);
        $fpdf->Cell(35,6,strtoupper($dataPengiriman),0,0,'C');
        $fpdf->SetTextColor(0,0,0);
        $fpdf->Cell(25,6,"",0,0,'C'); //supir harus buat master baru
        $fpdf->SetTextColor(250,37,37);
        $fpdf->Cell(50,6,Carbon::parse($dataDelivery->tanggal_sj)->isoFormat('dddd, D MMMM Y'),0,1,'C');
        $fpdf->SetTextColor(0,0,0);
        //end of header ringkasan kirim

        //Blok Produk
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','B',9);
        $fpdf->SetFillColor(210,210,210);
        $fpdf->Cell(8,6,"NO.",'LTB',0, 'C',true);
        $fpdf->Cell(85,6,"NAMA BARANG",'LTB',0, 'C',true);
        $fpdf->Cell(27,6,"POSISI",'LTB',0, 'C',true);
        $fpdf->Cell(20,6,"JUMLAH",'LTB',0, 'C',true);
        $fpdf->Cell(15,6,"SATUAN",'LTRB',0, 'C',true);
        $fpdf->Cell(15,6,"KOLI",'LTRB',0, 'C',true);
        $fpdf->Cell(25,6,"ISI PER DUS",'LTRB',1, 'C',true);
        $nmr = 1;
        $fpdf->SetFont('Arial','',9);
        $countItem = count($dataAlokasiDlv);
        $ttlKoli = 0;
        foreach ($dataAlokasiDlv as $dataItem) {
            // if ($dataItem['qty_dus'] != 0) {
            //     $koli = $dataItem['qty_item']/$dataItem['qty_dus'];
            //     $ttlKoli = $ttlKoli + $koli;
            // }
            // else {
            //     $koli = 0;
            // }

            $fpdf->Cell(8,5,$nmr,'LRB',0, 'C' );
            $fpdf->Cell(85,5,strtoupper($dataItem['value_spesifikasi'] != null ? '('.$dataItem['value_spesifikasi'].')' : "".$dataItem['kode_item'])." - ".$dataItem['nama_item'],'RB',0,'L' );
            $fpdf->Cell(27,5,$dataItem['txt_index'],'RB',0,'C');
            $fpdf->Cell(20,5,number_format(($dataItem['qty_item']),0,",","."),'RB',0,'R' );
            $fpdf->Cell(15,5,ucwords($dataItem['nama_satuan']),'RB',0,'R' );
            $fpdf->Cell(15,5,number_format(0,0,",","."),'RB',0,'R' );
            $fpdf->Cell(25,5,number_format(0,0,",","."),'RB',1,'R' );
            $nmr++;
        }
        $fpdf->SetFont('Arial','B',9);
        $fpdf->Cell(8,6,"",'LTB',0, 'C');
        $fpdf->Cell(147,6,"TOTAL KOLI",'LTB',0, 'C',true);
        $fpdf->Cell(40,6,$ttlKoli." KOLI",'LTRB',1, 'R',true);
        //End of Blok Produk

        //blok ttd 1
        $fpdf->ln(3);
        $fpdf->SetFont('Arial','I',10);
        $fpdf->SetX(20);
        $fpdf->Cell(50,5,"PETUGAS",0,1,"C" );
        $fpdf->SetX(20);
        $fpdf->Cell(50,5,"PENGAMBIL",0,1,"C" );
        $fpdf->SetX(20);
        $fpdf->Cell(50,5,"BARANG",0,1,"C" );
        $fpdf->SetX(20);
        $fpdf->Cell(50,10,"",0,1,"L" );
        $fpdf->SetX(20);
        $fpdf->Cell(50,5,"(                                               )",0,1,"L" );
        //end of blok ttd 1

        //blok ttd 2
        $fpdf->ln(-30);
        $fpdf->SetX(80);
        $fpdf->Cell(50,5,"PETUGAS",0,1,"C" );
        $fpdf->SetX(80);
        $fpdf->Cell(50,5,"PENGURANG STOK",0,1,"C" );
        $fpdf->SetX(80);
        $fpdf->Cell(50,5,"FISIK",0,1,"C" );
        $fpdf->SetX(80);
        $fpdf->Cell(50,10,"",0,1,"L" );
        $fpdf->SetX(80);
        $fpdf->Cell(50,5,"(                                               )",0,1,"L" );
        //end of blok ttd 2

        //blok ttd 3
        $fpdf->ln(-30);
        $fpdf->SetX(140);
        $fpdf->Cell(50,5,"PETUGAS",0,1,"C" );
        $fpdf->SetX(140);
        $fpdf->Cell(50,5,"INPUT",0,1,"C" );
        $fpdf->SetX(140);
        $fpdf->Cell(50,5,"SISTEM",0,1,"C" );
        $fpdf->SetX(140);
        $fpdf->Cell(50,10,"",0,1,"L" );
        $fpdf->SetX(140);
        $fpdf->Cell(50,5,"(                                               )",0,1,"L" );
        //end of blok ttd 3

        $fpdf->ln(5);
        if($dataDelivery->status_pengiriman == "draft"){
            $fpdf->Image('images/DRAFT.png',10,37);
        }

        return $fpdf;
    }

    public static function createStockTransaction($idSJ, $alokasi) {
        $tempQty = 0;
        $delivery = Delivery::find($idSJ);
        $transactionList = [];
        $dataStock = HelperDelivery::getStock($alokasi->id_item, $alokasi->id_satuan, $alokasi->id_index);
        //dd($dataStock);
        $tempQty = $alokasi->qty_item;
        foreach ($dataStock as $stock) {


            if ($stock->qty > 0) {
                if ($tempQty > 0 && $tempQty <= $stock->qty) {
                    $dataStok = [
                        'kode_transaksi' => $delivery->kode_pengiriman,
                        'id_item' => $alokasi->id_item,
                        'id_satuan' => $alokasi->id_satuan,
                        'qty_item' => $tempQty,
                        'id_index' => $alokasi->id_index,
                        'tgl_transaksi' => $delivery->tanggal_sj,
                        'jenis_transaksi' => "pengiriman",
                        'transaksi' => "out",
                        'jenis_sumber' => $stock->jenis_sumber,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transactionList, $dataStok);

                    $tempQty = $tempQty - $alokasi->qty_item;
                }
                elseif ($tempQty > 0 ) {
                    $dataStok = [
                        'kode_transaksi' => $delivery->kode_pengiriman,
                        'id_item' => $alokasi->id_item,
                        'id_satuan' => $alokasi->id_satuan,
                        'qty_item' => $stock->qty,
                        'id_index' => $alokasi->id_index,
                        'tgl_transaksi' => $delivery->tanggal_sj,
                        'jenis_transaksi' => "pengiriman",
                        'transaksi' => "out",
                        'jenis_sumber' => $stock->jenis_sumber,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transactionList, $dataStok);

                    $tempQty = $tempQty - $stock->qty;
                }
            }
        }
       // dd($transactionList);

        return $transactionList;
    }

    static function getStock($idItem, $idSatuan, $idIndex) {
        $dataStocks = StockTransaction::select(
            'stock_transaction.id_item',
            'stock_transaction.id_index',
            'stock_transaction.id_satuan',
            'stock_transaction.jenis_sumber',
            DB::raw("SUM(
                CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                        Else -stock_transaction.qty_item
                End
            ) AS qty")
        )
        ->where([
            ['stock_transaction.id_index', '=', $idIndex],
            ['stock_transaction.id_item', '=', $idItem],
            ['stock_transaction.id_satuan', '=', $idSatuan],
        ])
        ->groupBy('stock_transaction.id_item')
        ->groupBy('stock_transaction.id_satuan')
        ->groupBy('stock_transaction.jenis_sumber')
        ->orderBy('stock_transaction.jenis_sumber', 'asc')
        ->get();

        return $dataStocks;
    }
}
