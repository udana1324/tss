<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\Receiving;
use App\Models\Purchasing\ReceivingDetail;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperAccounting;
use App\Classes\BusinessManagement\HelperPurchaseInvoice;
use App\Exports\PurchaseInvoiceExport;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\PurchaseInvoiceTerms;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\TempTransaction;
use Maatwebsite\Excel\Facades\Excel;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseInvoice'],
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
                                                ['module.url', '=', '/PurchaseInvoice'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = PurchaseInvoice::distinct()->get('status_invoice');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('purchase_invoice_detail')->where('deleted_at', '!=', null)->delete();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Purchase Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex(Request $request)
    {
        $periode = $request->input('periode');

        $purchaseOrder = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', 'purchase_order.id')
                            ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                            ->select(
                                'supplier.nama_supplier',
                                'purchase_order.no_po',
                                'purchase_invoice.id',
                                'purchase_invoice.kode_invoice',
                                'purchase_invoice.dpp',
                                'purchase_invoice.ppn',
                                'purchase_invoice.grand_total',
                                'purchase_invoice.ttl_qty',
                                'purchase_invoice.tanggal_invoice',
                                'purchase_invoice.tanggal_jt',
                                'purchase_invoice.flag_revisi',
                                'purchase_invoice.flag_tf',
                                'purchase_invoice.status_invoice',
                                'purchase_invoice.flag_pembayaran',
                                DB::raw("CASE WHEN purchase_invoice.flag_pembayaran = 1 THEN 1
                                              WHEN purchase_invoice.flag_pembayaran = 2 THEN 2
                                              WHEN purchase_invoice.flag_pembayaran = 0 THEN 3
                                        END AS flag_pembayaran_filter"))
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($periode)->format('m'));
                                $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($periode)->format('Y'));
                            })
                            ->when($periode == "", function($q) use ($periode) {
                                $q->where('purchase_invoice.tanggal_invoice', '>=', Carbon::now()->subMonth(12)->startOfMonth()->format('Y-m-d'));
                            })
                            ->orderBy('purchase_invoice.id', 'desc')
                            ->get();
        return response()->json($purchaseOrder);
    }

    public function RestorePurchaseInvoiceDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idInvoice');
            $restore = PurchaseInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }


    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->select('supplier.id', 'supplier.nama_supplier')
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->join('receiving', 'receiving.id_po', 'purchase_order.id')
                                        ->where([
                                            ['receiving.status_penerimaan', '=', 'posted'],
                                            ['receiving.flag_invoiced', '=', '0']
                                        ])
                                        ->get();
                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;
                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Buat',
                    'desc' => 'Buat Purchase Invoice',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('purchase_invoice_detail')
                            ->where([
                                ['id_invoice', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.purchasing.invoice.add', $data);
            }
            else {
                return redirect('/PurchaseInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->select('supplier.id', 'supplier.nama_supplier')
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->join('receiving', 'receiving.id_po', 'purchase_order.id')
                                        ->where([
                                            ['receiving.status_penerimaan', '=', 'posted'],
                                            ['receiving.flag_invoiced', '=', '0']
                                        ])
                                        ->get();

                $dataInv = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('preference', 'purchase_order.id_alamat', '=', 'preference.id')
                                    ->select(
                                        'purchase_invoice.id',
                                        'purchase_invoice.kode_invoice',
                                        'purchase_invoice.id_po',
                                        'purchase_order.id_alamat',
                                        'purchase_invoice.dp',
                                        'purchase_invoice.tanggal_invoice',
                                        'purchase_invoice.durasi_jt',
                                        'purchase_invoice.tanggal_jt',
                                        'purchase_invoice.flag_ppn',
                                        'purchase_invoice.status_invoice',
                                        'purchase_order.id_supplier',
                                        'purchase_order.jenis_diskon',
                                        'purchase_order.metode_pembayaran',
                                        DB::raw("CASE WHEN purchase_order.jenis_diskon = 'P' THEN purchase_order.persentase_diskon ELSE purchase_order.nominal_diskon END AS value_diskon"),
                                        'preference.alamat_pt',
                                    )
                                    ->where([
                                        ['purchase_invoice.id', '=', $id],
                                    ])
                                    ->first();

                if ($dataInv->status_invoice != "draft") {
                    return redirect('/PurchaseInvoice')->with('warning', 'Invoice Pembelian tidak dapat diubah karena status Invoice bukan DRAFT!');
                }

                // $restore = PurchaseInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_invoice'],
                                    ['value1', '=', $id]
                                ])->delete();

                $dataDetail = PurchaseInvoiceDetail::where([
                                                    ['id_invoice', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'purchase_invoice',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_invoice,
                            'value2' => $detail->id_sj,
                            'value3' => $detail->qty_sj,
                            'value4' => $detail->subtotal_sj
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }


                $dataTerms = PurchaseInvoiceTerms::where('id_invoice', $id)->get();
                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);
                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataInv'] = $dataInv;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Purchase Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice.edit', $data);
            }
            else {
                return redirect('/PurchaseInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataInv = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                    ->leftJoin('preference', 'purchase_order.id_alamat', '=', 'preference.id')
                                    ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                    ->select(
                                        'purchase_invoice.id',
                                        'purchase_invoice.kode_invoice',
                                        'purchase_invoice.id_po',
                                        'purchase_invoice.dp',
                                        'purchase_invoice.tanggal_invoice',
                                        'purchase_invoice.durasi_jt',
                                        'purchase_invoice.tanggal_jt',
                                        'purchase_invoice.flag_ppn',
                                        'purchase_invoice.flag_revisi',
                                        'purchase_invoice.status_invoice',
                                        'purchase_invoice.flag_terms_po',
                                        'purchase_invoice.id_ppn',
                                        'purchase_order.id_supplier',
                                        'purchase_order.no_po',
                                        'purchase_order.jenis_diskon',
                                        'purchase_order.persentase_diskon',
                                        'purchase_order.nominal_diskon',
                                        'purchase_order.sisa_dp',
                                        'purchase_order.metode_pembayaran',
                                        DB::raw("CASE WHEN purchase_order.jenis_diskon = 'P' THEN purchase_order.persentase_diskon ELSE purchase_order.nominal_diskon END AS value_diskon"),
                                        'preference.alamat_pt',
                                        'supplier.nama_supplier',
                                    )
                                    ->where([
                                        ['purchase_invoice.id', '=', $id],
                                    ])
                                    ->first();

                $dataPreference = Preference::select(
                                                'preference.nama_pt',
                                                DB::raw("CONCAT(alamat_pt, ', ', COALESCE(kelurahan_pt, '-'), ', ', kecamatan_pt, ', ', kota_pt) AS alamat")
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();

                $dataTerms = PurchaseInvoiceTerms::where('id_invoice', $id)->get();
                $parentMenu = Module::find($hakAkses->parent);
                $taxSettings = TaxSettingsPPN::find($dataInv->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataInv'] = $dataInv;
                $data['dataPreference'] = $dataPreference;
                $data['dataTerms'] = $dataTerms;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Detail',
                    'desc' => 'Detail Purchase Invoice',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice.detail', $data);
            }
            else {
                return redirect('/PurchaseInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getPurchaseOrder(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $dataPo = PurchaseOrder::distinct()
                                ->leftJoin('receiving', 'receiving.id_po', '=', 'purchase_order.id')
                                ->select(
                                    'purchase_order.id',
                                    'purchase_order.no_po',
                                )
                                ->where([
                                    ['purchase_order.id_supplier', '=', $idSupplier],
                                    ['receiving.status_penerimaan', '=', 'posted'],
                                    ['receiving.flag_invoiced', '=', '0']
                                ])
                                ->orderBy('purchase_order.id', 'desc')
                                ->get();

        return response()->json($dataPo);
    }

    public function getPurchaseOrderData(Request $request)
    {
        $idPo = $request->input('idPurchaseOrder');

        $dataPo = PurchaseOrder::where('id', $idPo)->get();

        return response()->json($dataPo);
    }

    public function getReceiving(Request $request)
    {
        $idPurchaseOrder = $request->input('idPurchaseOrder');

        $dataReceiving = Receiving::where([
                                    ['id_po', '=', $idPurchaseOrder],
                                    ['flag_invoiced', '=', '0']
                                ])
                                ->orderBy('id', 'asc')
                                ->get();

        return response()->json($dataReceiving);
    }

    public function getDefaultAddress(Request $request)
    {
        $idPurchaseOrder = $request->input('idPurchaseOrder');

        $idAlamat = PurchaseOrder::find($idPurchaseOrder);

        $defaultAddress = Preference::where([
                                            ['id', '=', $idAlamat->id_alamat]
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getDataReceiving(Request $request)
    {
        $idReceiving = $request->input('idReceiving');
        $rcv = Receiving::find($idReceiving);
        $idPo = $rcv->id_po;

        $detailRcv = ReceivingDetail::leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', 'receiving_detail.id_item')
                                    ->select(
                                        'receiving_detail.id_penerimaan',
                                        DB::raw('SUM(receiving_detail.qty_item * purchase_order_detail.harga_beli) AS subtotalRcv')
                                    )
                                    ->whereIn('receiving_detail.id_penerimaan', function($subQuery) use ($idPo) {
                                        $subQuery->select('id')->from('receiving')
                                        ->where('id_po', $idPo);
                                    })
                                    ->where([
                                        ['purchase_order_detail.id_po', '=', $idPo],
                                    ])
                                    ->groupBy('receiving_detail.id_penerimaan');

        $dataRcv = Receiving::leftJoinSub($detailRcv, 'detailRcv', function($detailRcv) {
                                $detailRcv->on('receiving.id', '=', 'detailRcv.id_penerimaan');
                            })
                            ->select(
                                'receiving.id',
                                'receiving.kode_penerimaan',
                                'receiving.tanggal_sj',
                                'receiving.tanggal_terima',
                                'receiving.jumlah_total_sj',
                                'detailRcv.subtotalRcv',
                            )
                            ->where([
                                ['receiving.id', '=', $idReceiving],
                            ])
                            ->orderBy('receiving.tanggal_sj', 'desc')
                            ->get();


        return response()->json($dataRcv);
    }

    public function StoreInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idInvoice');
            $idRcv = $request->input('idRcv');
            $qty = $request->input('qtyRcv');
            $subtotal = $request->input('subtotalRcv');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);
            $subtotal = str_replace(",", ".", $subtotal);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DB::table('purchase_invoice_detail')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['id_invoice', '=' , $id],
                                    ['id_sj', '=', $idRcv],
                                    ['deleted_at', '=', null]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new PurchaseInvoiceDetail();
                    $listItem->id_invoice = $id;
                    $listItem->id_sj = $idRcv;
                    $listItem->qty_sj = $qty;
                    $listItem->subtotal_sj = $subtotal;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Invoice Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Invoice Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {
                $countItem = DB::table('temp_transaction')
                                ->select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'purchase_invoice'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idRcv]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'purchase_invoice';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idRcv;
                    $listItem->value3 = $qty;
                    $listItem->value4 = $subtotal;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Purchase Invoice Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Purchase Invoice Detail',
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

    public function SetInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idInvoice');
            $idPo = $request->input('idPurchaseOrder');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }
            if ($id != 'DRAFT') {
                $update = DB::table('temp_transaction')
                            ->where([
                                ['value1', '=', $id],
                                ['module', '=', "purchase_invoice"]
                            ])
                            ->update([
                                'action' => "hapus",
                                'deleted_by' => Auth::user()->user_name,
                                'deleted_at' => now()
                            ]);

                $detailRcv = ReceivingDetail::leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', 'receiving_detail.id_item')
                                            ->select(
                                                'receiving_detail.id_penerimaan',
                                                DB::raw('SUM(receiving_detail.qty_item * purchase_order_detail.harga_beli) AS subtotalRcv')
                                            )
                                            ->whereIn('receiving_detail.id_penerimaan', function($subQuery) use ($idPo) {
                                                $subQuery->select('id')->from('receiving')
                                                ->where('id_po', $idPo);
                                            })
                                            ->where([
                                                ['purchase_order_detail.id_po', '=', $idPo],
                                            ])
                                            ->groupBy('receiving_detail.id_penerimaan');

                $dataRcv = Receiving::leftJoinSub($detailRcv, 'detailRcv', function($detailRcv) {
                                        $detailRcv->on('receiving.id', '=', 'detailRcv.id_penerimaan');
                                    })
                                    ->select(
                                        'receiving.id',
                                        'receiving.kode_penerimaan',
                                        'receiving.tanggal_sj',
                                        'receiving.tanggal_terima',
                                        'receiving.jumlah_total_sj',
                                        'detailRcv.subtotalRcv',
                                    )
                                    ->where([
                                        ['receiving.status_penerimaan', '=', 'posted'],
                                        ['receiving.id_po', '=', $idPo],
                                        ['receiving.flag_invoiced', '=', '0'],
                                    ])
                                    ->get();

                $data = $dataRcv;

                $listDetail = [];
                foreach ($dataRcv As $detail) {

                    $dataDetail = [
                        'module' => "purchase_invoice",
                        'value1' => $id,
                        'value2' => $detail->id,
                        'value3' => $detail->jumlah_total_sj,
                        'value4' => $detail->subtotalRcv,
                        'action' => "tambah",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                TempTransaction::insert($listDetail);
            }
            else {
                $delete = DB::table('purchase_invoice_detail')
                            ->where('id_invoice', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('purchase_invoice_detail.created_by', $user);
                            })
                            ->delete();

                    $detailRcv = ReceivingDetail::leftJoin('purchase_order_detail', 'purchase_order_detail.id_item', 'receiving_detail.id_item')
                                                ->select(
                                                    'receiving_detail.id_penerimaan',
                                                    DB::raw('SUM(receiving_detail.qty_item * purchase_order_detail.harga_beli) AS subtotalRcv')
                                                )
                                                ->whereIn('receiving_detail.id_penerimaan', function($subQuery) use ($idPo) {
                                                    $subQuery->select('id')->from('receiving')
                                                    ->where('id_po', $idPo);
                                                })
                                                ->where([
                                                    ['purchase_order_detail.id_po', '=', $idPo],
                                                ])
                                                ->groupBy('receiving_detail.id_penerimaan');

                    $dataRcv = Receiving::leftJoinSub($detailRcv, 'detailRcv', function($detailRcv) {
                                            $detailRcv->on('receiving.id', '=', 'detailRcv.id_penerimaan');
                                        })
                                        ->select(
                                            'receiving.id',
                                            'receiving.kode_penerimaan',
                                            'receiving.tanggal_sj',
                                            'receiving.tanggal_terima',
                                            'receiving.jumlah_total_sj',
                                            'detailRcv.subtotalRcv',
                                        )
                                        ->where([
                                            ['receiving.status_penerimaan', '=', 'posted'],
                                            ['receiving.id_po', '=', $idPo],
                                            ['receiving.flag_invoiced', '=', '0'],
                                        ])
                                        ->get();


                    $listDetail = [];
                    foreach ($dataRcv As $detail) {

                    $dataDetail = [
                        'id_invoice' => $id,
                        'id_sj' => $detail->id,
                        'qty_sj' => $detail->jumlah_total_sj,
                        'subtotal_sj' => $detail->subtotalRcv,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                PurchaseInvoiceDetail::insert($listDetail);
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceDetail(Request $request)
    {
        $id = $request->input('idInvoice');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {

            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = PurchaseInvoiceDetail::leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                                        ->select(
                                            'purchase_invoice_detail.id',
                                            'purchase_invoice_detail.id_sj',
                                            'purchase_invoice_detail.qty_sj',
                                            'purchase_invoice_detail.subtotal_sj',
                                            'receiving.kode_penerimaan',
                                            'receiving.tanggal_sj',
                                            'receiving.tanggal_terima'
                                        )
                                        ->where([
                                            ['purchase_invoice_detail.id_invoice', '=', $id],
                                        ])
                                        ->when($id == "DRAFT", function($q) use ($user) {
                                            $q->where('purchase_invoice_detail.created_by', $user);
                                        })
                                        ->orderBy('receiving.tanggal_sj', 'desc')
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('receiving', 'temp_transaction.value2', '=', 'receiving.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'receiving.kode_penerimaan',
                                            'receiving.tanggal_sj',
                                            'receiving.tanggal_terima'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_invoice']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteInvoiceDetail(Request $request)
    {
        $data = "";
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
                $delete = DB::table('purchase_invoice_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceFooter(Request $request)
    {
        $id = $request->input('idInvoice');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if($mode != "edit") {

            $detail = PurchaseInvoiceDetail::select(
                                                DB::raw('COALESCE(SUM(purchase_invoice_detail.qty_sj),0) AS qtyInv'),
                                                DB::raw('COALESCE(SUM(purchase_invoice_detail.subtotal_sj),0) AS subtotalInv'),
                                            )
                                            ->where([
                                                ['purchase_invoice_detail.id_invoice', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('purchase_invoice_detail.created_by', $user);
                                            })
                                            ->groupBy('purchase_invoice_detail.id_invoice')
                                            ->first();
        }
        else {
            $detail = TempTransaction::select(
                                            DB::raw('COALESCE(SUM(temp_transaction.value3),0) AS qtyInv'),
                                            DB::raw('COALESCE(SUM(temp_transaction.value4),0) AS subtotalInv'),
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'purchase_invoice']
                                        ])
                                        ->groupBy('temp_transaction.value1')
                                        ->first();
        }

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function GetDate(Request $request)
    {
        $id = $request->input('idInvoice');
        $user = Auth::user()->user_name;

        $detail = Receiving::select(
                                DB::raw('MIN(receiving.tanggal_sj) AS firstDate'),
                                DB::raw('MAX(receiving.tanggal_sj) AS lastDate'),
                            )
                            ->whereIn('receiving.id', function($subQuery) use ($id, $user) {
                                $subQuery->select('id_sj')->from('purchase_invoice_detail')
                                ->where([
                                    ['id_invoice', '=', $id],
                                ])
                                ->when($id == "DRAFT", function($q) use ($user) {
                                    $q->where('purchase_invoice_detail.created_by', $user);
                                });
                            })
                            ->first();

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function getListTerms(Request $request)
    {
        $target = $request->input('target');

        $listTemplate = TermsAndConditionTemplate::where([
                                            ['target_template', '=', $target]
                                        ])
                                        ->get();

        return response()->json($listTemplate);
    }

    public function getTerms(Request $request)
    {
        $id = $request->input('idTemplate');

        $template = TermsAndConditionTemplateDetail::where([
                                            ['id_template', '=', $id]
                                        ])
                                        ->get();

        return response()->json($template);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier'=>'required',
            'purchaseOrder'=>'required',
            'tanggal_inv'=>'required',
        ]);

        $tglInv = $request->input('tanggal_inv');
        $flagPpn = $request->input('stat_ppn');

        $bulanIndonesia = Carbon::parse($tglInv)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglInv);
        if (!$aksesTransaksi) {
            return redirect('/PurchaseInvoice')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglInv);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/PurchaseInvoice')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }


        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idPurchaseOrder = $request->input('purchaseOrder');
            $tglInv = $request->input('tanggal_inv');
            $tenor = $request->input('durasiJT');
            $tglJt = $request->input('tgl_jt');
            $qty = $request->input('qtyTtl');
            $dp = $request->input('dp');
            $dpp = $request->input('dpp');
            $ppn = $request->input('ppn');
            $gt = $request->input('gt');
            $flagPPn = $request->input('stat_ppn');
            $user = Auth::user()->user_name;

            $flagTerms = $request->input('terms_usage');

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idPurchaseOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $qty = str_replace(",", ".", $qty);
            $dp = str_replace(",", ".", $dp);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $gt = str_replace(",", ".", $gt);

            $blnPeriode = date("m", strtotime($tglInv));
            $thnPeriode = date("Y", strtotime($tglInv));
            $tahunPeriode = date("y", strtotime($tglInv));

            $countKode = DB::table('purchase_invoice')
                            ->select(DB::raw("MAX(RIGHT(kode_invoice,2)) AS angka"))
                            // ->whereMonth('tanggal_invoice', $blnPeriode)
                            // ->whereYear('tanggal_invoice', $thnPeriode)
                            ->whereDate('tanggal_invoice', $tglInv)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglInv)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglInv))));

            if ($counter < 10) {
                $kodeInv = "fkb-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeInv = "fkb-cv-".$kodeTgl.$counter;
            }

            $purchasing = new PurchaseInvoice();
            $purchasing->kode_invoice = $kodeInv;
            $purchasing->id_po = $idPurchaseOrder;
            $purchasing->dp = $dp;
            $purchasing->dpp = $dpp;
            $purchasing->ppn = $ppn;
            $purchasing->grand_total = $gt;
            $purchasing->ttl_qty = $qty;
            $purchasing->flag_ppn = $flagPPn;
            $purchasing->tanggal_invoice = $tglInv;
            $purchasing->durasi_jt = $tenor;
            $purchasing->tanggal_jt = $tglJt;
            $purchasing->status_invoice = 'draft';
            $purchasing->flag_revisi = '0';
            $purchasing->flag_pembayaran = '0';
            $purchasing->flag_terms_po = $flagTermsUsage;
            $purchasing->id_ppn = $taxSettings->ppn_percentage_id;
            $purchasing->created_by = $user;
            $purchasing->save();

            $data = $purchasing;

            $setDetail = DB::table('purchase_invoice_detail')
                            ->where([
                                        ['id_invoice', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_invoice' => $purchasing->id,
                                'updated_by' => $user
                            ]);

            if ($flagTerms != "termsPo") {
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_invoice' => $purchasing->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    PurchaseInvoiceTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Purchase Invoice',
                'action' => 'Simpan',
                'desc' => 'Simpan Purchase Invoice',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseInvoice.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_invoice).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseInvoice')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'purchaseOrder'=>'required',
            'tanggal_inv'=>'required',
        ]);

        $tglInv = $request->input('tanggal_inv');
        $flagPpn = $request->input('stat_ppn');

        $bulanIndonesia = Carbon::parse($tglInv)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglInv);
        if (!$aksesTransaksi) {
            return redirect()->route('PurchaseInvoice.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        //CekPeriodePPN
        $periodePPN = Helper::CheckPPNPeriod($tglInv);
        if (!$periodePPN && $flagPpn != "N") {
            return redirect('/PurchaseInvoice')->with('danger', 'Transaksi gagal!. Transaksi Diluar periode PPn, silahkan update Pengaturan Faktur Pajak Terlebih Dahulu!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $idPurchaseOrder = $request->input('purchaseOrder');
            $tglInv = $request->input('tanggal_inv');
            $tenor = $request->input('durasiJT');
            $tglJt = $request->input('tgl_jt');
            $qty = $request->input('qtyTtl');
            $dpp = $request->input('dpp');
            $ppn = $request->input('ppn');
            $gt = $request->input('gt');
            $flagPPn = $request->input('stat_ppn');
            $user = Auth::user()->user_name;

            $flagTerms = $request->input('terms_usage');

            if ($flagTerms == "termsPo") {
                $flagTermsUsage = $idPurchaseOrder;
            }
            else {
                $flagTermsUsage = "0";
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $qty = str_replace(",", ".", $qty);
            $dpp = str_replace(",", ".", $dpp);
            $ppn = str_replace(",", ".", $ppn);
            $gt = str_replace(",", ".", $gt);

            $purchasing = PurchaseInvoice::find($id);
            $purchasing->id_po = $idPurchaseOrder;
            $purchasing->dpp = $dpp;
            $purchasing->ppn = $ppn;
            $purchasing->grand_total = $gt;
            $purchasing->ttl_qty = $qty;
            $purchasing->flag_ppn = $flagPPn;
            $purchasing->tanggal_invoice = $tglInv;
            $purchasing->durasi_jt = $tenor;
            $purchasing->tanggal_jt = $tglJt;
            $purchasing->status_invoice = 'draft';
            $purchasing->id_ppn = $taxSettings->ppn_percentage_id;
            $purchasing->updated_by = $user;
            $purchasing->save();
            $data = $purchasing;

            // $deletedDetail = PurchaseInvoiceDetail::onlyTrashed()->where([['id_invoice', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'purchase_invoice'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = PurchaseInvoiceDetail::find($detail->id_detail);
                        $listItem->id_invoice = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->qty_sj = $detail->value3;
                        $listItem->subtotal_sj = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new PurchaseInvoiceDetail();
                        $listItem->id_invoice = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->qty_sj = $detail->value3;
                        $listItem->subtotal_sj = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('purchase_invoice_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'purchase_invoice'],
                                    ['value1', '=', $id]
                                ])->delete();


            if ($flagTerms != "termsPo") {
                $delete = DB::table('purchase_invoice_terms')->where('id_invoice', '=', $id)->delete();

                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_invoice' => $purchasing->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    PurchaseInvoiceTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Purchase Invoice',
                'action' => 'Update',
                'desc' => 'Update Purchase Invoice',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseInvoice.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_invoice).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseInvoice')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id) {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $purchasing = PurchaseInvoice::find($id);

            if ($btnAction == "posting") {

                $cekSj = HelperPurchaseInvoice::CheckSJ($id);
                $cekSJ2 = explode("|", $cekSj);

                if ($cekSJ2[0] == "failedInvoiced") {
                    $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Diposting karena terdapat surat jalan ('.strtoupper($cekSJ2[1]).') yang sudah dibuat di invoice lain!';
                    $status = "warning";
                }
                elseif ($cekSJ2[0] == "failedDraft") {
                    $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Diposting karena terdapat surat jalan ('.strtoupper($cekSJ2[1]).') yang masih berstatus draft!';
                    $status = "warning";
                }
                else {
                    try {
                        $purchaseOrder = PurchaseOrder::find($purchasing->id_po);
                        $purchaseOrder->sisa_dp = $purchaseOrder->sisa_dp - $purchasing->dp;
                        $purchaseOrder->save();

                        $updateSJ = HelperPurchaseInvoice::UpdateSJ($id, 1);

                        if ($updateSJ == 'ok') {
                            $purchasing->status_invoice = "posted";
                            $purchasing->save();

                            $ap = HelperAccounting::InsertAPBalance($purchasing->id, 'posting');

                            // $settings = GLAccountSettings::find(1);
                            // $dataPurchase = PurchaseOrder::find($purchasing->id_po);
                            // $dataSupplier = Supplier::find($dataPurchase->id_supplier);
                            // $idAkun = "";
                            // $idTransaksi = "";

                            // if ($dataSupplier !=  null) {
                            //     $idAkun = $dataSupplier->id_account ?? $settings->id_account_hutang;
                            // }
                            // else {
                            //     $idAkun = $settings->id_account_hutang;
                            // }

                            // $postJournal = HelperAccounting::PostJournal("purchase_invoice", $purchasing->id, $settings->id_account_persediaan, $idAkun, $purchasing->tanggal_invoice, $purchasing->grand_total, 'system');

                            // if ($postJournal['error'] == "") {
                            //     $purchasing->flag_entry = 1;

                            // }

                            // $purchasing->save();


                            $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Telah Diposting!';
                            $status = 'success';

                            DB::commit();
                        }
                        else {
                            DB::rollBack();

                            $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Diposting!';
                            $status = 'Danger';
                        }

                        $log = ActionLog::create([
                            'module' => 'Purchase Invoice',
                            'action' => 'Posting',
                            'desc' => 'Posting Purchase Invoice',
                            'username' => Auth::user()->user_name
                        ]);

                    }
                    catch (\Exception $e) {
                        DB::rollBack();

                        $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Diposting!'.$e;
                        $status = 'danger';
                    }
                }
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                try {
                    if ($purchasing->status_invoice == "posted" && $purchasing->flag_tf == 0) {
                        $removeAp = HelperAccounting::RemoveAPBalance($purchasing->id, "revisi");

                        if ($removeAp['error'] != 'success') {
                            $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Direvisi karena sudah terdapat pembayaran untuk invoice!';
                            $status = "warning";
                            DB::rollBack();
                        }
                        else {
                            $updateSJ = HelperPurchaseInvoice::UpdateSJ($id, 0);

                            if ($updateSJ == 'ok') {

                                $removeJournal = HelperAccounting::RemoveJournal("purchase_invoice", $purchasing->id);
                                if ($removeJournal == "success") {
                                    $purchasing->flag_entry = 0;
                                }

                                $purchasing->status_invoice = "draft";
                                $purchasing->flag_revisi = '1';
                                $purchasing->updated_by = Auth::user()->user_name;
                                $purchasing->save();

                                $log = ActionLog::create([
                                    'module' => 'Purchase Invoice',
                                    'action' => 'Revisi',
                                    'desc' => 'Revisi Purchase Invoice',
                                    'username' => Auth::user()->user_name
                                ]);

                                $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Telah Direvisi!';
                                $status = "success";
                                DB::commit();
                            }
                            else {
                                DB::rollBack();

                                $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Direvisi!';
                                $status = 'danger';
                            }
                        }
                    }
                    else {
                        $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Direvisi karena Invoice sudah melewati proses Tukar Faktur!';
                        $status = "warning";
                        DB::rollBack();
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();

                    $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Direvisi!';
                    $status = 'danger';
                }
            }
            elseif ($btnAction == "batalPO") {
                $result = HelperPurchaseInvoice::CancelInvoice($id);

                if ($result['error'] == "success") {
                    $removeAp = HelperAccounting::RemoveAPBalance($purchasing->id, "revisi");
                    $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Telah Dibatalkan sampai dengan Purchase Order!';
                    $status = "success";
                }
                elseif ($result['error'] == "failSJ") {
                    $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Dibatalkan karena masih terdapat Surat Jalan Aktif!';
                    $status = "warning";
                }
                else {
                    $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Dibatalkan!';
                    $status = "warning";
                }

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Batal SO',
                    'desc' => 'Pembatalan Purchase Invoice Ke PO',
                    'username' => Auth::user()->user_name
                ]);
            }
            elseif ($btnAction == "batalINV") {
                try {
                    if ($purchasing->status_invoice == "posted" && $purchasing->flag_tf == 0) {
                        $removeAp = HelperAccounting::RemoveAPBalance($purchasing->id, "revisi");

                        if ($removeAp['error'] != 'success') {
                            $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat dibatalkan karena sudah terdapat pembayaran untuk invoice!';
                            $status = "warning";
                            DB::rollBack();
                        }
                        else {
                            $updateSJ = HelperPurchaseInvoice::UpdateSJ($id, 0);

                            if ($updateSJ == 'ok') {
                                $purchasing->status_invoice = "batal";
                                $purchasing->updated_by = Auth::user()->user_name;
                                $purchasing->save();

                                $log = ActionLog::create([
                                    'module' => 'Purchase Invoice',
                                    'action' => 'Batal',
                                    'desc' => 'Batal Purchase Invoice',
                                    'username' => Auth::user()->user_name
                                ]);

                                $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Telah Dibatalkan!';
                                $status = "success";
                                DB::commit();
                            }
                            else {
                                DB::rollBack();

                                $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Dibatalkan!';
                                $status = 'danger';
                            }
                        }
                    }
                    else {
                        $msg = 'Purchase Invoice '.strtoupper($purchasing->kode_invoice).' Tidak dapat Dibatalkan karena Invoice sudah melewati proses Tukar Faktur!';
                        $status = "warning";
                        DB::rollBack();
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();

                    $msg = 'Data '.strtoupper($purchasing->kode_invoice).' Gagal Direvisi!';
                    $status = 'danger';
                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('PurchaseInvoice.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetPurchaseInvoiceDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idInv');


            if ($id != "DRAFT") {
                // $detail = PurchaseInvoiceDetail::where([
                //                             ['id_invoice', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);

                $deleteTemp = TempTransaction::where([
                                    ['module', '=', 'purchase_invoice'],
                                    ['value1', '=', $id]
                                ])->update([
                                    'action' => 'hapus',
                                    'deleted_at' => now(),
                                    'deleted_by' => Auth::user()->user_name
                                ]);
            }
            else {
                $delete = DB::table('purchase_invoice_detail')->where('id_invoice', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetReceivingDetail(Request $request)
    {
        $idSj = $request->input('idReceiving');
        $idPo = $request->input('idPo');

        $dataDetail = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                            ->select('purchase_order_detail.id_item', 'purchase_order_detail.id_satuan', 'purchase_order_detail.harga_beli')
                                            ->where([
                                                    ['purchase_order.id', '=', $idPo]
                                        ]);

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $detail = ReceivingDetail::leftJoin('receiving', 'receiving_detail.id_penerimaan', '=', 'receiving.id')
                                ->leftJoin('product', 'receiving_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                ->leftJoinSub($dataDetail, 'dataDetail', function($dataDetail) {
                                    $dataDetail->on('receiving_detail.id_item', '=', 'dataDetail.id_item');
                                    $dataDetail->on('receiving_detail.id_satuan', '=', 'dataDetail.id_satuan');
                                })
                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                })
                                ->select(
                                    'product.kode_item',
                                    'product.nama_item',
                                    'product_unit.nama_satuan',
                                    'dataDetail.harga_beli',
                                    'receiving.id',
                                    'receiving_detail.qty_item',
                                    'receiving.kode_penerimaan',
                                    DB::raw("receiving_detail.qty_item * dataDetail.harga_beli AS subtotal_sj"),
                                    'dataSpek.value_spesifikasi'

                                )
                                ->where([
                                    ['receiving.id', '=', $idSj],
                                ])
                                ->get();

        return response()->json($detail);
    }

    public function cetak($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseInvoice'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataPurchaseInvoice = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                ->leftJoin('supplier', 'purchase_order.id_supplier', '=', 'supplier.id')
                                                ->select(
                                                    'supplier.kode_supplier',
                                                    'supplier.nama_supplier',
                                                    'supplier.npwp_supplier',
                                                    'supplier.telp_supplier',
                                                    'supplier.fax_supplier',
                                                    'supplier.email_supplier',
                                                    'supplier.kategori_supplier',
                                                    'purchase_order.no_po',
                                                    'purchase_order.id_supplier',
                                                    'purchase_order.id_alamat',
                                                    'purchase_order.metode_pembayaran',
                                                    'purchase_order.persentase_diskon',
                                                    DB::raw("purchase_order.persentase_diskon/100 *  purchase_invoice.dpp AS diskon"),
                                                    'purchase_order.durasi_jt',
                                                    'purchase_invoice.*'
                                                )
                                                ->where([
                                                    ['purchase_invoice.id', '=', $id],
                                                ])
                                                ->first();

                $dataSupplier = SupplierDetail::where([
                                                    ['supplier_detail.id_supplier', '=', $dataPurchaseInvoice->id_supplier],
                                                    ['supplier_detail.default', '=' , 'Y']
                                                ])
                                                ->first();

                $detailSJ = PurchaseInvoiceDetail::leftJoin('receiving', 'purchase_invoice_detail.id_sj', '=', 'receiving.id')
                                                ->select(
                                                    'receiving.kode_penerimaan',
                                                    'receiving.tanggal_sj',
                                                    'receiving.no_sj_supplier'
                                                )
                                                ->where([
                                                    ['purchase_invoice_detail.id_invoice', '=', $id]
                                                ])
                                                ->get();

                $dataTerms = PurchaseInvoiceTerms::where('id_invoice', $id)->get();

                $dataPreference = Preference::leftJoin('company_account', 'preference.rekening', '=', 'company_account.id')
                                            ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                            ->select(
                                                'bank.kode_bank',
                                                'bank.nama_bank',
                                                'company_account.nomor_rekening',
                                                'company_account.cabang',
                                                'company_account.atas_nama',
                                                'preference.*'
                                            )
                                            ->where('flag_inv_purc', 'Y')
                                            ->first();
                $idPo = $dataPurchaseInvoice->id_po;
                $detailPurchaseInvoice = PurchaseInvoiceDetail::leftJoin('receiving_detail', 'purchase_invoice_detail.id_sj', '=', 'receiving_detail.id_penerimaan')
                                                        ->leftJoin('purchase_order_detail',function($qJoin) use ($idPo) {
                                                            $qJoin->on('receiving_detail.id_item', '=', 'purchase_order_detail.id_item')
                                                            ->where('purchase_order_detail.id_po', $idPo);
                                                        })
                                                        ->leftJoin('product', 'purchase_order_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'receiving_detail.id_satuan', 'product_unit.id')
                                                        ->select(
                                                            'receiving_detail.id',
                                                            'receiving_detail.id_item',
                                                            'receiving_detail.qty_item',
                                                            'purchase_order_detail.harga_beli',
                                                            DB::raw('COALESCE(purchase_order_detail.harga_beli,0) * COALESCE(receiving_detail.qty_item) AS subtotal'),
                                                            'product.kode_item',
                                                            'product.nama_item',
                                                            'product_unit.nama_satuan'
                                                            )
                                                        ->where([
                                                                ['purchase_invoice_detail.id_invoice', '=', $id]
                                                            ])
                                                        ->get();

                $shipDate = Receiving::select(
                                DB::raw('MAX(receiving.tanggal_sj) AS lastDate'), 'receiving.kode_penerimaan'
                            )
                            ->whereIn('receiving.id', function($subQuery) use ($id) {
                                $subQuery->select('id_sj')->from('purchase_invoice_detail')
                                ->where('id_invoice', $id);
                            })
                            ->first();

                $dataAlamat = SupplierDetail::find($dataPurchaseInvoice->id_alamat);
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['dataPurchaseInvoice'] = $dataPurchaseInvoice;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseInvoice'] = $detailPurchaseInvoice;
                $data['shipDate'] = $shipDate;
                $data['detailSJ'] = $detailSJ;
                $data['dataSupplier'] = $dataSupplier;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Purchase Invoice',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperPurchaseInvoice::cetakPdfInv($data);

                $fpdf->Output('I', strtoupper($dataPurchaseInvoice->kode_invoice).".pdf");
                exit;
            }
            else {
                return redirect('/PurchaseInvoice')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function exportDataPurchaseInvoice(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new PurchaseInvoiceExport($request), 'PurchaseInvoice_'.$kodeTgl.'.xlsx');
    }
}
