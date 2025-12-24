<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperPurchaseInvoice;
use App\Classes\BusinessManagement\HelperPurchaseInvoiceCollection;
use App\Models\Accounting\TaxSettings;
use App\Models\Library\CompanyAccount;
use App\Models\Library\Supplier;
use App\Models\Library\SupplierDetail;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceCollection;
use App\Models\Purchasing\PurchaseInvoiceCollectionDetail;
use App\Models\Purchasing\PurchaseInvoiceCollectionTerms;
use App\Models\Purchasing\PurchaseInvoiceDetail;
use App\Models\Purchasing\PurchaseInvoiceTerms;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\Receiving;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use Codedge\Fpdf\Fpdf\Fpdf;
use stdClass;

class PurchaseInvoiceCollectionController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/PurchaseInvoiceCollection'],
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
                                                ['module.url', '=', '/PurchaseInvoiceCollection'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = PurchaseInvoiceCollection::distinct()->get('status');
                $dataSupplier = Supplier::distinct()->get('nama_supplier');

                $delete = DB::table('purchase_invoice_collection_detail')->where('deleted_at', '!=', null)->delete();

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataSupplier'] = $dataSupplier;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice_collection.index', $data);
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

        $invCollection = PurchaseInvoiceCollection::leftJoin('supplier', 'purchase_invoice_collection.id_supplier', '=', 'supplier.id')
                                                ->select(
                                                    'supplier.nama_supplier',
                                                    'purchase_invoice_collection.id',
                                                    'purchase_invoice_collection.kode_tf',
                                                    'purchase_invoice_collection.nominal',
                                                    'purchase_invoice_collection.tanggal',
                                                    'purchase_invoice_collection.pic_pengirim',
                                                    'purchase_invoice_collection.nmr_tf',
                                                    'purchase_invoice_collection.flag_revisi',
                                                    'purchase_invoice_collection.status')
                                                ->when($periode != "", function($q) use ($periode) {
                                                    $q->whereMonth('purchase_invoice_collection.tanggal', Carbon::parse($periode)->format('m'));
                                                    $q->whereYear('purchase_invoice_collection.tanggal', Carbon::parse($periode)->format('Y'));
                                                })
                                                ->orderBy('purchase_invoice_collection.id', 'desc')
                                                ->get();
        return response()->json($invCollection);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->whereIn('purchase_order.id', function($query){
                                            $query->select('id_po')->from('purchase_invoice');
                                            $query->where([
                                                ['status_invoice', '=', 'posted'],
                                                ['flag_tf', '=', '0']
                                            ]);
                                        })
                                        ->get();

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                $dataPreference = Preference::select(
                                                'preference.*'
                                            )
                                            ->where('flag_default', 'Y')
                                            ->first();

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataAccount'] = $CompanyAccount;
                $data['dataPreference'] = $dataPreference;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Buat',
                    'desc' => 'Buat Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('purchase_invoice_collection_detail')->where('id_tf', '=', 'DRAFT')->delete();

                return view('pages.purchasing.invoice_collection.add', $data);
            }
            else {
                return redirect('/PurchaseInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataSupplier = Supplier::distinct()
                                        ->join('purchase_order', 'purchase_order.id_supplier', 'supplier.id')
                                        ->select(
                                            'supplier.id',
                                            'supplier.nama_supplier'
                                        )
                                        ->whereIn('purchase_order.id', function($query){
                                            $query->select('id_po')->from('purchase_invoice');
                                            $query->where([
                                                ['status_invoice', '=', 'posted'],
                                                ['flag_tf', '=', '0']
                                            ]);
                                        })
                                        ->get();

                $dataCollection = PurchaseInvoiceCollection::leftJoin('supplier_detail', 'purchase_invoice_collection.id_alamat', '=', 'supplier_detail.id')
                                                        ->select(
                                                            'purchase_invoice_collection.id',
                                                            'purchase_invoice_collection.kode_tf',
                                                            'purchase_invoice_collection.tanggal',
                                                            'purchase_invoice_collection.nominal',
                                                            'purchase_invoice_collection.status',
                                                            'purchase_invoice_collection.id_supplier',
                                                            'purchase_invoice_collection.id_alamat',
                                                            'purchase_invoice_collection.id_rekening',
                                                            'purchase_invoice_collection.pic_pengirim',
                                                            'supplier_detail.alamat_supplier',
                                                        )
                                                        ->where([
                                                            ['purchase_invoice_collection.id', '=', $id],
                                                        ])
                                                        ->first();

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->get();

                if ($dataCollection->status != "draft") {
                    return redirect('/PurchaseInvoiceCollection')->with('warning', 'Tukar Faktur Invoice Penjualan tidak dapat diubah karena status Tukar Faktur bukan DRAFT!');
                }

                $restore = PurchaseInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
                $restore->restore();

                $dataTerms = PurchaseInvoiceCollectionTerms::where('id_tf', $id)->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataSupplier'] = $dataSupplier;
                $data['dataCollection'] = $dataCollection;
                $data['dataAccount'] = $CompanyAccount;
                $data['dataTerms'] = $dataTerms;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice_collection.edit', $data);
            }
            else {
                return redirect('/PurchaseInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCollection = PurchaseInvoiceCollection::leftJoin('supplier_detail', 'purchase_invoice_collection.id_alamat', '=', 'supplier_detail.id')
                                                        ->leftJoin('supplier', 'purchase_invoice_collection.id_supplier', '=', 'supplier.id')
                                                        ->leftJoin('company_account', 'company_account.id', '=', 'purchase_invoice_collection.id_rekening')
                                                        ->leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                        ->select(
                                                            'purchase_invoice_collection.id',
                                                            'purchase_invoice_collection.kode_tf',
                                                            'purchase_invoice_collection.tanggal',
                                                            'purchase_invoice_collection.nominal',
                                                            'purchase_invoice_collection.status',
                                                            'purchase_invoice_collection.id_rekening',
                                                            'purchase_invoice_collection.flag_approved',
                                                            'purchase_invoice_collection.id_supplier',
                                                            'purchase_invoice_collection.pic_pengirim',
                                                            'purchase_invoice_collection.diterima_oleh',
                                                            'purchase_invoice_collection.updated_by',
                                                            'supplier_detail.alamat_supplier',
                                                            'supplier.nama_supplier',
                                                            'company_account.atas_nama',
                                                            'company_account.nomor_rekening',
                                                            'bank.kode_bank',
                                                            'bank.nama_bank'
                                                        )
                                                        ->where([
                                                            ['purchase_invoice_collection.id', '=', $id],
                                                        ])
                                                        ->first();

                $dataTerms = PurchaseInvoiceCollectionTerms::where('id_tf', $id)->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCollection'] = $dataCollection;
                $data['dataTerms'] = $dataTerms;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Detail',
                    'desc' => 'Detail Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.purchasing.invoice_collection.detail', $data);
            }
            else {
                return redirect('/PurchaseInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/PurchaseInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataPurchaseInvoiceCollection = PurchaseInvoiceCollection::leftJoin('supplier', 'purchase_invoice_collection.id_supplier', '=', 'supplier.id')
                                                                    ->select(
                                                                        'supplier.kode_supplier',
                                                                        'supplier.nama_supplier',
                                                                        'supplier.npwp_supplier',
                                                                        'supplier.telp_supplier',
                                                                        'supplier.fax_supplier',
                                                                        'supplier.email_supplier',
                                                                        'supplier.kategori_supplier',
                                                                        'purchase_invoice_collection.*'
                                                                    )
                                                                    ->where([
                                                                        ['purchase_invoice_collection.id', '=', $id],
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
                                            ->where('flag_default', 'Y')
                                            ->first();

                $CompanyAccount = CompanyAccount::leftJoin('bank', 'company_account.bank', '=', 'bank.id')
                                                ->select(
                                                    'company_account.*',
                                                    'bank.kode_bank',
                                                    'bank.nama_bank'
                                                )
                                                ->where([
                                                    ['company_account.id', '=', $dataPurchaseInvoiceCollection->id_rekening]
                                                ])
                                                ->first();


                $detailPurchaseInvoiceCollection = PurchaseInvoiceCollectionDetail::leftJoin('purchase_invoice', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
                                                                            ->leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                                            ->leftJoin('supplier_detail', 'purchase_order.id_alamat', '=', 'supplier_detail.id')
                                                                            ->select(
                                                                                'purchase_invoice_collection_detail.id',
                                                                                'purchase_invoice.kode_invoice',
                                                                                'purchase_invoice.tanggal_invoice',
                                                                                'purchase_invoice.tanggal_jt',
                                                                                'purchase_invoice.grand_total',
                                                                                'purchase_order.no_po'
                                                                                )
                                                                            ->where([
                                                                                    ['purchase_invoice_collection_detail.id_tf', '=', $id]
                                                                                ])
                                                                            ->get();

                $dataAlamat = SupplierDetail::where([
                                                ['id_supplier', '=', $dataPurchaseInvoiceCollection->id_supplier]
                                            ])->first();

                $data['dataPurchaseInvoiceCollection'] = $dataPurchaseInvoiceCollection;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseInvoiceCollection'] = $detailPurchaseInvoiceCollection;
                $data['CompanyAccount'] = $CompanyAccount;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperPurchaseInvoiceCollection::cetakPdfInvCollection($data);
                $namaFile = str_replace("/","_",$dataPurchaseInvoiceCollection->kode_tf);

                $fpdf->Output('I', strtoupper($namaFile).".pdf");
                exit;
            }
            else {
                return redirect('/PurchaseInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakKwitansi($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/PurchaseInvoiceCollection'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataPurchaseInvoiceCollection = PurchaseInvoiceCollection::leftJoin('supplier', 'purchase_invoice_collection.id_supplier', '=', 'supplier.id')
                                                                    ->select(
                                                                        'supplier.kode_supplier',
                                                                        'supplier.nama_supplier',
                                                                        'supplier.npwp_supplier',
                                                                        'supplier.telp_supplier',
                                                                        'supplier.fax_supplier',
                                                                        'supplier.email_supplier',
                                                                        'supplier.kategori_supplier',
                                                                        'supplier.collection',
                                                                        'purchase_invoice_collection.id_alamat',
                                                                        'purchase_invoice_collection.*'
                                                                    )
                                                                    ->where([
                                                                        ['purchase_invoice_collection.id', '=', $id],
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
                                            ->where('flag_default', 'Y')
                                            ->first();

                $detailPurchaseInvoiceCollection = PurchaseInvoiceCollectionDetail::leftJoin('purchase_invoice', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
                                                                            ->leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
                                                                            ->leftJoin('supplier_detail', 'purchase_order.id_alamat', '=', 'supplier_detail.id')
                                                                            ->select(
                                                                                'purchase_invoice_collection_detail.id',
                                                                                'purchase_invoice.kode_invoice',
                                                                                'purchase_invoice.tanggal_invoice',
                                                                                'purchase_invoice.tanggal_jt',
                                                                                'purchase_invoice.grand_total',
                                                                                'purchase_order.no_po'
                                                                                )
                                                                            ->where([
                                                                                    ['purchase_invoice_collection_detail.id_tf', '=', $id]
                                                                                ])
                                                                            ->get();

                $dataAlamat = SupplierDetail::where([
                                                ['id_supplier', '=', $dataPurchaseInvoiceCollection->id_supplier]
                                            ])->first();

                $data['dataPurchaseInvoiceCollection'] = $dataPurchaseInvoiceCollection;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailPurchaseInvoiceCollection'] = $detailPurchaseInvoiceCollection;

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperPurchaseInvoiceCollection::cetakKwitansiPdfInvCollection($data);
                $no_kw = str_replace("tf", "KW", $dataPurchaseInvoiceCollection->kode_tf);
                $namaFile = str_replace("/","_",$no_kw);

                $fpdf->Output('I', strtoupper($namaFile).".pdf");
                exit;
            }
            else {
                return redirect('/PurchaseInvoiceCollection')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function preview(Request $request, Fpdf $fpdf)
    {
        $id = $request->input('idInvoice');

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
                                                        ->leftJoin('product_unit', 'purchase_order_detail.id_satuan', 'product_unit.id')
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
                'action' => 'Preview',
                'desc' => 'Preview Purchase Invoice',
                'username' => Auth::user()->user_name
            ]);

            $fpdf = HelperPurchaseInvoice::cetakPdfInv($data);

            $fpdf->Output('F', "preview/purchasing/preview_invoice.pdf");

        return response()->json("success");
    }

    public function confirm(Request $request)
    {
        $data = new stdClass();
        $penerima = $request->input('namaPenerima');
        $nmr = $request->input('nmr');
        if ($penerima == "" || $penerima == null) {
            return response()->json("false");
        }
        $exception = DB::transaction(function () use ($request, &$data, $penerima, $nmr) {
            $id = $request->input('idCollection');

            $dataCollection = PurchaseInvoiceCollection::find($id);
            $dataCollection->flag_approved = '1';
            $dataCollection->pic_pengirim = $nmr;
            $dataCollection->nmr_tf = $penerima;
            $dataCollection->updated_by = Auth::user()->user_name;
            $dataCollection->save();
            $data = $dataCollection;

        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function getInvoice(Request $request)
    {
        $idSupplier = $request->input('idSupplier');

        $dataInv = PurchaseInvoice::select(
                                    'purchase_invoice.id',
                                    'purchase_invoice.kode_invoice',
                                    'purchase_invoice.tanggal_invoice',
                                    'purchase_invoice.tanggal_jt',
                                    'purchase_invoice.grand_total',
                                )
                                ->where([
                                    ['purchase_invoice.flag_tf', '=', '0'],
                                    ['purchase_invoice.status_invoice', '=', 'posted']
                                ])
                                ->whereIn('purchase_invoice.id_po', function($query) use ($idSupplier) {
                                    $query->select('purchase_order.id')->from('purchase_order');
                                    $query->where([
                                        ['purchase_order.id_supplier', '=', $idSupplier]
                                    ]);
                                })
                                ->orderBy('purchase_invoice.id', 'asc')
                                ->get();

        return response()->json($dataInv);
    }

    public function getInvoiceData(Request $request)
    {
        $idInvoice = $request->input('idInvoice');

        $dataInv = PurchaseInvoice::where('id', $idInvoice)->get();

        return response()->json($dataInv);
    }

    public function getDefaultAddress(Request $request)
    {
        $idSupplier = $request->input('idSupplier');

        $npwp = SupplierDetail::where([
                                    ['id_supplier', '=', $idSupplier],
                                    ['jenis_alamat', '=', 'NPWP']
                                ])
                                ->first();

        $kantor = SupplierDetail::where([
                                    ['id_supplier', '=', $idSupplier],
                                    ['jenis_alamat', '=', 'Kantor']
                                ])
                                ->first();

        $defaultAddress = SupplierDetail::where([
                                ['id_supplier', '=', $idSupplier]
                            ])
                            ->first();

            return response()->json($defaultAddress);

        if ($npwp == null && $kantor == null) {

            return response()->json($defaultAddress);
        }
        else {
            if ($npwp != null) {
                return response()->json($npwp);
            }
            if ($kantor != null) {
                return response()->json($npwp);
            }
            else {
                return response()->json($defaultAddress);
            }
        }
    }

    public function GetDate(Request $request)
    {
        $id = $request->input('idInvoice');

        $detail = PurchaseInvoice::select(
                                DB::raw('MAX(purchase_invoice.tanggal_invoice) AS lastDate'),
                            )
                            ->whereIn('purchase_invoice.id', function($subQuery) use ($id) {
                                $subQuery->select('id_invoice')->from('purchase_invoice_collection_detail')
                                ->where('id_tf', $id);
                            })
                            ->first();

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function getSupplierAddress(Request $request)
    {
        $idSupplier = $request->input('id_supplier');

        $supplierAddress = SupplierDetail::where([
                                            ['id_supplier', '=', $idSupplier]
                                        ])
                                        ->get();

        return response()->json($supplierAddress);
    }

    public function StoreInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idCollection');
            $idInv = $request->input('idInv');
            $user = Auth::user()->user_name;

            if ($id == "") {
                $id = 'DRAFT';
            }

            $countItem = DB::table('purchase_invoice_collection_detail')->select(DB::raw("COUNT(*) AS angka"))->where([['id_invoice', '=' , $idInv]])->first();
            $count = $countItem->angka;

            if ($count > 0) {
                $data = "failDuplicate";
            }
            else {

                $listItem = new PurchaseInvoiceCollectionDetail();
                $listItem->id_tf = $id;
                $listItem->id_invoice = $idInv;
                $listItem->created_by = $user;
                $listItem->save();

                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection Detail',
                    'action' => 'Simpan',
                    'desc' => 'Simpan Purchase Invoice Collection Detail',
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

    public function SetCollectionDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idCollection');
            $idSupplier = $request->input('idSupplier');
            if ($id == "") {
                $id = 'DRAFT';
            }
            $delete = DB::table('purchase_invoice_collection_detail')->where('id_tf', '=', $id)->delete();

            $dataInv = PurchaseInvoice::select(
                                            'purchase_invoice.id'
                                        )
                                        ->where([
                                            ['purchase_invoice.flag_tf', '=', '0'],
                                            ['purchase_invoice.status_invoice', '=', 'posted']
                                        ])
                                        ->whereIn('purchase_invoice.id_po', function($query) use ($idSupplier) {
                                            $query->select('purchase_order.id')->from('purchase_order');
                                            $query->where([
                                                ['purchase_order.id_supplier', '=', $idSupplier]
                                            ]);
                                        })
                                        ->get();

            $data = $dataInv;
            $listDetail = [];
            foreach ($dataInv As $detail) {

                $dataDetail = [
                    'id_tf' => $id,
                    'id_invoice' => $detail->id,
                    'created_at' => now(),
                    'created_by' => Auth::user()->user_name,
                ];
                array_push($listDetail, $dataDetail);
            }
            PurchaseInvoiceCollectionDetail::insert($listDetail);
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceDetail(Request $request)
    {
        $id = $request->input('idCollection');

        if ($id == "") {
            $id = 'DRAFT';
        }

        $detail = PurchaseInvoiceCollectionDetail::leftJoin('purchase_invoice', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
                                                ->select(
                                                    'purchase_invoice_collection_detail.id',
                                                    'purchase_invoice_collection_detail.id_invoice',
                                                    'purchase_invoice.kode_invoice',
                                                    'purchase_invoice.tanggal_invoice',
                                                    'purchase_invoice.tanggal_jt',
                                                    'purchase_invoice.grand_total'
                                                )
                                                ->where([
                                                    ['purchase_invoice_collection_detail.id_tf', '=', $id],
                                                ])
                                                ->get();

        return response()->json($detail);
    }

    public function DeleteInvoiceDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDetail');
            $mode = $request->input('mode');
            $massDelete = $request->input('massDelete');

            if ($mode != "") {
                if ($massDelete == "Yes") {
                    DB::table('purchase_invoice_collection_detail')
                            ->whereIn('id', $id)
                            ->update([
                                'deleted_at' => now(),
                                'deleted_by' => Auth::user()->user_name
                            ]);
                }
                else {
                    $detail = PurchaseInvoiceCollectionDetail::find($id);
                    $detail->deleted_by = Auth::user()->user_name;
                    $detail->save();

                    $detail->delete();
                }

            }
            else {
                if ($massDelete == "Yes") {
                    DB::table('purchase_invoice_collection_detail')
                            ->whereIn('id', $id)
                            ->delete();
                }
                else {
                    $delete = DB::table('purchase_invoice_collection_detail')->where('id', '=', $id)->delete();
                }
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function RestoreInvoiceDetail(Request $request)
    {

        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idTf');
            $restore = PurchaseInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
            $restore->restore();

        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetInvoiceFooter(Request $request)
    {
        $id = $request->input('idTf');
        if ($id == "") {
            $id = 'DRAFT';
        }


        $detail = PurchaseInvoiceCollectionDetail::leftJoin('purchase_invoice', 'purchase_invoice_collection_detail.id_invoice', '=', 'purchase_invoice.id')
                                        ->select(
                                            DB::raw('COALESCE(SUM(purchase_invoice.grand_total),0) AS nominalTf'),
                                        )
                                        ->where([
                                            ['purchase_invoice_collection_detail.id_tf', '=', $id]
                                        ])
                                        ->groupBy('purchase_invoice_collection_detail.id_tf')
                                        ->first();

        if ($detail) {
            return response()->json($detail);
        }
        else {
            return response()->json("null");
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier'=>'required',
            'tanggal_tf'=>'required',
        ]);

        $tglTf = $request->input('tanggal_tf');

        $bulanIndonesia = Carbon::parse($tglTf)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglTf);
        if (!$aksesTransaksi) {
            return redirect('/PurchaseInvoiceCollection')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tgl = $request->input('tanggal_tf');
            $pic = $request->input('pic');
            $rekPerusahaan = $request->input('company_account');
            $nominal = $request->input('nominal');
            $user = Auth::user()->user_name;
            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $nominal = str_replace(",", ".", $nominal);

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));
            $tahunPeriode = date("y", strtotime($tgl));

            $countKode = DB::table('purchase_invoice_collection')
                        ->select(DB::raw("MAX(RIGHT(kode_tf,2)) AS angka"))
                        //->whereYear('tanggal', $thnPeriode)
                        ->whereDate('tanggal', $tgl)
                        ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');

            if ($counter < 10) {
                $kodeTf = "ttf-cv-".$kodeTgl."0".$counter;
            }
            else {
                $kodeTf = "ttf-cv-".$kodeTgl.$counter;
            }

            $collection = new PurchaseInvoiceCollection();
            $collection->kode_tf = $kodeTf;
            $collection->id_supplier = $idSupplier;
            $collection->pic_pengirim = $pic;
            $collection->id_ppn = $taxSettings->ppn_percentage_id;
            $collection->id_rekening = $rekPerusahaan;
            $collection->nominal = $nominal;
            $collection->flag_revisi = '0';
            $collection->tanggal = $tgl;
            $collection->status = 'draft';
            $collection->created_by = $user;
            $collection->save();

            $data = $collection;

            $setDetail = DB::table('purchase_invoice_collection_detail')
                            ->where([
                                        ['id_tf', '=', 'DRAFT']
                                    ])
                            ->update([
                                'id_tf' => $collection->id,
                                'updated_by' => $user
                            ]);

            if ($terms != "") {
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_tf' => $collection->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                PurchaseInvoiceCollectionTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Purchase Invoice Collection',
                'action' => 'Simpan',
                'desc' => 'Simpan Purchase Invoice Collection',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseInvoiceCollection.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_tf).' Telah Disimpan!');
        }
        else {
            return redirect('/PurchaseInvoiceCollection')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier'=>'required',
            'tanggal_tf'=>'required',
        ]);

        $tglTf = $request->input('tanggal_tf');

        $bulanIndonesia = Carbon::parse($tglTf)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglTf);
        if (!$aksesTransaksi) {
            return redirect()->route('PurchaseInvoiceCollection.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();
            $idSupplier = $request->input('supplier');
            $idAlamat = $request->input('id_alamat');
            $tgl = $request->input('tanggal_tf');
            $pic = $request->input('pic');
            $rekPerusahaan = $request->input('company_account');
            $nominal = $request->input('nominal');
            $user = Auth::user()->user_name;
            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');

            $nominal = str_replace(",", ".", $nominal);

            $collection = PurchaseInvoiceCollection::find($id);
            $collection->id_supplier = $idSupplier;
            $collection->id_alamat = $idAlamat;
            $collection->id_rekening = $rekPerusahaan;
            $collection->nominal = $nominal;
            $collection->tanggal = $tgl;
            $collection->pic_pengirim = $pic;
            $collection->id_ppn = $taxSettings->ppn_percentage_id;
            $collection->updated_by = $user;
            $collection->save();

            $data = $collection;

            $deletedDetail = PurchaseInvoiceCollectionDetail::onlyTrashed()->where([['id_tf', '=', $id]]);
            $deletedDetail->forceDelete();

            if ($terms != "") {
                $delete = DB::table('purchase_invoice_collection_terms')->where('id_tf', '=', $collection->id)->delete();
                $listTerms = [];
                foreach ($terms as $tnc) {
                    $dataTerms = [
                        'id_tf' => $collection->id,
                        'terms_and_cond' => $tnc,
                        'created_at' => now(),
                        'created_by' => $user
                    ];
                    array_push($listTerms, $dataTerms);
                }
                PurchaseInvoiceCollectionTerms::insert($listTerms);
            }

            $log = ActionLog::create([
                'module' => 'Purchase Invoice Collection',
                'action' => 'Update',
                'desc' => 'Update Purchase Invoice Collection',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('PurchaseInvoiceCollection.Detail', [$data->id])->with('success', 'Data '.strtoupper($data->kode_tf).' Telah Diubah!');
        }
        else {
            return redirect('/PurchaseInvoiceCollection')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $collection = PurchaseInvoiceCollection::find($id);

            if ($btnAction == "posting") {
                $collection->status = "posted";
                $collection->diterima_oleh = Auth::user()->user_name;
                $collection->save();

                $updateInvoice = DB::table('purchase_invoice')
                                        ->whereIn('purchase_invoice.id', function($subQuery) use ($id) {
                                            $subQuery->select('id_invoice')->from('purchase_invoice_collection_detail')
                                            ->where('id_tf', $id);
                                        })
                                        ->update([
                                            'flag_tf' => '1',
                                            'updated_by' => Auth::user()->user_name,
                                        ]);


                $log = ActionLog::create([
                    'module' => 'Purchase Invoice Collection',
                    'action' => 'Posting',
                    'desc' => 'Posting Purchase Invoice Collection',
                    'username' => Auth::user()->user_name
                ]);

                $msg = 'Data '.strtoupper($collection->kode_tf).' Telah Diposting!';
                $status = 'success';
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                if ($collection->status == "posted" && $collection->flag_approved == 0) {
                    $collection->status = "draft";
                    $collection->flag_revisi = '1';
                    $collection->updated_by = Auth::user()->user_name;
                    $collection->save();

                    $updateInvoice = DB::table('purchase_invoice')
                                        ->whereIn('purchase_invoice.id', function($subQuery) use ($id) {
                                            $subQuery->select('id_invoice')->from('purchase_invoice_collection_detail')
                                            ->where('id_tf', $id);
                                        })
                                        ->update([
                                            'flag_tf' => '0',
                                            'updated_by' => Auth::user()->user_name,
                                        ]);

                    $log = ActionLog::create([
                        'module' => 'Purchase Invoice Collection',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Purchase Invoice Collection',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Tukar Faktur '.strtoupper($collection->kode_tf).' Telah Direvisi!';
                    $status = 'success';
                }
                else {
                    $msg = 'Tukar Faktur '.strtoupper($collection->kode_tf).' Tidak dapat Direvisi karena Tukar Faktur Telah di Approve !';
                    $status = 'warning';
                }
            }

        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('PurchaseInvoiceCollection.edit', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
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

    public function getListTerms(Request $request)
    {
        $target = $request->input('target');

        $listTemplate = TermsAndConditionTemplate::where([
                                            ['target_template', '=', $target]
                                        ])
                                        ->get();

        return response()->json($listTemplate);
    }
}
