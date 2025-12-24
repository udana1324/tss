<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Sales\Delivery;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperExpeditionCost;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\Expedition;
use App\Models\Library\ExpeditionBranch;
use App\Models\Library\ExpeditionTarif;
use App\Models\Sales\DeliveryAllocation;
use App\Models\Sales\ExpeditionCost;
use App\Models\Sales\ExpeditionCostDetail;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class ExpeditionCostController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ExpeditionCost'],
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
                                                ['module.url', '=', '/ExpeditionCost'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = ExpeditionCost::distinct()->get('status_biaya');
                $dataEkspedisi = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                    ->select(
                                                        'expedition_branch.id',
                                                        'expedition_branch.nama_cabang'
                                                    )
                                                    ->where([
                                                        ['expedition.deleted_at', '=', null]
                                                    ])
                                                    ->get();

                $delete = DB::table('expedition_cost_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataEkspedisi'] = $dataEkspedisi;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Expedition Cost',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Expedition Cost',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.expedition_cost.index', $data);
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

        $expeditionCost = ExpeditionCost::leftJoin('expedition_branch', 'expedition_cost.id_cabang_ekspedisi', 'expedition_branch.id')
                            ->leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                            ->select(
                                'expedition_cost.*',
                                'expedition_branch.nama_cabang',
                                'expedition.nama_ekspedisi'
                            )
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('expedition_cost.tanggal_kirim', Carbon::parse($periode)->format('m'));
                                $q->whereYear('expedition_cost.tanggal_kirim', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('expedition_cost.id', 'desc')
                            ->get();
        return response()->json($expeditionCost);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ExpeditionCost'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCabang = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                ->select(
                                                    'expedition_branch.id',
                                                    'expedition_branch.nama_cabang'
                                                )
                                                ->where([
                                                    ['expedition.deleted_at', '=', null]
                                                ])
                                                ->get();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCabang'] = $dataCabang;

                $log = ActionLog::create([
                    'module' => 'Expedition Cost',
                    'action' => 'Buat',
                    'desc' => 'Buat Expedition Cost',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('expedition_cost_detail')
                            ->where([
                                ['id_cost', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.expedition_cost.add', $data);
            }
            else {
                return redirect('/ExpeditionCost')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ExpeditionCost'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCabang = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                ->select(
                                                    'expedition_branch.id',
                                                    'expedition_branch.nama_cabang'
                                                )
                                                ->where([
                                                    ['expedition.deleted_at', '=', null]
                                                ])
                                                ->get();

                $dataCost = ExpeditionCost::find($id);
                if ($dataCost->status_biaya != "draft") {
                    return redirect('/ExpeditionCost')->with('warning', 'Biaya Ekspedisi tidak dapat diubah karena status bukan DRAFT!');
                }

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'expedition_cost'],
                                    ['value1', '=', $id]
                                ])->delete();

                $dataDetail = ExpeditionCostDetail::where([
                                                    ['id_cost', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'expedition_cost',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_cost,
                            'value2' => $detail->id_sj,
                            'value3' => $detail->nama_resi,
                            'value4' => $detail->kota_tujuan,
                            'value5' => $detail->tarif,
                            'value6' => $detail->jumlah,
                            'value7' => $detail->berat,
                            'value8' => $detail->discount,
                            'value9' => $detail->flag_tagih,
                            'value10' => $detail->subtotal
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCabang'] = $dataCabang;
                $data['dataCost'] = $dataCost;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Expedition Cost',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Expedition Cost',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.expedition_cost.edit', $data);
            }
            else {
                return redirect('/ExpeditionCost')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/ExpeditionCost'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCost = ExpeditionCost::leftJoin('expedition_branch', 'expedition_cost.id_cabang_ekspedisi', 'expedition_branch.id')
                                    ->leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                    ->select(
                                        'expedition_cost.*',
                                        'expedition_branch.nama_cabang',
                                        'expedition_branch.alamat_cabang',
                                        'expedition.nama_ekspedisi'
                                    )
                                    ->where([
                                        ['expedition_cost.id', '=', $id],
                                    ])
                                    ->first();

                $taxSettings = TaxSettingsPPN::find($dataCost->id_ppn);

                $data['taxSettings'] = $taxSettings;

                $data['hakAkses'] = $hakAkses;
                $data['dataCost'] = $dataCost;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'ExpeditionCost',
                    'action' => 'Detail',
                    'desc' => 'Detail ExpeditionCost',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.expedition_cost.detail', $data);
            }
            else {
                return redirect('/ExpeditionCost')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetak($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/ExpeditionCost'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataExpeditionCost = ExpeditionCost::leftJoin('expedition_branch', 'expedition_cost.id_cabang_ekspedisi', 'expedition_branch.id')
                                                ->leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                ->select(
                                                    'expedition_cost.*',
                                                    'expedition_branch.nama_cabang',
                                                    'expedition.nama_ekspedisi'
                                                )
                                                ->where([
                                                    ['expedition_cost.id', '=', $id],
                                                ])
                                                ->first();

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
                                            ->where('flag_do', 'Y')
                                            ->first();

                $detailExpeditionCost = ExpeditionCostDetail::leftJoin('delivery', 'expedition_cost_detail.id_sj', '=', 'delivery.id')
                                                            ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                                            ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                                            ->select(
                                                                'expedition_cost_detail.id',
                                                                'expedition_cost_detail.tarif',
                                                                'expedition_cost_detail.jumlah',
                                                                'expedition_cost_detail.berat',
                                                                'expedition_cost_detail.subtotal',
                                                                'expedition_cost_detail.nama_resi',
                                                                'expedition_cost_detail.kota_tujuan',
                                                                'customer.nama_customer',
                                                                'customer_detail.nama_outlet',
                                                                'customer_detail.alamat_customer',
                                                                'customer_detail.kelurahan',
                                                                'customer_detail.kecamatan',
                                                                'customer_detail.kota',
                                                                'customer_detail.kode_pos',
                                                                'customer_detail.pic_alamat',
                                                                'customer_detail.telp_pic'
                                                            )
                                                            ->where([
                                                                    ['expedition_cost_detail.id_cost', '=', $id]
                                                                ])
                                                            ->get();


                $data['dataExpeditionCost'] = $dataExpeditionCost;
                $data['dataPreference'] = $dataPreference;
                $data['detailExpeditionCost'] = $detailExpeditionCost;

                $log = ActionLog::create([
                    'module' => 'ExpeditionCost',
                    'action' => 'Cetak',
                    'desc' => 'Cetak ExpeditionCost',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperExpeditionCost::cetakPdfCost($data);

                $fpdf->Output('I', strtoupper($dataExpeditionCost->no_biaya).".pdf");
                exit;
            }
            else {
                return redirect('/ExpeditionCost')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDelivery(Request $request)
    {
        $idBranch = $request->input('idBranch');

        $datadelivery = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                ->select(
                                    'delivery.id',
                                    'delivery.kode_pengiriman'
                                )
                                ->where([
                                    ['sales_order.metode_kirim', '=', 'ekspedisi'],
                                    ['sales_order.jenis_kirim', '=', $idBranch],
                                    ['delivery.status_pengiriman', '=', 'posted']
                                ])
                                ->whereNotIn('delivery.id', function($subQuery) {
                                    $subQuery->select('id_sj')->from('expedition_cost_detail')
                                    ->leftJoin('expedition_cost', 'expedition_cost_detail.id_cost', '=', 'expedition_cost.id')
                                    ->where([
                                        ['expedition_cost_detail.deleted_at', '=', null],
                                        ['expedition_cost.status_biaya', '=', 'posted']
                                    ]);
                                })
                                ->orderBy('id', 'asc')
                                ->get();

        return response()->json($datadelivery);
    }

    public function getExpeditionAddress(Request $request)
    {
        $idBranch = $request->input('idBranch');

        $alamat = ExpeditionBranch::find($idBranch);

        return response()->json($alamat);
    }

    public function getTarif(Request $request)
    {
        $idBranch = $request->input('idBranch');

        $expeditionBranch = ExpeditionBranch::find($idBranch);

        $dataTarif = Expedition::leftJoin('expedition_tarif', 'expedition_tarif.id_expedisi', '=', 'expedition.id')
                                    ->select(
                                        'expedition_tarif.nama_kota',
                                        'expedition_tarif.id'
                                    )
                                    ->where([
                                        ['expedition.id', '=', $expeditionBranch->id_expedisi],
                                    ])
                                    ->get();


        return response()->json($dataTarif);
    }

    public function getNominalTarif(Request $request)
    {
        $idTarif = $request->input('idTarif');

        $dataTarif = ExpeditionTarif::find($idTarif);

        return response()->json($dataTarif);
    }

    public function StoreCostDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idCost');
            $idDlv = $request->input('idDlv');
            $nama = $request->input('nama');
            $kota = $request->input('kota');
            $tarif = $request->input('tarif');
            $qty = $request->input('qty');
            $discount = $request->input('discount');
            $berat = $request->input('berat');
            $subtotal = $request->input('subtotal');
            $flagTagih = $request->input('flagTagih');
            $user = Auth::user()->user_name;

            if ($flagTagih == null || $flagTagih == "") {
                $flagTagih = 'N';
            }

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = ExpeditionCostDetail::select(DB::raw("COUNT(*) AS angka"))
                                                ->where([
                                                    ['id_cost', '=' , $id],
                                                    ['id_sj', '=', $idDlv]
                                                ])
                                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new ExpeditionCostDetail();
                    $listItem->id_cost = $id;
                    $listItem->id_sj = $idDlv;
                    $listItem->nama_resi = $nama;
                    $listItem->kota_tujuan = $kota;
                    $listItem->tarif = $tarif;
                    $listItem->jumlah = $qty;
                    $listItem->discount = $discount;
                    $listItem->berat = $berat;
                    $listItem->subtotal = $subtotal;
                    $listItem->flag_tagih = $flagTagih;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Expedition Cost Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Expedition Cost Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {

                // 'id_detail' => $detail->id,
                // 'value1' => $detail->id_cost,
                // 'value2' => $detail->id_sj,
                // 'value3' => $detail->nama_resi,
                // 'value4' => $detail->kota_tujuan,
                // 'value5' => $detail->tarif,
                // 'value6' => $detail->jumlah,
                // 'value7' => $detail->berat,
                // 'value8' => $detail->flag_tagih,
                // 'value9' => $detail->subtotal

                $countItem = TempTransaction::select(DB::raw("COUNT(*) AS angka"))
                                            ->where([
                                                ['module', '=', 'expedition_cost'],
                                                ['value1', '=' , $id],
                                                ['value2', '=', $idDlv]
                                            ])
                                            ->first();

                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'expedition_cost';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idDlv;
                    $listItem->value3 = $nama;
                    $listItem->value4 = $kota;
                    $listItem->value5 = $tarif;
                    $listItem->value6 = $qty;
                    $listItem->value7 = $berat;
                    $listItem->value8 = $discount;
                    $listItem->value9 = $flagTagih;
                    $listItem->value10 = $subtotal;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Expedition Cost Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Expedition Cost Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetCostDetail(Request $request)
    {
        $id = $request->input('idCost');
        $mode = $request->input('mode');

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = ExpeditionCostDetail::leftJoin('delivery', 'expedition_cost_detail.id_sj', '=', 'delivery.id')
                                        ->leftJoin('sales_order', 'delivery.id_so', 'sales_order.id')
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                            'customer.nama_customer',
                                            'expedition_cost_detail.id',
                                            'expedition_cost_detail.id_sj',
                                            'expedition_cost_detail.nama_resi',
                                            'expedition_cost_detail.kota_tujuan',
                                            'expedition_cost_detail.tarif',
                                            'expedition_cost_detail.jumlah',
                                            'expedition_cost_detail.berat',
                                            'expedition_cost_detail.discount',
                                            'expedition_cost_detail.subtotal',
                                            'expedition_cost_detail.flag_tagih',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_sj'
                                        )
                                        ->where([
                                            ['expedition_cost_detail.id_cost', '=', $id],
                                        ])
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('delivery', 'temp_transaction.value1', '=', 'delivery.id')
                                        ->leftJoin('sales_order', 'delivery.id_so', 'sales_order.id')
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                            'customer.nama_customer',
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            'temp_transaction.value9',
                                            'temp_transaction.value10',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_sj'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'expedition_cost']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function EditCostDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = ExpeditionCostDetail::leftJoin('delivery', 'expedition_cost_detail.id_sj', '=', 'delivery.id')
                                        ->select(
                                            'expedition_cost_detail.*',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_kirim',
                                            'delivery.tanggal_sj',
                                            'jumlah_total_sj'
                                        )
                                        ->where([
                                            ['expedition_cost_detail.id', '=', $id]
                                        ])
                                        ->get();
        }
        else {

            $detail = TempTransaction::leftJoin('delivery', 'temp_transaction.value2', '=', 'delivery.id')
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'temp_transaction.value5',
                                            'temp_transaction.value6',
                                            'temp_transaction.value7',
                                            'temp_transaction.value8',
                                            'temp_transaction.value9',
                                            'temp_transaction.value10',
                                            'delivery.kode_pengiriman',
                                            'delivery.tanggal_kirim',
                                            'delivery.tanggal_sj',
                                            'jumlah_total_sj'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'expedition_cost']
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function UpdateCostDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $idDetail = $request->input('idDetail');
            $id = $request->input('idCost');
            $idDlv = $request->input('idDlv');
            $nama = $request->input('nama');
            $kota = $request->input('kota');
            $tarif = $request->input('tarif');
            $qty = $request->input('qty');
            $berat = $request->input('berat');
            $discount = $request->input('discount');
            $subtotal = $request->input('subtotal');
            $flagTagih = $request->input('flagTagih');
            $user = Auth::user()->user_name;

            if ($flagTagih == null || $flagTagih == "") {
                $flagTagih = 'N';
            }

            if ($id == "") {
                $id = 'DRAFT';
                $listItem = ExpeditionCostDetail::find($idDetail);
                $listItem->nama_resi = $nama;
                $listItem->kota_tujuan = $kota;
                $listItem->tarif = $tarif;
                $listItem->jumlah = $qty;
                $listItem->berat = $berat;
                $listItem->discount = $discount;
                $listItem->subtotal = $subtotal;
                $listItem->flag_tagih = $flagTagih;
                $listItem->updated_by = $user;
                $listItem->save();
            }
            else {

                $listItem = TempTransaction::find($idDetail);
                $listItem->value3 = $nama;
                $listItem->value4 = $kota;
                $listItem->value5 = $tarif;
                $listItem->value6 = $qty;
                $listItem->value7 = $berat;
                $listItem->value8 = $discount;
                $listItem->value9 = $flagTagih;
                $listItem->value10 = $subtotal;
                $listItem->action = 'update';
                $listItem->save();
            }

            $log = ActionLog::create([
                'module' => 'Expedition Cost Detail',
                'action' => 'Update',
                'desc' => 'Update Expedition Cost Detail',
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

    public function GetDeliveryDetail(Request $request)
    {
        $idSj = $request->input('idDelivery');

        $alokasiDlv = DeliveryAllocation::leftJoin('product', 'delivery_allocation.id_item', '=', 'product.id')
                                        ->select(
                                            'delivery_allocation.id_delivery',
                                            DB::raw("SUM(delivery_allocation.qty_item / delivery_allocation.qty_dus) AS koli")
                                        )
                                        ->groupBy('delivery_allocation.id_delivery');

        $detail = Delivery::leftJoin('customer_detail', 'delivery.id_alamat', 'customer_detail.id')
                            ->leftJoinSub($alokasiDlv, 'alokasiDlv', function($alokasiDlv) {
                                $alokasiDlv->on('delivery.id', '=', 'alokasiDlv.id_delivery');
                            })
                            ->select(
                                'delivery.*',
                                'customer_detail.kota',
                                'alokasiDlv.koli'
                            )
                            ->where([
                                ['delivery.id', '=', $idSj],
                            ])
                            ->get();

        return response()->json($detail);
    }

    public function DeleteCostDetail(Request $request)
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
                $delete = DB::table('expedition_cost_detail')->where('id', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetCostFooter(Request $request)
    {
        $id = $request->input('idCost');
        $mode = $request->input('mode');

        if($mode != "edit") {

            $detail = ExpeditionCostDetail::select(
                                                DB::raw('COALESCE(SUM(expedition_cost_detail.jumlah),0) AS qtyCost'),
                                                DB::raw('COALESCE(SUM(expedition_cost_detail.berat),0) AS beratCost'),
                                                DB::raw('COALESCE(SUM(expedition_cost_detail.subtotal),0) AS subtotalCost')
                                            )
                                            ->where([
                                                ['expedition_cost_detail.id_cost', '=', $id]
                                            ])
                                            ->groupBy('expedition_cost_detail.id_cost')
                                            ->first();
        }
        else {
            $detail = TempTransaction::select(
                                            DB::raw('COALESCE(SUM(temp_transaction.value6),0) AS qtyCost'),
                                            DB::raw('COALESCE(SUM(temp_transaction.value7),0) AS beratCost'),
                                            DB::raw('COALESCE(SUM(temp_transaction.value10),0) AS subtotalCost')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'expedition_cost']
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

    public function RestoreCostDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idCost');
            $restore = ExpeditionCostDetail::onlyTrashed()->where([['id_cost', '=', $id]]);
            $restore->restore();

        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch'=>'required',
            'tanggal'=>'required',
        ]);

        $tgl = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tgl)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tgl);
        if (!$aksesTransaksi) {
            return redirect('/ExpeditionCost')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idBranch = $request->input('branch');
            $noResi = $request->input('resi');
            $tglKirim = $request->input('tanggal');
            $ttlJml = $request->input('ttlQty');
            $ttlBerat = $request->input('ttlBerat');
            $totalBiaya = $request->input('ttlBiaya');
            $user = Auth::user()->user_name;


            $blnPeriode = date("m", strtotime($tglKirim));
            $thnPeriode = date("Y", strtotime($tglKirim));
            $tahunPeriode = date("y", strtotime($tglKirim));

            $countKode = DB::table('expedition_cost')
                        ->select(DB::raw("MAX(RIGHT(no_biaya,2)) AS angka"))
                        //->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tglKirim)
                        ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglKirim)->format('ymd');

            if ($counter < 10) {
                $noBiaya = "exc-cv-".$kodeTgl."0".$counter;
            }
            else {
                $noBiaya = "exc-cv-".$kodeTgl.$counter;
            }

            $cost = new ExpeditionCost();
            $cost->no_biaya = $noBiaya;
            $cost->no_resi = $noResi;
            $cost->id_cabang_ekspedisi = $idBranch;
            $cost->tanggal_kirim = $tglKirim;
            $cost->total_jumlah = $ttlJml;
            $cost->total_berat = $ttlBerat;
            $cost->total_biaya = $totalBiaya;
            $cost->status_biaya = 'draft';
            $cost->id_ppn = $taxSettings->ppn_percentage_id;
            $cost->created_by = $user;
            $cost->save();

            $data = $cost;

            $setDetail = DB::table('expedition_cost_detail')
                            ->where([
                                        ['id_cost', '=', 'DRAFT'],
                                        ['created_by', '=', $user]
                                    ])
                            ->update([
                                'id_cost' => $cost->id,
                                'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Expedition Cost',
                'action' => 'Simpan',
                'desc' => 'Simpan Expedition Cost',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ExpeditionCost.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_biaya).' Telah Disimpan!');
        }
        else {
            return redirect('/ExpeditionCost')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch'=>'required',
            'tanggal'=>'required',
        ]);

        $tglInv = $request->input('tanggal');

        $bulanIndonesia = Carbon::parse($tglInv)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglInv);
        if (!$aksesTransaksi) {
            return redirect()->route('ExpeditionCost.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idBranch = $request->input('branch');
            $noResi = $request->input('resi');
            $tglKirim = $request->input('tanggal');
            $ttlJml = $request->input('ttlQty');
            $ttlBerat = $request->input('ttlBerat');
            $totalBiaya = $request->input('ttlBiaya');
            $user = Auth::user()->user_name;

            $cost = ExpeditionCost::find($id);
            $cost->no_resi = $noResi;
            $cost->id_cabang_ekspedisi = $idBranch;
            $cost->tanggal_kirim = $tglKirim;
            $cost->total_jumlah = $ttlJml;
            $cost->total_berat = $ttlBerat;
            $cost->total_biaya = $totalBiaya;
            $cost->id_ppn = $taxSettings->ppn_percentage_id;
            $cost->created_by = $user;
            $cost->save();

            $data = $cost;


            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'expedition_cost'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();

            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = ExpeditionCostDetail::find($detail->id_detail);
                        $listItem->id_cost = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->nama_resi = $detail->value3;
                        $listItem->kota_tujuan = $detail->value4;
                        $listItem->tarif = $detail->value5;
                        $listItem->jumlah = $detail->value6;
                        $listItem->berat = $detail->value7;
                        $listItem->discount = $detail->value8;
                        $listItem->flag_tagih = $detail->value9;
                        $listItem->subtotal = $detail->value10;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new ExpeditionCostDetail();
                        $listItem->id_cost = $detail->value1;
                        $listItem->id_sj = $detail->value2;
                        $listItem->nama_resi = $detail->value3;
                        $listItem->kota_tujuan = $detail->value4;
                        $listItem->tarif = $detail->value5;
                        $listItem->jumlah = $detail->value6;
                        $listItem->berat = $detail->value7;
                        $listItem->discount = $detail->value8;
                        $listItem->flag_tagih = $detail->value9;
                        $listItem->subtotal = $detail->value10;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('expedition_cost_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'expedition_cost'],
                                    ['value1', '=', $id]
                                ])->delete();

            $log = ActionLog::create([
                'module' => 'Expedition Cost',
                'action' => 'Update',
                'desc' => 'Update Expedition Cost',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('ExpeditionCost.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->no_biaya).' Telah Diubah!');
        }
        else {
            return redirect('/ExpeditionCost')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id) {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $cost = ExpeditionCost::find($id);

            if ($btnAction == "posting") {

                $cekSjPosted = Delivery::select('kode_pengiriman')
                ->where([
                    ['delivery.status_pengiriman', '!=', 'posted']
                ])
                ->whereIn('delivery.id', function($subQuery) use ($id) {
                    $subQuery->select('id_sj')->from('expedition_cost_detail')
                    ->where('id_cost', $id);
                })
                ->get();

                if (count($cekSjPosted) != 0) {
                    $msg = 'Biaya Ekspedisi '.strtoupper($cost->no_biaya).' Tidak dapat Diposting karena terdapat surat jalan SJ ('.strtoupper($cekSjPosted->pluck('kode_pengiriman')->implode(', ')).') yang belum diposting!';
                    $status = "warning";
                }
                else {

                    $cost->status_biaya = "posted";
                    $cost->save();

                    $log = ActionLog::create([
                        'module' => 'Expedition Cost',
                        'action' => 'Posting',
                        'desc' => 'Posting Expedition Cost',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Data '.strtoupper($cost->no_biaya).' Telah Diposting!';
                    $status = 'success';
                }
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($cost->status_biaya == "posted") {
                    $cost->status_biaya = "draft";
                    $cost->flag_revisi = '1';
                    $cost->updated_by = Auth::user()->user_name;
                    $cost->save();

                    $log = ActionLog::create([
                        'module' => 'Expedition Cost',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Expedition Cost',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Biaya Ekspedisi '.strtoupper($cost->no_biaya).' Telah Direvisi!';
                    $status = "success";

                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('ExpeditionCost.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetExpeditionCostDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idCost');


            if ($id != "DRAFT") {

                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'expedition_cost'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('expedition_cost_detail')->where('id_cost', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function InputResi(Request $request)
    {
        $data = new stdClass();
        $resi = $request->input('resi');
        if ($resi == "" || $resi == null) {
            return response()->json("false");
        }

        $exception = DB::transaction(function () use ($request, &$data, $resi) {
            $id = $request->input('idCost');


            $dataCost = ExpeditionCost::find($id);
            $dataCost->no_resi = $resi;
            $dataCost->updated_by = Auth::user()->user_name;
            $dataCost->save();

            $data = $dataCost;
        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }
}
