<?php

namespace App\Exports;

use App\Models\Product\Product;
use App\Models\Product\ProductDetailSpecification;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Models\Sales\SalesInvoice;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StockCardExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(Request $request)
    {
        $this->jenisPeriode = $request->input('jenisPeriode');
        $this->tglStart = $request->input('tanggal_picker_start');
        $this->tglEnd = $request->input('tanggal_picker_end');
        $this->bulan = $request->input('bulan_picker_val');
        $this->tahun = $request->input('tahun_picker_val');
    }

    public function view(): View
    {
        $transaction = "";
        $txt = "";
        $jenisPeriode = $this->jenisPeriode;
        $tglStart = $this->tglStart;
        $tglEnd = $this->tglEnd;
        $bulan = $this->bulan;
        $tahun = $this->tahun;

        // $stokIn = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
        //                             ->where([
        //                                         ['transaksi', '=', 'in']
        //                                     ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan')
        //                             ->groupBy('id_index');

        // $stokOut = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
        //                             ->where([
        //                                 ['transaksi', '=', 'out']
        //                             ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan')
        //                             ->groupBy('id_index');

        $dataStocks = StockTransaction::select(
                                            'stock_transaction.id_item',
                                            'stock_transaction.id_index',
                                            'stock_transaction.id_satuan',
                                            DB::raw("SUM(
                                                CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                                                        Else -stock_transaction.qty_item
                                                End
                                            ) AS qty")
                                        )
                                        ->groupBy('stock_transaction.id_item')
                                        ->groupBy('stock_transaction.id_satuan')
                                        ->groupBy('stock_transaction.id_index');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $list = [];
        $i = 0;
        foreach ($dataIndex as $index) {
            $txt = "";
            foreach ($index->ancestors as $ancestors) {
                $txt = $txt.$ancestors->nama_index.".";
            }

            $txt = $txt.$index->nama_index;
            $dataTxt = [
                'id' => $index->id,
                'nama_index' => $txt
            ];

            array_push($list, $dataTxt);
        }

        $detailStok = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                            ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                            ->leftJoin('product_detail', 'product.id', '=', 'product_detail.id_product')
                            ->leftJoin('product_unit', 'product_unit.id', '=', 'product_detail.id_satuan')
                            ->leftJoinSub($dataStocks, 'dataStocks', function($join_in) {
                                $join_in->on('product_detail.id_product', '=', 'dataStocks.id_item');
                                $join_in->on('product_detail.id_satuan', '=', 'dataStocks.id_satuan');
                            })
                            ->select('product.id',
                                'product.kode_item',
                                'product.nama_item',
                                'product.jenis_item',
                                'product_brand.nama_merk',
                                'product_category.nama_kategori',
                                'product_detail.stok_minimum',
                                'product_detail.stok_maksimum',
                                'product_unit.nama_satuan',
                                'dataStocks.id_index',
                                DB::raw('COALESCE(dataStocks.qty,0) AS stok_item'),
                                DB::raw("(CASE WHEN COALESCE(dataStocks.qty,0) < 0 THEN 'Stok Minus' WHEN COALESCE(dataStocks.qty,0) = 0 THEN 'Kosong' WHEN COALESCE(dataStocks.qty,0) <= product_detail.stok_minimum THEN 'Stok Menipis' WHEN COALESCE(dataStocks.qty,0) >= product_detail.stok_maksimum THEN 'Stok Melebihi Batas' END) AS status_stok")
                            )
                            ->where([
                                ['product_detail.deleted_at', '=', null]
                            ])
                            ->orderBy('dataStocks.id_index')
                            ->get();

        $dataStok = [];
        foreach($detailStok as $dataStock) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataStock->id_index) {
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $dataStock->id,
                'nama_merk' => $dataStock->nama_merk,
                'nama_kategori' => $dataStock->nama_kategori,
                'nama_item' => $dataStock->nama_item,
                'kode_item' => $dataStock->kode_item,
                'value_spesifikasi' => $dataStock->value_spesifikasi,
                'stok_item' => $dataStock->stok_item,
                'nama_satuan' => $dataStock->nama_satuan,
                'stok_minimum' => $dataStock->stok_minimum,
                'stok_maksimum' => $dataStock->stok_maksimum,
                'status_stok' => $dataStock->status_stok,
                'txt_index' => $txtIndex,
            ];
            array_push($dataStok, $dataAlloc);
        }

        usort($dataStok, function($a, $b){
            return $a['txt_index'] <=> $b['txt_index'];
        });

        $data['dataStok'] = $dataStok;

        return View('pages.stock.stock.stockCardExport', $data);
    }
}
