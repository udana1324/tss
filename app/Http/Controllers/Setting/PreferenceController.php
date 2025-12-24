<?php

namespace App\Http\Controllers\Setting;

use App\Classes\BusinessManagement\SetMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Preference;
use App\Models\Library\Bank;
use App\Models\Library\CompanyAccount;
use App\Models\ActionLog;
use App\Models\Setting\Module;

class PreferenceController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
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
                                                ['module.url', '=', '/Preference'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Preference',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Preference',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.setting.preference.index', $data);
            }
            else {
                return redirect('/Preference')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndex()
    {
        $dataRekening = CompanyAccount::withTrashed()->join('bank', 'company_account.bank', '=', 'bank.id')
                        ->select('company_account.id', 'company_account.nomor_rekening', 'company_account.atas_nama', 'bank.nama_bank');

        $dataPref = Preference::leftJoinSub($dataRekening, 'dataRekening', function($dataRekening) {
                                    $dataRekening->on('preference.rekening', '=', 'dataRekening.id');
                                })
                                ->select(
                                    'preference.*',
                                    DB::raw("dataRekening.id As idRekening"),
                                    'dataRekening.nomor_rekening',
                                    'dataRekening.atas_nama',
                                    'dataRekening.nama_bank',
                                    DB::raw("CONCAT(dataRekening.nama_bank, ' - ', dataRekening.nomor_rekening) As txtRekening"),
                                )
                                ->get();
        return response()->json($dataPref);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'bank.nama_bank',
                                                    'company_account.*'
                                                )
                                                ->get();
                $data['CompanyAccount'] = $CompanyAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Preference',
                    'action' => 'Buat',
                    'desc' => 'Buat Preference Baru',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.setting.preference.add', $data);
            }
            else {
                return redirect('/Preference')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function edit($id){
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                $dataPreferensi = Preference::find($id);
                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'bank.nama_bank',
                                                    'company_account.*'
                                                )
                                                ->get();

                $data['Preference'] = $dataPreferensi;
                $data['CompanyAccount'] = $CompanyAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Preference',
                    'action' => 'Edit',
                    'desc' => 'Ubah Preference',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.setting.preference.edit', $data);
            }
            else {
                return redirect('/Preference')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id){
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            if ($countAkses > 0) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();
                $data = array();
                $dataPreferensi = Preference::find($id);
                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'bank.nama_bank',
                                                    'company_account.*'
                                                )
                                                ->get();

                $data['Preference'] = $dataPreferensi;
                $data['CompanyAccount'] = $CompanyAccount;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Preference',
                    'action' => 'Detail',
                    'desc' => 'Detail Preference',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.setting.preference.detail', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
    	$metode = $request->input('metode');
    	if ($metode == "store") {
    		$request->validate([
                'nama_pt'=>'required',
                'telp'=>'required',
                'email'=>'required',
                'npwp1'=>'required',
                'npwp2'=>'required',
                'npwp3'=>'required',
                'npwp4'=>'required',
                'npwp5'=>'required',
                'npwp6'=>'required',
                'alamat' => 'required',
                'kecamatan' => 'required',
                'kota' => 'required'
            ]);

            $nama = strtolower($request->input('nama_pt'));
            $telp = $request->input('telp');
            $email = $request->input('email');
            $npwpP1 = $request->input('npwp1');
            $npwpP2 = $request->input('npwp2');
            $npwpP3 = $request->input('npwp3');
            $npwpP4 = $request->input('npwp4');
            $npwpP5 = $request->input('npwp5');
            $npwpP6 = $request->input('npwp6');
            $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
            $npwp16P1 = $request->input('npwp16_cust1');
            $npwp16P2 = $request->input('npwp16_cust2');
            $npwp16P3 = $request->input('npwp16_cust3');
            $npwp16P4 = $request->input('npwp16_cust4');
            $npwp16 = $npwp16P1.".".$npwp16P2.".".$npwp16P3.".".$npwp16P4;
            $rekening = $request->input('rekening');
            $flag_default = $request->input('flagDefault');
            $flag_quo = $request->input('flagQUO');
			$flag_do = $request->input('flagDO');
			$flag_rcv = $request->input('flagRCV');
			$flag_so = $request->input('flagSO');
            $flag_po = $request->input('flagPO');
            $flag_invdp = $request->input('flagDP');
			$flag_invsale = $request->input('flagINVS');
			$flag_invpurc = $request->input('flagINVP');
			$alamat = $request->input('alamat');
			$kelurahan = $request->input('kelurahan');
			$kecamatan = $request->input('kecamatan');
			$kota = $request->input('kota');
			$website = $request->input('web');
            $user = Auth::user()->user_name;

            if ($flag_default == "Y") {
            	$update = DB::table('preference')->update(array('flag_default' => 'N'));
            }

            if ($flag_quo == "Y") {
            	$update = DB::table('preference')->update(array('flag_quo' => 'N'));
            }

            if ($flag_so == "Y") {
            	$update = DB::table('preference')->update(array('flag_so' => 'N'));
            }

            if ($flag_do == "Y") {
            	$update = DB::table('preference')->update(array('flag_do' => 'N'));
            }

            if ($flag_invdp == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_dp' => 'N'));
            }

            if ($flag_invsale == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_sale' => 'N'));
            }

            if ($flag_po == "Y") {
            	$update = DB::table('preference')->update(array('flag_po' => 'N'));
            }

            if ($flag_rcv == "Y") {
            	$update = DB::table('preference')->update(array('flag_rcv' => 'N'));
            }

            if ($flag_invpurc == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_purc' => 'N'));
            }



            $Pref = Preference::firstOrCreate(
                ['alamat_pt' => $alamat],
                [
                    'nama_pt' => $nama,
                    'kelurahan_pt' => $kelurahan,
                    'kecamatan_pt' => $kecamatan,
                    'kota_pt' => $kota,
                    'npwp_pt' => $npwp,
                    'npwp_pt_16' => $npwp16,
                    'rekening' => $rekening,
                    'telp_pt' => $telp,
                    'email_pt' => $email,
                    'website_pt' => $website,
                    'flag_default' => $flag_default,
                    'flag_quo' => $flag_quo,
                    'flag_do' => $flag_do,
                    'flag_rcv' => $flag_rcv,
                    'flag_so' => $flag_so,
                    'flag_po' => $flag_po,
                    'flag_inv_dp' => $flag_invdp,
                    'flag_inv_sale' => $flag_invsale,
                    'flag_inv_purc' => $flag_invpurc,
                    'created_by' => $user
                ]
            );

            $log = ActionLog::create([
                'module' => 'Preference',
                'action' => 'Tambah',
                'desc' => 'Tambah Preference',
                'username' => Auth::user()->user_name
            ]);

            if ($Pref->wasRecentlyCreated) {
                return redirect('Preference')->with('success', 'Data Preferensi Telah Disimpan!');
            }
            else {
                return redirect('Preference')->with('error', 'Data Tidak berhasil disimpan!');
            }
    	}
    	else {
    		$request->validate([
                'nama_pt'=>'required',
                'telp'=>'required',
                'email'=>'required',
                'npwp1'=>'required',
                'npwp2'=>'required',
                'npwp3'=>'required',
                'npwp4'=>'required',
                'npwp5'=>'required',
                'npwp6'=>'required',
                'alamat' => 'required',
                'kecamatan' => 'required',
                'kota' => 'required'
            ]);

    		$id = $request->input('idData');
            $nama = strtolower($request->input('nama_pt'));
            $telp = $request->input('telp');
            $email = $request->input('email');
            $npwpP1 = $request->input('npwp1');
            $npwpP2 = $request->input('npwp2');
            $npwpP3 = $request->input('npwp3');
            $npwpP4 = $request->input('npwp4');
            $npwpP5 = $request->input('npwp5');
            $npwpP6 = $request->input('npwp6');
            $npwp = $npwpP1.".".$npwpP2.".".$npwpP3.".".$npwpP4."-".$npwpP5.".".$npwpP6;
            $rekening = $request->input('rekening');
            $flag_default = $request->input('flagDefault');
			$flag_quo = $request->input('flagQUO');
			$flag_do = $request->input('flagDO');
			$flag_rcv = $request->input('flagRCV');
			$flag_so = $request->input('flagSO');
            $flag_po = $request->input('flagPO');
            $flag_invdp = $request->input('flagDP');
			$flag_invsale = $request->input('flagINVS');
			$flag_invpurc = $request->input('flagINVP');
			$alamat = $request->input('alamat');
			$kelurahan = $request->input('kelurahan');
			$kecamatan = $request->input('kecamatan');
			$kota = $request->input('kota');
			$website = $request->input('web');
            $user = Auth::user()->user_name;

            if ($flag_default == "Y") {
            	$update = DB::table('preference')->update(array('flag_default' => 'N'));
            }

            if ($flag_quo == "Y") {
            	$update = DB::table('preference')->update(array('flag_quo' => 'N'));
            }

            if ($flag_so == "Y") {
            	$update = DB::table('preference')->update(array('flag_so' => 'N'));
            }

            if ($flag_do == "Y") {
            	$update = DB::table('preference')->update(array('flag_do' => 'N'));
            }

            if ($flag_invdp == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_dp' => 'N'));
            }

            if ($flag_invsale == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_sale' => 'N'));
            }

            if ($flag_po == "Y") {
            	$update = DB::table('preference')->update(array('flag_po' => 'N'));
            }

            if ($flag_rcv == "Y") {
            	$update = DB::table('preference')->update(array('flag_rcv' => 'N'));
            }

            if ($flag_invpurc == "Y") {
            	$update = DB::table('preference')->update(array('flag_inv_purc' => 'N'));
            }

            $pref = DB::table('preference')
                            ->where('id', $id)
                            ->update([
				                    'nama_pt' => $nama,
				                    'alamat_pt' => $alamat,
				                    'kelurahan_pt' => $kelurahan,
				                    'kecamatan_pt' => $kecamatan,
				                    'kota_pt' => $kota,
				                    'npwp_pt' => $npwp,
				                    'rekening' => $rekening,
				                    'telp_pt' => $telp,
				                    'email_pt' => $email,
                                    'website_pt' => $website,
                                    'flag_default' => $flag_default,
                                    'flag_quo' => $flag_quo,
				                    'flag_do' => $flag_do,
				                    'flag_rcv' => $flag_rcv,
				                    'flag_so' => $flag_so,
                                    'flag_po' => $flag_po,
                                    'flag_inv_dp' => $flag_invdp,
				                    'flag_inv_sale' => $flag_invsale,
				                    'flag_inv_purc' => $flag_invpurc,
				                    'updated_by' => $user
                            ]);

            $log = ActionLog::create([
                'module' => 'Preference',
                'action' => 'Ubah',
                'desc' => 'Ubah Preference',
                'username' => Auth::user()->user_name
            ]);

            return redirect('Preference')->with('success', 'Data Preferensi Telah Diupdate!');
    	}

    }

    public function delete(Request $request)
    {
        if (Auth::check()) {
            $id = $request->input('id');
            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Preference'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();
            if ($hakAkses->delete == "Y") {
                $data = Preference::find($id);
                $menu = DB::table('preference')
                            ->where('id', '=', $id)
                            ->update([
                                'deleted_by' => Auth::user()->user_name
                            ]);
                $data->delete();

                $log = ActionLog::create([
                    'module' => 'Preference',
                    'action' => 'Delete',
                    'desc' => 'Delete Preference',
                    'username' => Auth::user()->user_name
                ]);

                return response()->json($data);
            }
            else {
                return redirect('/Preference')->with('warning', 'Anda tidak memiliki Hak Akses untuk menghapus data!');
            }
        }
        else {
            return redirect('/');
        }
    }
}
