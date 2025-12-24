<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Models\Accounting\TaxSettings;
use App\Models\Accounting\TaxSettingsPPN;
use App\Models\Library\CustomerDetail;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\Setting\Preference;
use stdClass;

class TaxSettingsController extends Controller
{
    public function index() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/TaxSettings'],
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
                                                ['module.url', '=', '/TaxSettings'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::find(1);
                $preferensi = Preference::all();
                $ppnList = TaxSettingsPPN::all();

                $data['hakAkses'] = $hakAkses;
                $data['preferensi'] = $preferensi;
                $data['ppnList'] = $ppnList;
                $data['taxSettings'] = $taxSettings;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Settings',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Tax Settings',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.tax_settings.edit', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
		'ppn'=>'required',
            'preferensi'=>'required',
        ]);

        $taxPercent = $request->input('ppn');
        $idPreferensi = $request->input('preferensi');
        $enableTax = $request->input('enable_tax');
        $autoGenerate = $request->input('auto_generate');
      	$user = Auth::user()->user_name;

        $update = TaxSettings::find($id);
        $update->ppn_percentage_id = $taxPercent;
        $update->id_preferensi = $idPreferensi;
        $update->enable_tax = $enableTax;
        $update->auto_generate_tax_invoice = $autoGenerate;
        $update->updated_by = $user;
        $update->save();

        $log = ActionLog::create([
            'module' => 'TaxSettings',
            'action' => 'Update',
            'desc' => 'Update TaxSettings',
            'username' => Auth::user()->user_name
        ]);

          return redirect('TaxSettings')->with('success', 'Data Pengaturan Pajak Telah Diupdate!');
    }

    public function getAddress(Request $request)
    {
        $id = $request->input('id_pref');

        $dataAlamat = Preference::select(DB::raw("CONCAT(alamat_pt, ', ', kelurahan_pt, ', ', kecamatan_pt, ', ', kota_pt) AS txtAlamat"))
                                ->where([
                                    ['id', '=', $id]
                                ])
                                ->first();


        return response()->json($dataAlamat);
    }

    public function getPPn(Request $request)
    {
        $id = $request->input('id_ppn');
        $mode = $request->input('mode');

        if ($mode == "display") {
            $dataPPn = TaxSettingsPPN::where([
                ['id', '=', $id]
            ])
            ->first();
        }
        else {
            $dataPPn = TaxSettingsPPN::all();
        }

        return response()->json($dataPPn);
    }

    public function SavePPn(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $mode = $request->input('mode');
            $ppn_id = $request->input('ppn_id');
            $percentage = $request->input('percentage');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($mode == "add") {
                $taxPPN = new TaxSettingsPPN();
                $taxPPN->ppn_name = "PPn ".$percentage;
                $taxPPN->ppn_percentage = $percentage;
                $taxPPN->ppn_start_date = $startDate;
                $taxPPN->ppn_end_date = $endDate;
                $taxPPN->created_by = Auth::user()->user_name;
                $taxPPN->save();

                $data = $taxPPN;
            }
            else {
                $taxPPN = TaxSettingsPPN::find($ppn_id);
                $taxPPN->ppn_percentage = $percentage;
                $taxPPN->ppn_start_date = $startDate;
                $taxPPN->ppn_end_date = $endDate;
                $taxPPN->created_by = Auth::user()->user_name;
                $taxPPN->save();
                $data = $taxPPN;
            }


        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }
}
