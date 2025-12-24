<?php

namespace App\Http\Controllers\Library;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Library\Expedition;
use App\Models\Library\ExpeditionBranch;
use App\Models\ActionLog;
use App\Models\Library\ExpeditionTarif;
use App\Models\Setting\Module;

class ExpeditionController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Expedition'],
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
                                                ['module.url', '=', '/Expedition'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Expedition',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ekspedisi',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.library.expedition.index', $data);
            }
            else {
                return redirect('/Bank')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataExpedition = Expedition::all();

        return response()->json($dataExpedition);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                            ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                            ->select('*')
                            ->where([
                                        ['module.url', '=', '/Expedition'],
                                        ['module_access.user_id', '=', Auth::user()->id]
                                ])
                            ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $delete = ExpeditionBranch::where('id_expedisi', '=', 'draft')->delete();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.expedition.add');
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function StoreBranchExpedition(Request $request)
    {
        $id = $request->input('idAlamat');
        $idExpedition = $request->input('idExpedition');
        $alamat = $request->input('alamat');
        $namaCabang = $request->input('nama');
        $kota = $request->input('kota');
        $telpCabang = $request->input('telpCabang');
        $user = Auth::user()->user_name;


        if ($idExpedition == "") {
            $idExpedition = 'DRAFT';
            $userAct = "created_by";
        }
        else {
            $userAct = "updated_by";
        }

        $cekNama = DB::table('expedition_branch')->select(DB::raw("COUNT(*) AS angka"))->where([['id_expedisi', '=' , $idExpedition], ['nama_cabang', '=', $namaCabang]])->first();
        $cek = $cekNama->angka;

        $countDef = DB::table('expedition_branch')->select(DB::raw("COUNT(*) AS angkaDef"))->where([['id_expedisi', '=' , $idExpedition], ['default', '=', 'Y']])->first();
        $countDefault = $countDef->angkaDef;

        if ($countDefault > 0) {
            $flagDef = 'N';
        }
        else {
            $flagDef = 'Y';
        }

        if ($id != "") {
            $getFlag = DB::table('expedition_branch')->select('default')->where([['id', '=' , $id]])->first();
            $flagDef = $getFlag->default;
        }

        if ($cek > 0 && $id == "") {
            return response()->json("failNama");
        }
        else {
            ExpeditionBranch::updateOrCreate(
                ['id' => $id],
                [
                    'id_expedisi' => $idExpedition,
                    'nama_cabang' => $namaCabang,
                    'alamat_cabang' => $alamat,
                    'kota_cabang' => $kota,
                    'telp_cabang' => $telpCabang,
                    'default' => $flagDef,
                    $userAct => $user
                ]
            );
            return response()->json("success");
        }

        $log = ActionLog::create([
            'module' => 'Expedition',
            'action' => 'Simpan Alamat',
            'desc' => 'Simpan Alamat Ekspedisi',
            'username' => Auth::user()->user_name
        ]);
    }

    public function EditBranchExpedition(Request $request)
    {
        $idExpedition = $request->input('idBranch');


        $expeditionBranch = ExpeditionBranch::find($idExpedition);

        return response()->json($expeditionBranch);
    }

    public function GetBranchData(Request $request)
    {
        $idExpedition = $request->input('idExpedition');
        if ($idExpedition == "") {
            $idExpedition = 'DRAFT';
        }

        $expeditionBranch = ExpeditionBranch::where([
                                 ['id_expedisi', '=', $idExpedition]
                                ])
                        ->get();

        return response()->json($expeditionBranch);
    }

    public function SetDefaultBranch(Request $request)
    {
        $id = $request->input('idBranch');
        $idExpedition = $request->input('idExpedition');

        if ($idExpedition == "") {
            $idExpedition = 'DRAFT';
        }

        $updateFlagAlamt = $update = DB::table('expedition_branch')
                                ->where('id_expedisi', $idExpedition)
                                ->update([
                                    'default' => 'N'
                                ]);

        $setFlagAlamt = $update = DB::table('expedition_branch')
                                ->where('id', $id)
                                ->update([
                                    'default' => 'Y'
                                ]);

        $log = ActionLog::create([
                    'module' => 'Expedition',
                    'action' => 'Simpan Alamat Default',
                    'desc' => 'Simpan Alamat Default Ekspedisi',
                    'username' => Auth::user()->user_name
                ]);

        return response()->json("success");
    }

    public function DeleteBranchData(Request $request)
    {
        $idExpedition = $request->input('idBranch');
        $data = ExpeditionBranch::find($idExpedition);
        $data->delete();

        return response()->json($data);
    }

    public function StoreTarifExpedition(Request $request)
    {
        $id = $request->input('idTarif');
        $idExpedition = $request->input('idExpedition');
        $kota = $request->input('namaKota');
        $tarif = $request->input('tarif');
        $user = Auth::user()->user_name;


        if ($idExpedition == "") {
            $idExpedition = 'DRAFT';
            $userAct = "created_by";
        }
        else {
            $userAct = "updated_by";
        }

        $cekNama = DB::table('expedition_tarif')->select(DB::raw("COUNT(*) AS angka"))->where([['id_expedisi', '=' , $idExpedition], ['nama_kota', '=', $kota]])->first();
        $cek = $cekNama->angka;

        if ($cek > 0 && $id == "") {
            return response()->json("failNama");
        }
        else {
            ExpeditionTarif::updateOrCreate(
                ['id' => $id],
                [
                    'id_expedisi' => $idExpedition,
                    'nama_kota' => $kota,
                    'tarif' => $tarif,
                    $userAct => $user
                ]
            );
            return response()->json("success");
        }

        $log = ActionLog::create([
            'module' => 'Expedition',
            'action' => 'Simpan Alamat',
            'desc' => 'Simpan Alamat Ekspedisi',
            'username' => Auth::user()->user_name
        ]);
    }

    public function EditTarifExpedition(Request $request)
    {
        $idExpedition = $request->input('idTarif');


        $expeditionBranch = ExpeditionTarif::find($idExpedition);

        return response()->json($expeditionBranch);
    }

    public function GetTarifData(Request $request)
    {
        $idExpedition = $request->input('idExpedition');
        if ($idExpedition == "") {
            $idExpedition = 'DRAFT';
        }

        $expeditionBranch = ExpeditionTarif::where([
                                 ['id_expedisi', '=', $idExpedition]
                                ])
                        ->get();

        return response()->json($expeditionBranch);
    }

    public function DeleteTarifData(Request $request)
    {
        $idExpedition = $request->input('idTarif');
        $data = ExpeditionTarif::find($idExpedition);
        $data->delete();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekspedisi' => 'required',
            'nama_perusahaan' => 'required'

        ]);

        $namaEkspedisi = strtolower($request->input('nama_ekspedisi'));
        $namaPerusahaan = $request->input('nama_perusahaan');
        $telpEkspedisi = $request->input('telp_perusahaan');
        $user = Auth::user()->user_name;

        $expedition = Expedition::firstOrCreate(
            ['nama_ekspedisi' => $namaEkspedisi],
            [
                'nama_perusahaan' => $namaPerusahaan,
                'telp_perusahaan' => $telpEkspedisi,
                'created_by' => $user
            ]
        );

        $setAlamat = DB::table('expedition_branch')
                         ->where([
                                    ['id_expedisi', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_expedisi' => $expedition->id
                        ]);

        $setTarif = DB::table('expedition_tarif')
                         ->where([
                                    ['id_expedisi', '=', 'DRAFT'],
                                    ['created_by', '=', $user]
                                ])
                         ->update([
                            'id_expedisi' => $expedition->id
                        ]);

        $log = ActionLog::create([
                    'module' => 'Expedition',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Ekspedisi',
                    'username' => Auth::user()->user_name
                ]);

        if ($expedition->wasRecentlyCreated) {
            return redirect('/Expedition')->with('success', 'Data '.strtoupper($namaEkspedisi).' Telah Disimpan!');
        }
        else {
            return redirect('/Expedition')->with('danger', 'Data '.strtoupper($namaEkspedisi).' Telah Tersedia!');
        }
    }

    public function edit($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                            ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                            ->select('*')
                            ->where([
                                        ['module.url', '=', '/Expedition'],
                                        ['module_access.user_id', '=', Auth::user()->id]
                                ])
                            ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {

                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataExpedition = Expedition::find($id);

                $log = ActionLog::create([
                    'module' => 'Expedition',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Ekspedisi',
                    'username' => Auth::user()->user_name
                ]);

                $data['dataExpedition'] = $dataExpedition;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.expedition.edit', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function update(Request $request, $id )
    {
        $request->validate([
            'nama_ekspedisi' => 'required',
            'nama_perusahaan' => 'required'

        ]);

        $namaEkspedisi = strtolower($request->input('nama_ekspedisi'));
        $namaPerusahaan = $request->input('nama_perusahaan');
        $telpEkspedisi = $request->input('telp_perusahaan');
        $user = Auth::user()->user_name;

        $ekspedisi = Expedition::find($id);

        $ekspedisi->nama_ekspedisi = $namaEkspedisi;
        $ekspedisi->nama_perusahaan = $namaPerusahaan;
        $ekspedisi->telp_perusahaan = $telpEkspedisi;
        $ekspedisi->updated_by = $user;
        $ekspedisi->save();

        $log = ActionLog::create([
            'module' => 'Expedition',
            'action' => 'Update',
            'desc' => 'Update Ekspedisi',
            'username' => Auth::user()->user_name
        ]);

        return redirect('/Expedition')->with('success', 'Data '.strtoupper($namaEkspedisi).' Telah Diupdate!');
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Expedition'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                            ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                            ->select('*')
                            ->where([
                                        ['module.url', '=', '/Expedition'],
                                        ['module_access.user_id', '=', Auth::user()->id]
                                ])
                            ->first();
                $data = array();

                $dataExpedition = Expedition::find($id);

                $log = ActionLog::create([
                    'module' => 'Expedition',
                    'action' => 'Detail',
                    'desc' => 'Detail Ekspedisi',
                    'username' => Auth::user()->user_name
                ]);

                $data['dataExpedition'] = $dataExpedition;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                return view('pages.library.expedition.detail', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function delete(Request $request)
    {

      	$id = $request->input('id_expedition');
      	$user = Auth::user()->user_name;
        $data = Expedition::find($id);
        $data->deleted_by = $user;
        $data->save();
        $data->delete();

        $log = ActionLog::create([
            'module' => 'Expedition',
            'action' => 'Delete',
            'desc' => 'Delete Expedisi',
            'username' => Auth::user()->user_name
        ]);

        if ($data) {
            $request->session()->flash('delet', 'Data Berhasil dihapus!');
            return response()->json();
        }
        else {
            $request->session()->flash('delet', 'Data Gagal dihapus!');
            return response()->json();
        }
    }
}
