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
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductUnit;
use App\Models\Stock\StockConversion;
use App\Models\Stock\StockConversionDetail;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class StockConversionController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/StockConversion'],
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
                                                ['module.url', '=', '/StockConversion'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Conversion Stok',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Conversion Stok',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.conversion.index', $data);
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

        $dataConversion = StockConversion::orderBy('id','desc')
                                        ->get();


        return response()->json($dataConversion);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/StockConversion'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

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

                $parentMenu = Module::find($hakAkses->parent);
                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $dataProduct = Product::all();

                $data['hakAkses'] = $hakAkses;
                $data['dataProduct'] = $dataProduct;
                $data['listIndex'] = $list;

                $log = ActionLog::create([
                    'module' => 'Conversion',
                    'action' => 'Buat',
                    'desc' => 'Buat Conversion',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('stock_conversion_detail')
                            ->where([
                                ['id_conversion', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.stock.conversion.add', $data);
            }
            else {
                return redirect('/Conversion')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/StockConversion'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataProduct = Product::all();


                $restore = StockConversionDetail::onlyTrashed()->where([['id_conversion', '=', $id]]);
                $restore->restore();

                $dataConversion = StockConversion::find($id);

                $parentMenu = Module::find($hakAkses->parent);

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
                $data['dataConversion'] = $dataConversion;
                $data['listIndex'] = $list;

                $log = ActionLog::create([
                    'module' => 'Stock Conversion',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Stock Conversion',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.conversion.edit', $data);
            }
            else {
                return redirect('/StockConversion')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/StockConversion'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataConversion = StockConversion::find($id);

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataConversion'] = $dataConversion;

                $log = ActionLog::create([
                    'module' => 'Stock Conversion',
                    'action' => 'Detail',
                    'desc' => 'Detail Stock Conversion',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.stock.conversion.detail', $data);
            }
            else {
                return redirect('/StockConversion')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function GetConversionDetailFrom(Request $request)
    {
        $id = $request->input('idConversion');
        if ($id == "") {
            $id = 'DRAFT';
        }

        $data = [];
        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $detail = StockConversionDetail::leftJoin('product', 'stock_conversion_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'stock_conversion_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'stock_conversion_detail.id',
                                            'stock_conversion_detail.id_item',
                                            'stock_conversion_detail.id_satuan',
                                            'stock_conversion_detail.id_index',
                                            'stock_conversion_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['stock_conversion_detail.id_conversion', '=', $id],
                                            ['stock_conversion_detail.jenis', '=', 'out']
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


        foreach($detail as $dataConv) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataConv->id_index) {
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataTr = [
                'id' => $dataConv->id,
                'kode_item' => $dataConv->kode_item,
                'nama_item' => $dataConv->nama_item,
                'nama_satuan' => $dataConv->nama_satuan,
                'id' => $dataConv->id,
                'id_satuan' => $dataConv->id_satuan,
                'id_item' => $dataConv->id_item,
                'id_index' => $dataConv->id_index,
                'txt_index' => $txtIndex,
                'qty_item' => $dataConv->qty_item,


            ];
            array_push($data, $dataTr);
        }

        return response()->json($data);
    }

    public function GetConversionDetailTo(Request $request)
    {
        $id = $request->input('idConversion');
        if ($id == "") {
            $id = 'DRAFT';
        }

        $data = [];
        $dataIndex = StockIndex::with('ancestors')->withDepth()->whereIsLeaf()->defaultOrder()->get();

        $detail = StockConversionDetail::leftJoin('product', 'stock_conversion_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'stock_conversion_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'stock_conversion_detail.id',
                                            'stock_conversion_detail.id_item',
                                            'stock_conversion_detail.id_satuan',
                                            'stock_conversion_detail.id_index',
                                            'stock_conversion_detail.qty_item',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['stock_conversion_detail.id_conversion', '=', $id],
                                            ['stock_conversion_detail.jenis', '=', 'in']
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


        foreach($detail as $dataConv) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataConv->id_index) {
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataTr = [
                'id' => $dataConv->id,
                'kode_item' => $dataConv->kode_item,
                'nama_item' => $dataConv->nama_item,
                'nama_satuan' => $dataConv->nama_satuan,
                'id' => $dataConv->id,
                'id_satuan' => $dataConv->id_satuan,
                'id_item' => $dataConv->id_item,
                'id_index' => $dataConv->id_index,
                'txt_index' => $txtIndex,
                'qty_item' => $dataConv->qty_item,


            ];
            array_push($data, $dataTr);
        }

        return response()->json($data);
    }

    public function Store(Request $request)
    {
        $tgl = $request->input('tanggal_conversion');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/StockConversion')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;
            $tgl = $request->input('tanggal_conversion');
            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('stock_conversion')
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
                $nmrConversion = "con-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrConversion = "con-cv-".$kodeTgl.$counter;
            }


            $conv = new StockConversion();
            $conv->kode_transaksi = $nmrConversion;
            $conv->tgl_transaksi = $tgl;
            $conv->keterangan = $keterangan;
            $conv->status_conversion = 'draft';
            $conv->flag_revisi = 0;
            $conv->created_by = $user;
            $conv->save();

            $data = $conv;

            $setDetail = DB::table('stock_conversion_detail')
                                ->where([
                                            ['id_conversion', '=', 'DRAFT'],
                                            ['created_by', '=', $user],
                                        ])
                                ->update([
                                    'id_conversion' => $conv->id,
                                    'updated_by' => $user
                                ]);

            $log = ActionLog::create([
                'module' => 'Conversion',
                'action' => 'Simpan',
                'desc' => 'Simpan Conversion',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('StockConversion.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_transaksi).' Telah Disimpan!');
        }
        else {
            return redirect('/StockConversion')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $tgl = $request->input('tanggal_conversion');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/StockConversion')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $keterangan = $request->input('keterangan');
            $user = Auth::user()->user_name;
            $tgl = $request->input('tanggal_conversion');

            $conv = StockConversion::find($id);
            $conv->tgl_transaksi = $tgl;
            $conv->keterangan = $keterangan;
            $conv->updated_by = $user;
            $conv->save();

            $data = $conv;

            $deletedDetail = StockConversionDetail::onlyTrashed()->where([['id_conversion', '=', $id]]);
            $deletedDetail->forceDelete();

            $log = ActionLog::create([
                'module' => 'Conversion',
                'action' => 'Ubah',
                'desc' => 'Ubah Conversion',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('StockConversion.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_transaksi).' Telah Disimpan!');
        }
        else {
            return redirect('/StockConversion')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $conversion = StockConversion::find($id);

            if ($btnAction == "posting") {
                $detailConv = StockConversionDetail::where([
                                                    ['stock_conversion_detail.id_conversion', '=', $id]
                                                ])
                                                ->get();
                $transaksi = [];
                $failedItem = [];
                foreach ($detailConv As $detail) {

                    if ($detail->jenis == "in") {
                        $jenisSumber = 4;
                    }
                    else {
                        $jenisSumber = 0;
                    }

                    $dataDetail = [
                        'kode_transaksi' => $conversion->kode_transaksi,
                        'id_item' => $detail->id_item,
                        'id_satuan' => $detail->id_satuan,
                        'id_index' => $detail->id_index,
                        'qty_item' => $detail->qty_item,
                        'tgl_transaksi' => $conversion->tgl_transaksi,
                        'jenis_transaksi' => "konversi",
                        'transaksi' => $detail->jenis,
                        'jenis_sumber' => $jenisSumber,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($transaksi, $dataDetail);
                }

                StockTransaction::insert($transaksi);

                $conversion->status_conversion = "posted";
                $conversion->save();

                $log = ActionLog::create([
                    'module' => 'Stock Conversion',
                    'action' => 'Posting',
                    'desc' => 'Posting Stock Conversion',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Data '.strtoupper($conversion->no_so).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $conversion->kode_transaksi)->delete();

                $conversion->status_conversion = "draft";
                $conversion->flag_revisi = '1';
                $conversion->updated_by = Auth::user()->user_name;
                $conversion->save();


                $log = ActionLog::create([
                    'module' => 'Stock Conversion',
                    'action' => 'Revisi',
                    'desc' => 'Revisi Stock Conversion',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Konversi Barang '.strtoupper($conversion->kode_transaksi).' Telah Direvisi!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $conversion->kode_transaksi)->delete();

                $conversion->status_conversion = "batal";
                $conversion->flag_revisi = '1';
                $conversion->updated_by = Auth::user()->user_name;
                $conversion->save();

                $log = ActionLog::create([
                    'module' => 'Stock Conversion',
                    'action' => 'Batal',
                    'desc' => 'Batal Stock Conversion',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Konversi '.strtoupper($conversion->kode_transaksi).' Telah Dibatalkan!';
                $status = "success";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('StockConversion.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function StoreConversionDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idConversion');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndex = $request->input('idIndex');
            $qty = $request->input('qtyItem');
            $jenis = $request->input('jenisConversion');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            $countItem = DB::table('stock_conversion_detail')
                            ->select(DB::raw("COUNT(*) AS angka"))
                            ->where([
                                ['id_conversion', '=' , $id],
                                ['id_item', '=', $idItem],
                                ['id_satuan', '=', $idSatuan],
                                ['id_index', '=', $idIndex],
                                ['jenis', '=', $jenis],
                                ['deleted_at', '=', null]
                            ])
                            ->first();
            $count = $countItem->angka;

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listItem = new StockConversionDetail();
                $listItem->id_conversion = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->id_index = $idIndex;
                $listItem->qty_item = $qty;
                $listItem->jenis = $jenis;
                $listItem->created_by = $user;
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'Conversion Detail',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Conversion Detail',
                    'username' => Auth::user()->user_name
                ]);
                $data = "success";
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function UpdateConversionDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idConversion');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idIndex = $request->input('idIndex');
            $qty = $request->input('qtyItem');
            $jenis = $request->input('jenisConversion');
            $idDetail = $request->input('idDetail');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            $listItem = StockConversionDetail::find($idDetail);
            $listItem->id_Conversion = $id;
            $listItem->id_item = $idItem;
            $listItem->id_satuan = $idSatuan;
            $listItem->id_index = $idIndex;
            $listItem->qty_item = $qty;
            $listItem->updated_by = $user;
            $listItem->save();

            $log = ActionLog::create([
                'module' => 'Conversion Detail',
                'action' => 'Update',
                'desc' => 'Update Conversion Detail',
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

    public function EditConversionDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $idIndex = $request->input('idIndex');

            $stokIn = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
                                        ->where([
                                                    ['transaksi', '=', 'in'],

                                                ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->groupBy('id_index');

            $stokOut = StockTransaction::select('id_item', 'id_satuan', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
                                        ->where([
                                            ['transaksi', '=', 'out'],

                                        ])
                                        ->groupBy('id_item')
                                        ->groupBy('id_satuan')
                                        ->groupBy('id_index');

            $detail = StockConversionDetail::leftJoin('product', 'stock_conversion_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'stock_conversion_detail.id_satuan', 'product_unit.id')
                                            ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                                $join_in->on('stock_conversion_detail.id_item', '=', 'stokIn.id_item');
                                                $join_in->on('stock_conversion_detail.id_satuan', '=', 'stokIn.id_satuan');
                                                $join_in->on('stock_conversion_detail.id_index', '=', 'stokIn.id_index');
                                            })
                                            ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                                $join_out->on('stock_conversion_detail.id_item', '=', 'stokOut.id_item');
                                                $join_out->on('stock_conversion_detail.id_satuan', '=', 'stokOut.id_satuan');
                                                $join_out->on('stock_conversion_detail.id_index', '=', 'stokOut.id_index');
                                            })
                                            ->select(
                                                'stock_conversion_detail.id',
                                                'stock_conversion_detail.id_item',
                                                'stock_conversion_detail.id_satuan',
                                                'stock_conversion_detail.id_index',
                                                'stock_conversion_detail.qty_item',
                                                'stock_conversion_detail.jenis',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                DB::raw('COALESCE(stokIn.stok_in,0) - COALESCE(stokOut.stok_out,0) AS stok_item'),
                                            )
                                            ->where([
                                                ['stock_conversion_detail.id', '=', $id]
                                            ])
                                            ->get();

            $data = $detail;
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function DeleteConversionDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');

            if ($mode != "") {
                $detail = StockConversionDetail::find($id);
                $detail->deleted_by = Auth::user()->user_name;
                $detail->save();
                $detail->delete();
            }
            else {
                $delete = DB::table('stock_conversion_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function RestoreConversionDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idConversion');
            $restore = StockConversionDetail::onlyTrashed()->where([['id_conversion', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }
}
