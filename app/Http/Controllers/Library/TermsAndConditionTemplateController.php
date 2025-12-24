<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\ActionLog;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Setting\Module;

class TermsAndConditionTemplateController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TermsAndCondTemplate'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $user = Auth::user()->user_group;

            if ($countAkses > 0) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                $hakAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/TermsAndCondTemplate'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Terms n Condition Template',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.terms_and_cond_template.index', $data);
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

        $dataTerms = TermsAndConditionTemplate::all();

        return response()->json($dataTerms);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TermsAndCondTemplate'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Terms n Condition Template',
                    'action' => 'Buat',
                    'desc' => 'Buat Template',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.terms_and_cond_template.add', $data);
            }
            else {
                return redirect('/TermsAndCondTemplate')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/TermsAndCondTemplate'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTerms = TermsAndConditionTemplate::find($id);
                $detailTerms = TermsAndConditionTemplateDetail::where('id_template', $id)->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataTerms'] = $dataTerms;
                $data['detailTerms'] = $detailTerms;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Terms n Condition Template',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Template',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.terms_and_cond_template.edit', $data);
            }
            else {
                return redirect('/TermsAndCondTemplate')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function detail($id)
    {
        if (Auth::check()) {

            $countHakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TermsAndCondTemplate'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->count();

            $user = Auth::user()->user_group;

            if ($countHakAkses > 0) {
                $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TermsAndCondTemplate'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataTerms = TermsAndConditionTemplate::find($id);
                $detailTerms = TermsAndConditionTemplateDetail::where('id_template', $id)->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataTerms'] = $dataTerms;
                $data['detailTerms'] = $detailTerms;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Terms n Condition Template',
                    'action' => 'Detail',
                    'desc' => 'Detail Template',
                    'username' => Auth::user()->user_name
                ]);


                return view('pages.library.terms_and_cond_template.detail', $data);
            }
            else {
                return redirect('/TermsAndCondTemplate')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template'=>'required',
            'target_template'=>'required'
        ]);

        $nama  = $request->input('nama_template');
        $target = $request->input('target_template');


        $dataTemplate = new TermsAndConditionTemplate;
        $dataTemplate->nama_template = $nama;
        $dataTemplate->target_template = $target;
        $dataTemplate->created_by = Auth::user()->user_name;
        $dataTemplate->save();

        $arrayTermsAndCond = $request->input('isi');
        if ($arrayTermsAndCond != "") {
            $listTerms = [];
                foreach ($arrayTermsAndCond as $detil) {
                    $dataTerms=[
                        'id_template' => $dataTemplate->id,
                        'terms_and_condition' => $detil["terms"],
                        'created_by' => Auth::user()->user_name,
                        'created_at' => now(),
                    ];
                    array_push($listTerms, $dataTerms);
                }
            TermsAndConditionTemplateDetail::insert($listTerms);
        }

        $log = ActionLog::create([
            'module' => 'Terms n Condition Template',
            'action' => 'Simpan',
            'desc' => 'Simpan Template',
            'username' => Auth::user()->user_name
        ]);

        if ($dataTemplate) {
            return redirect('/TermsAndCondTemplate')->with('success', 'Data Template '.ucwords($nama).' Berhasil Disimpam!');
        }
        else {
            return redirect()->back()->with('warning', 'Data Gagal Disimpan!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_template'=>'required',
            'target_template'=>'required'
        ]);

        $nama  = $request->input('nama_template');
        $target = $request->input('target_template');


        $dataTemplate = TermsAndConditionTemplate::find($id);
        $dataTemplate->nama_template = $nama;
        $dataTemplate->target_template = $target;
        $dataTemplate->updated_by = Auth::user()->user_name;
        $dataTemplate->save();

        $arrayTermsAndCond = $request->input('isi');
        if ($arrayTermsAndCond != "") {
            $delete = DB::table('terms_and_condition_template_detail')->where('id_template', '=', $id)->delete();
            $listTerms = [];
                foreach ($arrayTermsAndCond as $detil) {
                    $dataTerms=[
                        'id_template' => $dataTemplate->id,
                        'terms_and_condition' => $detil["terms"],
                        'updated_by' => Auth::user()->user_name,
                        'updated_at' => now(),
                    ];
                    array_push($listTerms, $dataTerms);
                }
            TermsAndConditionTemplateDetail::insert($listTerms);
        }

        $log = ActionLog::create([
            'module' => 'Terms n Condition Template',
            'action' => 'Update',
            'desc' => 'Update Template',
            'username' => Auth::user()->user_name
        ]);

        if ($dataTemplate) {
            return redirect('/TermsAndCondTemplate')->with('success', 'Data Template '.ucwords($nama).' Berhasil Diupdate!');
        }
        else {
            return redirect()->back()->with('warning', 'Data Gagal Disimpan!');
        }
    }

    public function delete(Request $request)
    {

        $id = $request->input('id_template');
        $user = Auth::user()->user_name;
        $delete = TermsAndConditionTemplate::find($id);
        $delete->deleted_by = $user;
        $delete->save();
        $delete->delete();

        $log = ActionLog::create([
            'module' => 'Terms n Condition Template',
            'action' => 'Delete',
            'desc' => 'Delete Template',
            'username' => Auth::user()->user_name
        ]);

        return response()->json(['success'=>'Data Berhasil Dihapus!']);
     }
}
