<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Models\Product\ProductDetailSpecification;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransfer;
use App\Models\Stock\StockTransferDetail;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class StocktransferController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/StockTransfer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/StockTransfer'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $dataProduct = Product::distinct()
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->orderBy('nama_item', 'asc')
                                        ->get();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);
                $dataStatus = StockTransfer::distinct()->get('status_transfer');

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['dataStatus'] = $dataStatus;
                $data['dataProduct'] = $dataProduct;

                $log = ActionLog::create([
                    'module' => 'Transfer Stok',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Transfer Stok',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock_transfer.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {

        $datatransfer = StockTransfer::orderBy('id','desc')
                                        ->get();


        return response()->json($datatransfer);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/StockTransfer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $parentMenu = Module::find($hakAkses->parent);

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $dataProduct = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();

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

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['listIndex'] = $list;

                $log = ActionLog::create([
                    'module' => 'Transfer',
                    'action' => 'Buat',
                    'desc' => 'Buat Transfer',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('stock_transfer_detail')
                            ->where([
                                ['id_transfer', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.stock.stock_transfer.add', $data);
            }
            else {
                return redirect('/Transfer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/StockTransfer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTransfer = StockTransfer::find($id);

                $parentMenu = Module::find($hakAkses->parent);

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
                $dataProduct = Product::leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get();

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

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'stock_transfer'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = StockTransferDetail::where([
                                                    ['id_transfer', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'stock_transfer',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_transfer,
                            'value2' => $detail->id_item,
                            'value3' => $detail->qty_item,
                            'value4' => $detail->id_index_f,
                            'value5' => $detail->id_index_t,
                            'value6' => $detail->id_satuan
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['listIndex'] = $list;
                $data['dataTransfer'] = $dataTransfer;

                $log = ActionLog::create([
                    'module' => 'Stock Transfer',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Stock Transfer',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock_transfer.edit', $data);
            }
            else {
                return redirect('/StockTransfer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/StockTransfer'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTransfer = StockTransfer::find($id);

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataTransfer'] = $dataTransfer;

                $log = ActionLog::create([
                    'module' => 'Stock Transfer',
                    'action' => 'Detail',
                    'desc' => 'Detail Stock Transfer',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.stock_transfer.detail', $data);
            }
            else {
                return redirect('/StockTransfer')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function GetTransferDetail(Request $request)
    {
        $id = $request->input('idTransfer');
        $mode = $request->input('mode');

        $data = [];
        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);
        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = StockTransferDetail::leftJoin('product', 'stock_transfer_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'stock_transfer_detail.id_satuan', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'stock_transfer_detail.id',
                                            'stock_transfer_detail.id_item',
                                            'stock_transfer_detail.id_satuan',
                                            'stock_transfer_detail.id_index_f',
                                            'stock_transfer_detail.id_index_t',
                                            'stock_transfer_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['stock_transfer_detail.id_transfer', '=', $id]
                                        ])
                                        ->get();

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


            foreach($detail as $dataTrf) {
                $txtIndex = "-";
                foreach ($list as $txt) {
                    if ($txt["id"] == $dataTrf->id_index_f) {
                        $txtIndexF = $txt["nama_index"];
                    }

                    if ($txt["id"] == $dataTrf->id_index_t) {
                        $txtIndexT = $txt["nama_index"];
                    }
                }
                $dataTr = [
                    'id' => $dataTrf->id,
                    'kode_item' => $dataTrf->kode_item,
                    'nama_item' => $dataTrf->nama_item,
                    'nama_satuan' => $dataTrf->nama_satuan,
                    'value_spesifikasi' => $dataTrf->value_spesifikasi,
                    'id_transfer' => $dataTrf->id_transfer,
                    'id_item' => $dataTrf->id_item,
                    'id_satuan' => $dataTrf->id_satuan,
                    'id_index_f' => $dataTrf->id_index_f,
                    'txt_index_f' => $txtIndexF,
                    'id_index_t' => $dataTrf->id_index_t,
                    'txt_index_t' => $txtIndexT,
                    'qty_item' => $dataTrf->qty_item,


                ];
                array_push($data, $dataTr);
            }
        }
        else {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value6', 'product_unit.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value1',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'stock_transfer']
                                        ])
                                        ->get();

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

            foreach($detail as $dataTrf) {
                foreach ($list as $txt) {
                    if ($txt["id"] == $dataTrf->value4) {
                        $txtIndexF = $txt["nama_index"];
                    }

                    if ($txt["id"] == $dataTrf->value5) {
                        $txtIndexT = $txt["nama_index"];
                    }
                }
                $dataTr = [
                    'id' => $dataTrf->id,
                    'kode_item' => $dataTrf->kode_item,
                    'nama_item' => $dataTrf->nama_item,
                    'nama_satuan' => $dataTrf->nama_satuan,
                    'value_spesifikasi' => $dataTrf->value_spesifikasi,
                    'id_transfer' => $dataTrf->value1,
                    'id_item' => $dataTrf->value2,
                    'id_satuan' => $dataTrf->value6,
                    'id_index_f' => $dataTrf->value4,
                    'txt_index_f' => $txtIndexF,
                    'id_index_t' => $dataTrf->value5,
                    'txt_index_t' => $txtIndexT,
                    'qty_item' => $dataTrf->value3,


                ];
                array_push($data, $dataTr);
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $tgl = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/StockTransfer')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;
            $tgl = $request->input('tanggal');
            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('stock_transfer')
                            ->select(DB::raw("MAX(RIGHT(kode_transaksi,2)) AS angka"))
                            // ->whereMonth('tgl_transaksi', $blnPeriode)
                            // ->whereYear('tgl_transaksi', $thnPeriode)
                            ->whereDate('tgl_transaksi', $tgl)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tgl))));

            if ($counter < 10) {
                $nmrtransfer = "trf-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrtransfer = "trf-cv-".$kodeTgl.$counter;
            }


            $trf = new StockTransfer();
            $trf->kode_transaksi = $nmrtransfer;
            $trf->tgl_transaksi = $tgl;
            $trf->keterangan = $keterangan;
            $trf->status_transfer = 'draft';
            $trf->flag_revisi = 0;
            $trf->created_by = $user;
            $trf->save();

            $data = $trf;

            $setDetail = DB::table('stock_transfer_detail')
                                ->where([
                                            ['id_transfer', '=', 'DRAFT'],
                                            ['created_by', '=', $user]
                                        ])
                                ->update([
                                    'id_transfer' => $trf->id,
                                    'updated_by' => $user
                                ]);

            $log = ActionLog::create([
                'module' => 'Transfer',
                'action' => 'Simpan',
                'desc' => 'Simpan Transfer',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('StockTransfer.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_transaksi).' Telah Disimpan!');
        }
        else {
            return redirect('/StockTransfer')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $tgl = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/StockTransfer')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;
            $tgl = $request->input('tanggal');

            $trf = StockTransfer::find($id);
            $trf->tgl_transaksi = $tgl;
            $trf->keterangan = $keterangan;
            $trf->updated_by = $user;
            $trf->save();

            $data = $trf;

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'stock_transfer'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = StockTransferDetail::find($detail->id_detail);
                        $listItem->id_transfer = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value6;
                        $listItem->qty_item = $detail->value3;
                        $listItem->id_index_f = $detail->value4;
                        $listItem->id_index_t = $detail->value5;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new StockTransferDetail();
                        $listItem->id_transfer = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value6;
                        $listItem->qty_item = $detail->value3;
                        $listItem->id_index_f = $detail->value4;
                        $listItem->id_index_t = $detail->value5;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('stock_transfer_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'stock_transfer'],
                                    ['value1', '=', $id]
                                ])->delete();

            $log = ActionLog::create([
                'module' => 'Transfer',
                'action' => 'Update',
                'desc' => 'Update Transfer',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('StockTransfer.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_transaksi).' Telah Disimpan!');
        }
        else {
            return redirect('/StockTransfer')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $transfer = StockTransfer::find($id);

            if ($btnAction == "posting") {
                $detailTrf = StockTransferDetail::where([
                                                    ['stock_transfer_detail.id_transfer', '=', $id]
                                                ])
                                                ->get();
                $transaksi = [];
                $failedItem = [];
                foreach ($detailTrf As $detailOut) {

                    $dataDetailOut = [
                        'kode_transaksi' => $transfer->kode_transaksi,
                        'id_item' => $detailOut->id_item,
                        'id_satuan' => $detailOut->id_satuan,
                        'id_index' => $detailOut->id_index_f,
                        'qty_item' => $detailOut->qty_item,
                        'tgl_transaksi' => $transfer->tgl_transaksi,
                        'jenis_transaksi' => "transfer",
                        'transaksi' => "out",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transaksi, $dataDetailOut);
                }

                foreach ($detailTrf As $detailIn) {

                    $dataDetailIn = [
                        'kode_transaksi' => $transfer->kode_transaksi,
                        'id_item' => $detailIn->id_item,
                        'id_satuan' => $detailIn->id_satuan,
                        'id_index' => $detailIn->id_index_t,
                        'qty_item' => $detailIn->qty_item,
                        'tgl_transaksi' => $transfer->tgl_transaksi,
                        'jenis_transaksi' => "transfer",
                        'transaksi' => "in",
                        'jenis_sumber' => 3,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transaksi, $dataDetailIn);
                }

                StockTransaction::insert($transaksi);

                $transfer->status_transfer = "posted";
                $transfer->save();

                $log = ActionLog::create([
                    'module' => 'Stock Transfer',
                    'action' => 'Posting',
                    'desc' => 'Posting Stock Transfer',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($transfer->kode_transaksi).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $transfer->kode_transaksi)->delete();

                $transfer->status_transfer = "draft";
                $transfer->flag_revisi = '1';
                $transfer->updated_by = Auth::user()->user_name;
                $transfer->save();


                $log = ActionLog::create([
                    'module' => 'Stock Transfer',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Stock Transfer',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Transfer Stok '.strtoupper($transfer->kode_transaksi).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $transfer->kode_transaksi)->delete();

                $transfer->status_transfer = "batal";
                $transfer->updated_by = Auth::user()->user_name;
                $transfer->save();

                $log = ActionLog::create([
                    'module' => 'Stock Transfer',
                    'action' => 'Batal',
                    'desc' => 'Batal Stock Transfer',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Transfer Stok '.strtoupper($transfer->kode_transaksi).' Telah Dibatalkan!';
                $status = "success";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('StockTransfer.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function StoreTransferDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idTransfer');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndexF = $request->input('idIndexF');
            $idIndexT = $request->input('idIndexT');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('stock_transfer_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_transfer', '=' , $id],
                                    ['id_item', '=', $idItem],
                                    ['id_satuan', '=', $idSatuan],
                                    ['id_index_f', '=', $idIndexF],
                                    ['id_index_t', '=', $idIndexT],
                                    ['deleted_at', '=', null]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new StockTransferDetail();
                    $listItem->id_transfer = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->id_index_f = $idIndexF;
                    $listItem->id_index_t = $idIndexT;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Transfer Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Transfer Detail',
                        'username' => Auth::user()->user_name
                    ]);
                    $data = "success";
                }
            }
            else {

                $countItem = TempTransaction::select(DB::raw("COUNT(*) AS angka"))
                                            ->where([
                                                ['module', '=', 'stock_transfer'],
                                                ['value1', '=' , $id],
                                                ['value2', '=', $idItem],
                                                ['value4', '=', $idIndexF],
                                                ['value5', '=', $idIndexT],
                                                ['value6', '=', $idSatuan],
                                                ['deleted_at', '=', null]
                                            ])
                                            ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'stock_transfer';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value4 = $idIndexF;
                    $listItem->value5 = $idIndexT;
                    $listItem->value3 = $qty;
                    $listItem->value6 = $idSatuan;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Transfer Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Transfer Detail',
                        'username' => Auth::user()->user_name
                    ]);
                    $data = "success";
                }
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function UpdateTransferDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idTransfer');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndexF = $request->input('idIndexF');
            $idIndexT = $request->input('idIndexT');
            $qty = $request->input('qtyItem');
            $idDetail = $request->input('idDetail');
            $user = Auth::user()->user_name;

            if ($id == "") {
                if ($id == "") {
                    $id = 'DRAFT';
                }

                $listItem = StockTransferDetail::find($idDetail);
                $listItem->id_transfer = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->id_index_f = $idIndexF;
                $listItem->id_index_t = $idIndexT;
                $listItem->qty_item = $qty;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {

                $listItem = TempTransaction::find($idDetail);
                $listItem->module = 'stock_transfer';
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value4 = $idIndexF;
                $listItem->value5 = $idIndexT;
                $listItem->value3 = $qty;
                $listItem->value6 = $idSatuan;
                $listItem->updated_by = $user;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'Transfer Detail',
                'action' => 'Update',
                'desc' => 'Update Transfer Detail',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function EditTransferDetail(Request $request)
    {
        $data = [];
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $stokInF = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
                                            ->where([
                                                        ['transaksi', '=', 'in']
                                                    ])
                                            ->groupBy('id_item')
                                            ->groupBy('id_index');

            $stokOutF = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
                                        ->where([
                                            ['transaksi', '=', 'out']
                                        ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_index');

            $stokInT = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
                                        ->where([
                                                    ['transaksi', '=', 'in']
                                                ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_index');

            $stokOutT = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
                                        ->where([
                                            ['transaksi', '=', 'out']
                                        ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_index');

            if ($mode != "edit") {

                $detail = StockTransferDetail::leftJoin('product', 'stock_transfer_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'stock_transfer_detail.id_satuan', 'product_unit.id')
                                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                                })
                                                ->leftJoinSub($stokInF, 'stokInF', function($join_inF) {
                                                    $join_inF->on('stock_transfer_detail.id_item', '=', 'stokInF.id_item');
                                                    $join_inF->on('stock_transfer_detail.id_index_f', '=', 'stokInF.id_index');
                                                })
                                                ->leftJoinSub($stokOutF, 'stokOutF', function($join_outF) {
                                                    $join_outF->on('stock_transfer_detail.id_item', '=', 'stokOutF.id_item');
                                                    $join_outF->on('stock_transfer_detail.id_index_f', '=', 'stokOutF.id_index');
                                                })
                                                ->leftJoinSub($stokInT, 'stokInT', function($join_inT) {
                                                    $join_inT->on('stock_transfer_detail.id_item', '=', 'stokInT.id_item');
                                                    $join_inT->on('stock_transfer_detail.id_index_t', '=', 'stokInT.id_index');
                                                })
                                                ->leftJoinSub($stokOutT, 'stokOutT', function($join_outT) {
                                                    $join_outT->on('stock_transfer_detail.id_item', '=', 'stokOutT.id_item');
                                                    $join_outT->on('stock_transfer_detail.id_index_t', '=', 'stokOutT.id_index');
                                                })
                                                ->select(
                                                    'stock_transfer_detail.id',
                                                    'stock_transfer_detail.id_item',
                                                    'stock_transfer_detail.id_satuan',
                                                    'stock_transfer_detail.id_index_f',
                                                    'stock_transfer_detail.id_index_t',
                                                    'stock_transfer_detail.qty_item',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan',
                                                    'dataSpek.value_spesifikasi',
                                                )
                                                ->where([
                                                    ['stock_transfer_detail.id', '=', $id]
                                                ])
                                                ->get();

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


                foreach($detail as $dataTrf) {
                    $txtIndexF = "-";
                    $txtIndexT = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataTrf->id_index_f) {
                            $txtIndexF = $txt["nama_index"];
                        }

                        if ($txt["id"] == $dataTrf->id_index_t) {
                            $txtIndexT = $txt["nama_index"];
                        }
                    }
                    $dataTr = [
                        'id' => $dataTrf->id,
                        'kode_item' => $dataTrf->kode_item,
                        'nama_item' => $dataTrf->nama_item,
                        'nama_satuan' => $dataTrf->nama_satuan,
                        'value_spesifikasi' => $dataTrf->value_spesifikasi,
                        'id_transfer' => $dataTrf->id_transfer,
                        'id_item' => $dataTrf->id_item,
                        'id_satuan' => $dataTrf->id_satuan,
                        'id_index_f' => $dataTrf->id_index_f,
                        'txt_index_f' => $txtIndexF,
                        'id_index_t' => $dataTrf->id_index_t,
                        'txt_index_t' => $txtIndexT,
                        'qty_item' => $dataTrf->qty_item,
                    ];
                    array_push($data, $dataTr);
                }
            }
            else {

                $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                                ->leftJoin('product_unit', 'temp_transaction.value6', 'product_unit.id')
                                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                                })
                                                ->leftJoinSub($stokInF, 'stokInF', function($join_in) {
                                                    $join_in->on('temp_transaction.value2', '=', 'stokInF.id_item');
                                                    $join_in->on('temp_transaction.value4', '=', 'stokInF.id_index');
                                                })
                                                ->leftJoinSub($stokOutF, 'stokOutF', function($join_out) {
                                                    $join_out->on('temp_transaction.value2', '=', 'stokOutF.id_item');
                                                    $join_out->on('temp_transaction.value4', '=', 'stokOutF.id_index');
                                                })
                                                ->leftJoinSub($stokInT, 'stokInT', function($join_in) {
                                                    $join_in->on('temp_transaction.value2', '=', 'stokInT.id_item');
                                                    $join_in->on('temp_transaction.value5', '=', 'stokInT.id_index');
                                                })
                                                ->leftJoinSub($stokOutT, 'stokOutT', function($join_out) {
                                                    $join_out->on('temp_transaction.value2', '=', 'stokOutT.id_item');
                                                    $join_out->on('temp_transaction.value5', '=', 'stokOutT.id_index');
                                                })
                                                ->select(
                                                    'temp_transaction.id',
                                                    'temp_transaction.value1',
                                                    'temp_transaction.value2',
                                                    'temp_transaction.value6',
                                                    'temp_transaction.value4',
                                                    'temp_transaction.value5',
                                                    'temp_transaction.value3',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan',
                                                    'dataSpek.value_spesifikasi',
                                                )
                                                ->where([
                                                    ['temp_transaction.id', '=', $id]
                                                ])
                                                ->get();

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


                foreach($detail as $dataTrf) {
                    $txtIndexF = "-";
                    $txtIndexT = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataTrf->value4) {
                            $txtIndexF = $txt["nama_index"];
                        }

                        if ($txt["id"] == $dataTrf->value4) {
                            $txtIndexT = $txt["nama_index"];
                        }
                    }
                    $dataTr = [
                        'id' => $dataTrf->id,
                        'kode_item' => $dataTrf->kode_item,
                        'nama_item' => $dataTrf->nama_item,
                        'nama_satuan' => $dataTrf->nama_satuan,
                        'value_spesifikasi' => $dataTrf->value_spesifikasi,
                        'id_transfer' => $dataTrf->value1,
                        'id_item' => $dataTrf->value2,
                        'id_satuan' => $dataTrf->value6,
                        'id_index_f' => $dataTrf->value4,
                        'txt_index_f' => $txtIndexF,
                        'id_index_t' => $dataTrf->value5,
                        'txt_index_t' => $txtIndexT,
                        'qty_item' => $dataTrf->value3,
                    ];
                    array_push($data, $dataTr);
                }
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function DeleteTransferDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            if ($mode != "") {
                $detail = TempTransaction::find($id);
                $detail->deleted_by = Auth::user()->user_name;
                $detail->action = "hapus";
                $detail->save();

                $detail->delete();
            }
            else {
                $delete = DB::table('stock_transfer_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function RestoretransferDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idtransfer');
            $restore = StockTransferDetail::onlyTrashed()->where([['id_transfer', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getStockItem(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idIndex = $request->input('idIndex');
        $idSatuan = $request->input('idSatuan');


        $stokIn = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in'],
                                                ['id_index', '=', $idIndex],
                                                ['id_satuan', '=', $idSatuan],
                                            ])
                                    ->groupBy('id_item');

        $stokOut = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out'],
                                        ['id_index', '=', $idIndex],
                                        ['id_satuan', '=', $idSatuan],
                                    ])
                                    ->groupBy('id_item');

        $dataProduct = Product::leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                    $join_in->on('product.id', '=', 'stokIn.id_item');
                                })
                                ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                    $join_out->on('product.id', '=', 'stokOut.id_item');
                                })
                                ->select(
                                    DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                )
                                ->where([
                                    ['product.id', '=', $idProduct]
                                ])
                                ->get();

        return response()->json($dataProduct);
    }

    public function getIndexList(Request $request)
    {
        $idProduct = $request->input('id_item');
        $idSatuan = $request->input('id_satuan');

        // $stokIn = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
        //                             ->where([
        //                                         ['transaksi', '=', 'in'],
        //                                         ['id_satuan', '=', $idSatuan],
        //                                     ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_index');

        // $stokOut = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
        //                             ->where([
        //                                 ['transaksi', '=', 'out'],
        //                                 ['id_satuan', '=', $idSatuan],
        //                             ])
        //                             ->groupBy('id_item')
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
                'txt_index' => $txt
            ];

            array_push($list, $dataTxt);
        }

        $dataStoks = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                            ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                            ->leftJoin('product_detail', function($join) use ($idSatuan) {
                                $join->on('product_detail.id_product', '=', 'product.id')->where('product_detail.id_satuan', '=', $idSatuan);
                            })
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
                                'dataStocks.id_index',
                                DB::raw('COALESCE(dataStocks.qty,0) AS stok_item')
                            )
                            ->where([
                                ['product.id', '=', $idProduct]
                            ])
                            ->get();

        $listIndex = [];
        foreach($dataStoks as $dataStock) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataStock->id_index) {
                    $txtIndex = $txt["txt_index"];
                }
            }
            if ($dataStock->stok_item > 0) {
                $indexList = [
                    'id' => $dataStock->id_index,
                    'txt_index' => $txtIndex,
                ];
                array_push($listIndex, $indexList);
            }
        }

        if (count($listIndex) < 1) {
            $listIndex = $list;
        }

        return response()->json($listIndex);
    }

    public function Delete(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            $detail = StockTransfer::find($id);
            $detail->deleted_by = Auth::user()->user_name;
            $detail->save();

            $detail->delete();
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetDataProduct(Request $request)
    {
        $idProduct = $request->input('idProduct');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'product.id',
                                    'product.kode_item',
                                    'product.nama_item',
                                    'product.jenis_item',
                                    'product_brand.nama_merk',
                                    'product_category.nama_kategori',
                                    'dataSpek.value_spesifikasi'
                                )
                                ->where([
                                    ['product.id', '=', $idProduct]
                                ])
                                ->get();


        return response()->json($dataProduct);
    }
}
