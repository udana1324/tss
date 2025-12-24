<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperSalesTaxInvoice;
use App\Exports\ExportFakturPajak;
use App\Models\Accounting\SalesTaxInvoice;
use App\Models\Accounting\SalesTaxInvoiceDetail;
use App\Models\Accounting\TaxSerialNumber;
use App\Models\Accounting\TaxSettings;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use Illuminate\Support\Carbon;
use App\Models\Setting\Module;
use App\Models\Setting\Preference;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class TaxSerialNumberController extends Controller
{
    public function index() {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/TaxSerialNumber'],
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
                                                ['module.url', '=', '/TaxSerialNumber'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Settings',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Tax Settings',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.tax_serial_number.index', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexMassGenerate()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/MassGenerateFP'],
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
                                                ['module.url', '=', '/MassGenerateFP'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Mass Generate Faktur Pajak',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Mass Generate Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.tax_serial_number.indexMassGenerate', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function indexFP()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/FakturPajak'],
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
                                                ['module.url', '=', '/FakturPajak'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $data['hakAkses'] = $hakAkses;
                $data['taxSettings'] = $taxSettings;
                $data['dataCustomer'] = $dataCustomer;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Faktur Pajak',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.accounting.tax_serial_number.indexFP', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataIndexFP(Request $request)
    {

        $data = SalesTaxInvoice::all();
        $periode = $request->input('periode');

        // $listSJ = Delivery::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
        //                     ->select('sales_invoice_detail.id_invoice',
        //                         DB::raw("GROUP_CONCAT(delivery.kode_pengiriman SEPARATOR ',') as list_sj")
        //                     )
        //                     ->groupBy('sales_invoice_detail.id_invoice');

        $data = SalesTaxInvoice::leftJoin('sales_invoice', 'sales_tax_invoice.id_invoice', 'sales_invoice.id')
                                ->leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                // ->leftJoinSub($listSJ, 'listSJ', function($listSJ) {
                                //     $listSJ->on('sales_invoice.id', '=', 'listSJ.id_invoice');
                                // })
                                ->select(
                                    'sales_tax_invoice.id',
                                    'sales_tax_invoice.nomor_faktur',
                                    'sales_tax_invoice.tanggal_faktur',
                                    'sales_tax_invoice.dpp',
                                    'sales_tax_invoice.ppn',
                                    'sales_tax_invoice.grand_total',
                                    'sales_tax_invoice.ttl_qty',
                                    'sales_tax_invoice.flag_export',
                                    'sales_tax_invoice.flag_batal',
                                    'sales_tax_invoice.pembetulan',
                                    'sales_tax_invoice.id_parent',
                                    'customer.nama_customer',
                                    DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                    DB::raw("COALESCE(customer.npwp_customer_16, customer.npwp_customer) as npwp_customer"),
                                    // 'listSJ.list_sj',
                                    'sales_order.no_so',
                                    'sales_order.metode_pembayaran',
                                    'sales_order.no_po_customer',
                                    'sales_order.nominal_dp',
                                    'sales_invoice.kode_invoice',
                                    'sales_invoice.tanggal_invoice',
                                    'sales_invoice.tanggal_jt',
                                    'sales_invoice.durasi_jt',
                                    'sales_invoice.flag_revisi',
                                    'sales_invoice.flag_tf',
                                    'sales_invoice.flag_pembayaran',
                                    'sales_invoice.status_invoice')
                                ->when($periode != "", function($q) use ($periode) {
                                    $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($periode)->format('m'));
                                    $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($periode)->format('Y'));
                                })
                                ->orderBy('sales_tax_invoice.id', 'desc')
                                ->get();

        return response()->json($data);
    }

    public function getDataIndex()
    {

        $data = TaxSerialNumber::orderBy('id', 'desc')->get();

        return response()->json($data);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/TaxSerialNumber'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data['hakAkses'] = $hakAkses;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Nomor Seri Faktur Pajak',
                    'action' => 'Tambah',
                    'desc' => 'Tambah Nomor Seri Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.add', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/TaxSerialNumber'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSerialNumber = TaxSerialNumber::find($id);

                $data['hakAkses'] = $hakAkses;
                $data['dataSerialNumber'] = $dataSerialNumber;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.edit', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/TaxSerialNumber'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $dataSerialNumber = TaxSerialNumber::find($id);

                $exportedFP = SalesTaxInvoice::where([
                    ['id_seri', '=', $id],
                    ['flag_export', '=', '1']
                ])
                ->count();

                $data['hakAkses'] = $hakAkses;
                $data['dataSerialNumber'] = $dataSerialNumber;
                $data['exportedFP'] = $exportedFP;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Tampilan',
                    'desc' => 'Tampilan Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.detail', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function store(Request $request)
    {
	    $request->validate([
	    	'no_permohonan'=>'required',
	        'tanggal_permohonan'=>'required',
            'no_pemberitahuan'=>'required',
	        'tanggal_pemberitahuan'=>'required',
            'tahun_pajak'=>'required',
	        'jumlah_seri'=>'required',
            'seri_awal'=>'required',
	        'seri_akhir'=>'required'
	    ]);

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {

            $noPermohonan = $request->input('no_permohonan');
            $tglPermohonan = $request->input('tanggal_permohonan');
            $noPemberitahuan = $request->input('no_pemberitahuan');
            $tglPemberitahuan = $request->input('tanggal_pemberitahuan');
            $thnPajak = $request->input('tahun_pajak');
            $jmlSeri = $request->input('jumlah_seri');
            $awal = strtolower($request->input('seri_awal'));
            $akhir = $request->input('seri_akhir');
            $user = Auth::user()->user_name;

            $taxSerialNumber = new TaxSerialNumber();
            $taxSerialNumber->no_permohonan = $noPermohonan;
            $taxSerialNumber->tanggal_permohonan = $tglPermohonan;
            $taxSerialNumber->no_pemberitahuan_djp = $noPemberitahuan;
            $taxSerialNumber->tanggal_pemberitahuan_djp = $tglPemberitahuan;
            $taxSerialNumber->tahun_berlaku_seri = $thnPajak;
            $taxSerialNumber->jumlah_no_seri = $jmlSeri;
            $taxSerialNumber->nomor_seri_dari = $awal;
            $taxSerialNumber->nomor_seri_sampai = $akhir;
            $taxSerialNumber->status = 'draft';
            $taxSerialNumber->created_by = $user;
            $taxSerialNumber->save();

            $data = $taxSerialNumber;

            $log = ActionLog::create([
                'module' => 'Tax Serial Number',
                'action' => 'Simpan',
                'desc' => 'Simpan Tax Serial Number',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('TaxSerialNumber.Detail', [$data->id])->with('success', 'Nomor Seri '.strtoupper($data->no_pemberitahuan_djp).' Telah Disimpan!');
        }
        else {
            return redirect('/TaxSerialNumber')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
	    	'no_permohonan'=>'required',
	        'tanggal_permohonan'=>'required',
            'no_pemberitahuan'=>'required',
	        'tanggal_pemberitahuan'=>'required',
            'tahun_pajak'=>'required',
	        'jumlah_seri'=>'required',
            'seri_awal'=>'required',
	        'seri_akhir'=>'required'
	    ]);

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $noPermohonan = $request->input('no_permohonan');
            $tglPermohonan = $request->input('tanggal_permohonan');
            $noPemberitahuan = $request->input('no_pemberitahuan');
            $tglPemberitahuan = $request->input('tanggal_pemberitahuan');
            $thnPajak = $request->input('tahun_pajak');
            $jmlSeri = $request->input('jumlah_seri');
            $awal = strtolower($request->input('seri_awal'));
            $akhir = $request->input('seri_akhir');
            $user = Auth::user()->user_name;

            $taxSerialNumber = TaxSerialNumber::find($id);
            $taxSerialNumber->no_permohonan = $noPermohonan;
            $taxSerialNumber->tanggal_permohonan = $tglPermohonan;
            $taxSerialNumber->no_pemberitahuan_djp = $noPemberitahuan;
            $taxSerialNumber->tanggal_pemberitahuan_djp = $tglPemberitahuan;
            $taxSerialNumber->tahun_berlaku_seri = $thnPajak;
            $taxSerialNumber->jumlah_no_seri = $jmlSeri;
            $taxSerialNumber->nomor_seri_dari = $awal;
            $taxSerialNumber->nomor_seri_sampai = $akhir;
            $taxSerialNumber->created_by = $user;
            $taxSerialNumber->save();

            $data = $taxSerialNumber;

            $log = ActionLog::create([
                'module' => 'Tax Serial Number',
                'action' => 'Ubah',
                'desc' => 'Ubah Tax Serial Number',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('TaxSerialNumber.Detail', [$data->id])->with('success', 'Nomor Seri '.strtoupper($data->no_pemberitahuan_djp).' Telah Diupdate!');
        }
        else {
            return redirect('/TaxSerialNumber')->with('error', $exception);
        }
    }

    public function delete(Request $request)
    {

      	$id = $request->input('id_sei');
      	$user = Auth::user()->user_name;
      	$data = TaxSerialNumber::find($id);
        $bank = DB::table('tax_serial_number')
                    ->where('id', '=', $id)
                    ->update([
                        'deleted_by' => Auth::user()->user_name
                    ]);
        $data->delete();

        $log = ActionLog::create([
            'module' => 'Tax Serial Number',
            'action' => 'Delete',
            'desc' => 'Delete Tax Serial Number',
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

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $taxSerialNumber = TaxSerialNumber::find($id);
            $data = $taxSerialNumber;

            $cekExported = SalesTaxInvoice::where([
                ['id_seri', '=', $id],
                ['flag_export', '=', '1']
            ])
            ->count();

            if ($btnAction == "posting") {
                $taxSerialNumber->status = "posted";
                $taxSerialNumber->save();
                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Posting',
                    'desc' => 'Posting Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = 'ubah';
            }
            elseif ($btnAction == "tutup") {
                $taxSerialNumber->status = "close";
                $taxSerialNumber->updated_by = Auth::user()->user_name;
                $taxSerialNumber->save();

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Tutup',
                    'desc' => 'Tutup Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Ditutup!';
                $status = 'success';
            }
            elseif ($btnAction == "batal") {
                if ($cekExported > 0) {
                    $taxSerialNumber->status = "batal";
                    $taxSerialNumber->updated_by = Auth::user()->user_name;
                    $taxSerialNumber->save();

                    $log = ActionLog::create([
                        'module' => 'Tax Serial Number',
                        'action' => 'Batal',
                        'desc' => 'Batal Tax Serial Number',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Telah Dibatalkan!';
                    $status = 'success';
                }
                else {
                    $msg = 'Nomor Seri Faktur Pajak : '.strtoupper($taxSerialNumber->no_pemberitahuan_djp).' Tidak dapat Dibatalkan karena terdapat Surat Jalan Faktur pajak yang sudah di export!';
                    $status = 'warning';
                }
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('TaxSerialNumber.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function MassGenerateTaxInvoice(Request $request)
    {
        $failMsg = "";
        $successCount = 0;
        $returnData = [];
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
        $invoices = SalesInvoice::where([
                                        ['flag_fp', '=', '0'],
                                        ['flag_ppn', '!=', 'N'],
                                        ['status_invoice', '=', 'posted']
                                    ])
                                    ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                        $q->whereBetween('sales_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                    })
                                    ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                        $q->whereMonth('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                        $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                    })
                                    ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                        $q->whereYear('sales_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                    })
                                    ->orderBy('sales_invoice.id')
                                    ->get();


        if ($taxSettings->enable_tax == "Y" && $taxSettings->auto_generate_tax_invoice != "Y") {
            foreach ($invoices as $invoice) {

                $dataTaxInvoice = array();
                $sales = SalesInvoice::find($invoice->id);

                $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->select(
                                                    DB::raw("sales_order.persentase_diskon/100 *  sales_invoice.dpp AS diskon"),
                                                    'sales_invoice.*'
                                                )
                                                ->where([
                                                    ['sales_invoice.id', '=', $invoice->id],
                                                    ['sales_invoice.flag_fp', '=', 0]
                                                ])
                                                ->first();

                $idSo = $dataSalesInvoice->id_so;
                $detailSalesInvoice = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('sales_order_detail',function($qJoin) use ($idSo) {
                                                            $qJoin->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item')
                                                            ->where('sales_order_detail.id_so', $idSo);
                                                        })
                                                        ->select(
                                                            'delivery_detail.id_item',
                                                            'delivery_detail.id_satuan',
                                                            'delivery_detail.qty_item',
                                                            'sales_order_detail.harga_jual',
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $invoice->id]
                                                            ])
                                                        ->get();

                                                       // dd($dataSalesInvoice, $detailSalesInvoice);

                $dataTaxInvoice['dataSalesInvoice'] = $dataSalesInvoice;
                $dataTaxInvoice['detailSalesInvoice'] = $detailSalesInvoice;

                $result = HelperSalesTaxInvoice::AutoGenerateTaxInvoice($dataTaxInvoice, 0, null);
                //dd($result);

                if ($result == "success") {
                    $sales->flag_fp = 1;
                    $sales->save();

                    $successCount = $successCount + 1;
                }
                else if ($result == "failNoTaxSeries") {
                    $failMsg = "Tidak terdapat nomor seri faktur yang dapat digunakan, harap input nomor seri terlebih dahulu!";
                    break;
                }
                else if ($result == "failTaxSeriesRunOut") {
                    $failMsg = "Nomor seri faktur yang dapat digunakan telah habis, Harap input nomor seri baru!";
                    break;
                }
            }
        }


        if ($failMsg != "") {
            $fail = ["failMsg" => $failMsg];
            array_push($returnData,$fail);
        }
        else {
            $fail = ["failMsg" => "noFail"];
            array_push($returnData,$fail);
        }

        if ($successCount > 0) {

            $dataInv = SalesTaxInvoice::leftJoin("sales_invoice", 'sales_tax_invoice.id_invoice', '=', 'sales_invoice.id')
                                    ->leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                                    ->leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                    ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                    ->select(
                                        'sales_tax_invoice.id',
                                        'sales_tax_invoice.nomor_faktur',
                                        'sales_tax_invoice.tanggal_faktur',
                                        'sales_tax_invoice.dpp',
                                        'sales_tax_invoice.ppn',
                                        'sales_tax_invoice.grand_total',
                                        'sales_tax_invoice.ttl_qty',
                                        'customer.nama_customer',
                                        DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                        'delivery.kode_pengiriman',
                                        'sales_order.no_so',
                                        'sales_order.metode_pembayaran',
                                        'sales_order.no_po_customer',
                                        'sales_order.nominal_dp',
                                        'sales_invoice.kode_invoice',
                                        'sales_invoice.tanggal_invoice',
                                        'sales_invoice.tanggal_jt',
                                        'sales_invoice.durasi_jt',
                                        'sales_invoice.flag_revisi',
                                        'sales_invoice.flag_tf',
                                        'sales_invoice.flag_pembayaran',
                                        'sales_invoice.status_invoice')
                                    ->where([
                                        ['sales_invoice.flag_fp', '=', 1]
                                    ])
                                    ->whereIn('sales_tax_invoice.id_invoice', $invoices->pluck('id'))
                                    ->orderBy('sales_tax_invoice.id')
                                    ->get();

            $data = ["dataInv" => $dataInv];
            array_push($returnData, $data);

        }
        else {
            $data = ["dataInv" => null];
            array_push($returnData, $data);
        }

        return response()->json($returnData);
    }

    public function exportDataFP(Request $request)
    {
        $periode = $request->input('bulan_picker_val');
        $periodeBulan = Carbon::parse($periode)->isoFormat('MMM');
        $periodeTahun = Carbon::parse($periode)->isoFormat('Y');
        return Excel::download(new ExportFakturPajak($request), 'FakturPajak_'.$periodeBulan.'_'.$periodeTahun.'.xlsx');
    }

    public function detailFP($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/FakturPajak'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                $data = array();
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);

                $dataInv = SalesTaxInvoice::leftJoin('sales_invoice', 'sales_tax_invoice.id_invoice', '=', 'sales_invoice.id')
                                    ->leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('sales_tax_invoice as parent', 'sales_tax_invoice.id_parent', 'parent.id')
                                    ->select(
                                        'sales_tax_invoice.id',
                                        'sales_invoice.kode_invoice',
                                        'sales_order.no_so',
                                        'sales_order.id_customer',
                                        'customer.nama_customer',
                                        'sales_tax_invoice.nomor_faktur',
                                        'sales_tax_invoice.tanggal_faktur',
                                        'sales_tax_invoice.dpp',
                                        'sales_tax_invoice.ppn',
                                        'sales_tax_invoice.grand_total',
                                        'sales_tax_invoice.pembetulan',
                                        'sales_tax_invoice.flag_batal',
                                        'sales_tax_invoice.jenis_faktur',
                                        'sales_tax_invoice.id_parent',
                                        'sales_invoice.ttl_qty',
                                        'sales_invoice.status_invoice',
                                        'sales_invoice.flag_revisi',
                                        'sales_invoice.flag_ppn',
                                        'parent.jenis_faktur as jenis_faktur_parent',
                                        'parent.pembetulan as pembetulan_parent',
                                        'parent.nomor_faktur as nomor_faktur_parent',
                                        'parent.tanggal_faktur as tanggal_faktur_parent'
                                    )
                                    ->where([
                                        ['sales_tax_invoice.id', '=', $id],
                                    ])
                                    ->first();

                $detailFaktur = SalesTaxInvoiceDetail::leftJoin('product', 'sales_tax_invoice_detail.id_item', '=', 'product.id')
                                                    ->select(
                                                        'product.kode_item',
                                                        'product.nama_item',
                                                        'sales_tax_invoice_detail.harga_jual',
                                                        'sales_tax_invoice_detail.qty'
                                                    )
                                                    ->where([
                                                        ['id_faktur', '=', $dataInv->id]
                                                    ])
                                                    ->get();

                $dataAlamat = CustomerDetail::where([
                                    ['id_customer', '=', $dataInv->id_customer],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

                if ($dataAlamat == null) {
                    $dataAlamat = CustomerDetail::find($dataInv->id_alamat);
                }

                $dataPreference = Preference::select(
                                                'preference.nama_pt',
                                                DB::raw("CONCAT(alamat_pt, ', ', COALESCE(kelurahan_pt, '-'), ', ', kecamatan_pt, ', ', kota_pt) AS alamat")
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();
                $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
                $ppnPercentageInc = 1+($taxSettings->ppn_percentage/100);
                $ppnPercentageExc = $taxSettings->ppn_percentage/100;

                $data['hakAkses'] = $hakAkses;
                $data['dataInv'] = $dataInv;
                $data['detailFaktur'] = $detailFaktur;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataPreference'] = $dataPreference;
                $data['taxSettings'] = $taxSettings;
                $data['ppnPercentageInc'] = $ppnPercentageInc;
                $data['ppnPercentageExc'] = $ppnPercentageExc;

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Tax Serial Number',
                    'action' => 'Tampilan',
                    'desc' => 'Tampilan Tax Serial Number',
                    'username' => Auth::user()->user_name
                ]);



                return view('pages.accounting.tax_serial_number.detail_fp', $data);
            }
            else {
                return redirect('/TaxSerialNumber')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function postingFP(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $idPengganti = "";
        $successCount = 0;
        $btnAction = $request->input('submit_action');
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status, $idPengganti, $successCount, $btnAction)
        {

            $taxInvoice = SalesTaxInvoice::find($id);

            if ($btnAction == "revisi") {
                $dataTaxInvoice = array();

                $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->select(
                                                    DB::raw("sales_order.persentase_diskon/100 *  sales_invoice.dpp AS diskon"),
                                                    'sales_invoice.*'
                                                )
                                                ->where([
                                                    ['sales_invoice.id', '=', $taxInvoice->id_invoice],
                                                    ['sales_invoice.flag_fp', '=', 1]
                                                ])
                                                ->first();

                $idSo = $dataSalesInvoice->id_so;
                $detailSalesInvoice = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('sales_order_detail',function($qJoin) use ($idSo) {
                                                            $qJoin->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item')
                                                            ->where('sales_order_detail.id_so', $idSo);
                                                        })
                                                        ->leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'product.satuan_item', 'product_unit.id')
                                                        ->select(
                                                            'delivery_detail.id_item',
                                                            'delivery_detail.qty_item',
                                                            'sales_order_detail.harga_jual',
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $taxInvoice->id_invoice]
                                                            ])
                                                        ->get();

                                                       // dd($dataSalesInvoice, $detailSalesInvoice);

                $dataTaxInvoice['dataSalesInvoice'] = $dataSalesInvoice;
                $dataTaxInvoice['detailSalesInvoice'] = $detailSalesInvoice;

                $result = HelperSalesTaxInvoice::AutoGenerateTaxInvoice($dataTaxInvoice, 1, $id);

                if ($result == "success") {
                    $msg = 'Faktur Pajak : '.strtoupper($taxInvoice->jenis_faktur.$taxInvoice->pembetulan.$taxInvoice->nomor_faktur).' Telah Direvisi!';
                    $status = 'success';

                    $taxInvoice->flag_batal = 2;
                    $taxInvoice->save();
                }
                else if ($result == "failNoTaxSeries") {
                    $msg = "Tidak terdapat nomor seri faktur yang dapat digunakan, harap input nomor seri terlebih dahulu!";
                    $status = 'warning';
                }
                else if ($result == "failTaxSeriesRunOut") {
                    $msg = "Nomor seri faktur yang dapat digunakan telah habis, Harap input nomor seri baru!";
                    $status = 'warning';
                }


                $log = ActionLog::create([
                    'module' => 'Faktur Pajak',
                    'action' => 'Pembetulan',
                    'desc' => 'Pembetulan Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);

            }
            elseif ($btnAction == "refresh") {
                $dataTaxInvoice = array();

                $dataSalesInvoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                                ->select(
                                                    DB::raw("sales_order.persentase_diskon/100 *  sales_invoice.dpp AS diskon"),
                                                    'sales_invoice.*'
                                                )
                                                ->where([
                                                    ['sales_invoice.id', '=', $taxInvoice->id_invoice],
                                                    ['sales_invoice.flag_fp', '=', 1]
                                                ])
                                                ->first();

                $idSo = $dataSalesInvoice->id_so;
                $detailSalesInvoice = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('sales_order_detail',function($qJoin) use ($idSo) {
                                                            $qJoin->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item')
                                                            ->where('sales_order_detail.id_so', $idSo);
                                                        })
                                                        ->leftJoin('product', 'sales_order_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'product.satuan_item', 'product_unit.id')
                                                        ->select(
                                                            'delivery_detail.id_item',
                                                            'delivery_detail.qty_item',
                                                            'sales_order_detail.harga_jual',
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $taxInvoice->id_invoice]
                                                            ])
                                                        ->get();

                                                       // dd($dataSalesInvoice, $detailSalesInvoice);

                $dataTaxInvoice['dataSalesInvoice'] = $dataSalesInvoice;
                $dataTaxInvoice['detailSalesInvoice'] = $detailSalesInvoice;

                $result = HelperSalesTaxInvoice::RefreshTaxInvoice($dataTaxInvoice, $id);

                if ($result == "success") {
                    $msg = 'Faktur Pajak : '.strtoupper($taxInvoice->jenis_faktur.$taxInvoice->pembetulan.$taxInvoice->nomor_faktur).' Telah Direfresh!';
                    $status = 'success';

                    $taxInvoice->flag_batal = 2;
                    $taxInvoice->save();
                }
                else if ($result == "failNoTaxSeries") {
                    $msg = "Tidak terdapat nomor seri faktur yang dapat digunakan, harap input nomor seri terlebih dahulu!";
                    $status = 'warning';
                }
                else if ($result == "failTaxSeriesRunOut") {
                    $msg = "Nomor seri faktur yang dapat digunakan telah habis, Harap input nomor seri baru!";
                    $status = 'warning';
                }


                $log = ActionLog::create([
                    'module' => 'Faktur Pajak',
                    'action' => 'Refresh',
                    'desc' => 'Refresh Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);

            }
            elseif ($btnAction == "batal") {
                $taxInvoice->flag_batal = 1;
                $taxInvoice->updated_by = Auth::user()->user_name;
                $taxInvoice->save();

                $salesInv = SalesInvoice::find($taxInvoice->id_invoice);
                $salesInv->flag_fp = 0;
                $salesInv->save();

                $log = ActionLog::create([
                    'module' => 'Faktur Pajak',
                    'action' => 'Batal',
                    'desc' => 'Batal Faktur Pajak',
                    'username' => Auth::user()->user_name
                ]);
                $msg = 'Faktur Pajak : '.strtoupper($salesInv->jenis_faktur.$salesInv->pembetulan.$salesInv->nomor_faktur).' Telah Dibatalkan!';
                $status = 'success';
            }
        });

        if (is_null($exception)) {
            if ($btnAction == "revisi") {
                if ($status == 'success') {
                    $newTaxInv = SalesTaxInvoice::where('id_parent', $id)->first();
                    if ($newTaxInv != null) {
                        return redirect('/FakturPajak/Detail/'.$newTaxInv->id)->with($status, $msg);
                    }
                    else {
                        return redirect()->back()->with('warning', 'Faktur Pajak Baru tidak ditemukan');
                    }
                }
                else {
                    return redirect()->back()->with($status, $msg);
                }
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function exportDataFPXML(Request $request) {
        $periode = $request->input('bulan_picker_val');
        $periodeBulan = Carbon::parse($periode)->isoFormat('MMM');
        $periodeTahun = Carbon::parse($periode)->isoFormat('Y');

        $dataFP = [];
        $ids = $request->input('id_invoices');
        $arrayIDs = explode(',', $ids);
        if ($periode != null || $ids != null) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

            $transaction = SalesTaxInvoice::leftJoin('sales_invoice', 'sales_tax_invoice.id_invoice', 'sales_invoice.id')
                                            ->leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('customer_detail',function($qJoin) {
                                                $qJoin->on('customer.id', '=', 'customer_detail.id_customer')
                                                //->where('customer_detail.jenis_alamat', "NPWP");
                                                ->whereRaw(
                                                    "customer_detail.jenis_alamat = CASE WHEN customer.jenis_customer = 'C' THEN 'NPWP' ELSE 'Gudang/Pengiriman' END"
                                                );
                                            })
                                            ->select(
                                                'sales_tax_invoice.id',
                                                'sales_tax_invoice.id_invoice',
                                                'sales_tax_invoice.nomor_faktur',
                                                'sales_tax_invoice.jenis_faktur',
                                                'sales_tax_invoice.pembetulan',
                                                'sales_tax_invoice.tanggal_faktur',
                                                'sales_tax_invoice.diskon',
                                                'sales_tax_invoice.dpp',
                                                'sales_tax_invoice.ppn',
                                                'sales_tax_invoice.grand_total',
                                                'sales_tax_invoice.ttl_qty',
                                                'sales_order.persentase_diskon',
                                                'customer.nama_customer',
                                                'customer.npwp_customer',
                                                'customer.npwp_customer_16',
                                                'customer.ktp_customer',
                                                'customer.jenis_customer',
                                                DB::raw("CONCAT(customer_detail.alamat_customer, ', ', customer_detail.kelurahan, ', ', customer_detail.kecamatan, ', ', customer_detail.kota) AS txtAlamat"),
                                                'sales_invoice.kode_invoice',
                                                'sales_invoice.flag_ppn',
                                            )
                                            // ->where([
                                            //     ['sales_tax_invoice.flag_export', '=', 0]
                                            // ])
                                            ->when($periode != "", function($q) use ($periode) {
                                                $q->whereMonth('sales_tax_invoice.tanggal_faktur', Carbon::parse($periode)->format('m'));
                                                $q->whereYear('sales_tax_invoice.tanggal_faktur', Carbon::parse($periode)->format('Y'));
                                            })
                                            ->when($ids != "", function($q) use ($arrayIDs) {
                                                $q->whereIn('sales_tax_invoice.id', $arrayIDs);
                                            })
                                            ->orderBy('sales_tax_invoice.nomor_faktur', 'desc')
                                            ->get();

            foreach ($transaction as $dataTransaction) {
                $salesTaxInvoice = SalesTaxInvoice::find($dataTransaction->id);
                $salesTaxInvoice->flag_export = 1;
                $salesTaxInvoice->save();

                $detailItem = SalesInvoiceDetail::leftJoin('delivery_detail', 'sales_invoice_detail.id_sj', '=', 'delivery_detail.id_pengiriman')
                                                        ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                        ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                                        ->select(
                                                            'delivery_detail.id_item',
                                                            'product_category.kode_kategori_pajak',
                                                            'product_unit.kode_satuan_pajak',
                                                            )
                                                        ->where([
                                                                ['sales_invoice_detail.id_invoice', '=', $dataTransaction->id_invoice]
                                                        ]);

                $detailTemp = SalesTaxInvoiceDetail::leftJoin('product', 'sales_tax_invoice_detail.id_item', 'product.id')
                                                    ->leftJoinSub($detailItem, 'detailItem', function($detailItem) {
                                                        $detailItem->on('product.id', '=', 'detailItem.id_item');
                                                    })
                                                    ->select(
                                                        DB::raw("'OF' AS HeadRow"),
                                                        'product.nama_item',
                                                        'sales_tax_invoice_detail.qty',
                                                        'sales_tax_invoice_detail.harga_jual',
                                                        'detailItem.kode_kategori_pajak',
                                                        'detailItem.kode_satuan_pajak',
                                                    )
                                                    ->where([
                                                        ['sales_tax_invoice_detail.id_faktur', '=', $dataTransaction->id]
                                                    ])
                                                    ->get();

                if (count($detailTemp) > 0) {
                    $npwp = $dataTransaction->npwp_customer_16 != "" && $dataTransaction->npwp_customer_16 != null ? $dataTransaction->npwp_customer_16 == "0000.0000.0000.0000" ? $dataTransaction->npwp_customer : $dataTransaction->npwp_customer_16 : "0".$dataTransaction->npwp_customer;
                    $dataFaktur = [
                        'HeadRow' => 'FK',
                        'HeadJenis' => $dataTransaction->jenis_faktur,
                        'FKPengganti' => $dataTransaction->pembetulan,
                        'nomor_faktur' => str_replace('.', '',$dataTransaction->nomor_faktur),
                        'masa_pajak' => Carbon::parse($dataTransaction->tanggal_faktur)->format('m'),
                        'tahun_pajak' => Carbon::parse($dataTransaction->tanggal_faktur)->format('Y'),
                        'tanggal_faktur' => $dataTransaction->tanggal_faktur,
                        'flag_ppn' => $dataTransaction->flag_ppn,
                        'diskon' => $dataTransaction->persentase_diskon,
                        'dpp' => $dataTransaction->dpp,
                        'ppn' => $dataTransaction->ppn,
                        'grand_total' => $dataTransaction->grand_total,
                        'ttl_qty' => $dataTransaction->ttl_qty,
                        'nama_customer' => $dataTransaction->nama_customer,
                        'ktp_customer' => $dataTransaction->ktp_customer,
                        'jenis_customer' => $dataTransaction->jenis_customer,
                        'npwp_customer' => $dataTransaction->jenis_customer == "I" ? "0000000000000000" : $npwp,
                        'dokumen_customer' => $dataTransaction->jenis_customer == "I" ? "National ID" : "TIN",
                        'txtAlamat' => $dataTransaction->txtAlamat,
                        'kode_invoice' => $dataTransaction->kode_invoice,
                        'detailFaktur' => $detailTemp,
                    ];
                    array_push($dataFP, $dataFaktur);
                }
            }
        }

        $ppnPercentageInc = 1+($taxSettings->ppn_percentage/100);
        $ppnPercentageExc = $taxSettings->ppn_percentage/100;
        $data['dataExport'] = $dataFP;
        $data['dataPreference'] = Preference::where([['flag_default', '=', 'Y']])->first();
        $data['taxSettings'] = $taxSettings;
        $data['ppnPercentageInc'] = $ppnPercentageInc;
        $data['ppnPercentageExc'] = $ppnPercentageExc;

        $xml = view('pages.accounting.tax_serial_number.exportFakturPajakXML', ['data' => $data])->render();

        // return response($xml)->withHeaders([
        // 'content-type' => 'text/xml'
        // ]);

        return response($xml, 200, [
            'Content-Type' => 'application/xml', // use your required mime type
            'Content-Disposition' => 'attachment; filename="FakturPajak_'.$periodeBulan.'_'.$periodeTahun.'.xml"',
        ]);
    }
}
