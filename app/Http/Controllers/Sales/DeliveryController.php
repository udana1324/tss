<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Models\Library\Customer;
use App\Models\Library\CustomerDetail;
use App\Models\Library\CustomerProduct;
use App\Models\Product\Product;
use App\Models\Library\TermsAndConditionTemplateDetail;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use App\Models\Sales\Delivery;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\DeliveryTerms;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Classes\BusinessManagement\Helper;
use App\Classes\BusinessManagement\HelperDelivery;
use App\Exports\DeliveryExport;
use App\Models\Library\ExpeditionBranch;
use App\Models\Library\TermsAndConditionTemplate;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceDetail;
use App\Models\Sales\SalesOrderTerms;
use App\Models\Library\Sales;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Sales\DeliveryAllocation;
use App\Models\Setting\Preference;
use App\Models\Setting\Module;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use App\Models\TempTransaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class DeliveryController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/Delivery'],
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
                                                ['module.url', '=', '/Delivery'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataStatus = Delivery::distinct()->get('status_pengiriman');
                $dataCustomer = Customer::distinct()->get('nama_customer');

                $delete = DB::table('delivery_detail')->where('deleted_at', '!=', null)->delete();

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataStatus'] = $dataStatus;
                $data['dataCustomer'] = $dataCustomer;

                $log = ActionLog::create([
                    'module' => 'Delivery',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Delivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.delivery.index', $data);
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

        $delivery = Delivery::leftJoin('sales_order', 'delivery.id_so', 'sales_order.id')
                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                            ->leftJoin('customer_detail', 'sales_order.id_alamat', '=', 'customer_detail.id')
                            ->select(
                                'customer.nama_customer',
                                DB::raw("COALESCE(customer_detail.nama_outlet, '-') as nama_outlet"),
                                'sales_order.no_so',
                                'sales_order.no_po_customer',
                                'delivery.id',
                                'delivery.kode_pengiriman',
                                'delivery.no_sj_manual',
                                'delivery.jumlah_total_sj',
                                'delivery.tanggal_sj',
                                'delivery.tanggal_kirim',
                                'delivery.metode_pengiriman',
                                'delivery.flag_revisi',
                                'delivery.flag_terkirim',
                                'delivery.flag_invoiced',
                                'delivery.diterima_oleh',
                                'delivery.updated_by',
                                'delivery.status_pengiriman')
                            ->when($periode != "", function($q) use ($periode) {
                                $q->whereMonth('delivery.tanggal_sj', Carbon::parse($periode)->format('m'));
                                $q->whereYear('delivery.tanggal_sj', Carbon::parse($periode)->format('Y'));
                            })
                            ->orderBy('delivery.id', 'desc')
                            ->get();
        return response()->json($delivery);
    }

    public function create()
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->add == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::distinct()
                                        ->leftJoin('sales_order', 'sales_order.id_customer', 'customer.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->where([
                                            ['sales_order.status_so', '=', 'posted']
                                        ])
                                        ->get();
                $idSo = Session::get('id_so');
                $idCust = Session::get('id_cust');
                if ($idCust == "" && $idSo == "") {
                    $mode = "tambah";
                }
                else {
                    $mode = "kirim";
                }
                Session::forget('id_so');
                Session::forget('id_cust');
                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['idSo'] = $idSo;
                $data['idCust'] = $idCust;
                $data['mode'] = $mode;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Delivery',
                    'action' => 'Buat',
                    'desc' => 'Buat Delivery',
                    'username' => Auth::user()->user_name
                ]);

                $delete = DB::table('delivery_detail')
                            ->where([
                                ['id_pengiriman', '=', 'DRAFT'],
                                ['created_by', '=', Auth::user()->user_name]
                            ])
                            ->delete();

                return view('pages.sales.delivery.add', $data);
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->edit == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::distinct()
                                        ->leftJoin('sales_order', 'sales_order.id_customer', 'customer.id')
                                        ->select(
                                            'customer.id',
                                            'customer.nama_customer'
                                        )
                                        ->where([
                                            ['sales_order.status_so', '=', 'posted']
                                        ])
                                        ->get();

                $dataDlv = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.id_so',
                                        'delivery.id_alamat',
                                        'delivery.id',
                                        'delivery.tanggal_sj',
                                        'delivery.status_pengiriman',
                                        'delivery.flag_terms_so',
                                        'delivery.flag_revisi',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.status_so',
                                        'sales_order.metode_kirim',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['delivery.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataDlv->status_so == "draft") {
                    return redirect('/Delivery')->with('warning', 'Pengiriman tidak dapat diubah karena status Sales order adalah DRAFT!');
                }

                if ($dataDlv->status_pengiriman != "draft") {
                    return redirect('/Delivery')->with('warning', 'Pengiriman tidak dapat diubah karena status Pengiriman bukan DRAFT!');
                }

                // $restore = DeliveryDetail::onlyTrashed()->where([['id_pengiriman', '=', $id]]);
                // $restore->restore();

                $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'delivery'],
                                    ['value1', '=', $id]
                                ])->delete();
                $dataDetail = DeliveryDetail::where([
                                                    ['id_pengiriman', '=', $id]
                                                ])
                                                ->get();

                if ($dataDetail != "") {
                    $listTemp = [];
                    foreach ($dataDetail as $detail) {
                        $dataTemps = [
                            'module' => 'delivery',
                            'id_detail' => $detail->id,
                            'value1' => $detail->id_pengiriman,
                            'value2' => $detail->id_item,
                            'value3' => $detail->id_satuan,
                            'value4' => $detail->qty_item
                        ];
                        array_push($listTemp, $dataTemps);
                    }
                    TempTransaction::insert($listTemp);
                }

                if ($dataDlv->flag_terms_so == 0) {
                    $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataDlv->id_so)->get();
                }

                $dataAlamat = CustomerDetail::find($dataDlv->id_alamat);

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['dataAlamat'] = $dataAlamat;

                $log = ActionLog::create([
                    'module' => 'Delivery',
                    'action' => 'Ubah',
                    'desc' => 'Ubah Delivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.delivery.edit', $data);
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function staging($id)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->posting == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataDlv = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.id_so',
                                        'delivery.id_alamat',
                                        'delivery.tanggal_sj',
                                        'delivery.status_pengiriman',
                                        'delivery.flag_terkirim',
                                        'delivery.flag_invoiced',
                                        'delivery.flag_revisi',
                                        'delivery.flag_terms_so',
                                        'delivery.tanggal_kirim',
                                        'delivery.diterima_oleh',
                                        'delivery.updated_by',
                                        'delivery.jumlah_total_sj',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.metode_kirim',
                                        'sales_order.no_po_customer',
                                        'customer.nama_customer',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['delivery.id', '=', $id],
                                    ])
                                    ->first();
                $dataAlamat = CustomerDetail::find($dataDlv->id_alamat);

                if ($dataDlv->flag_terms_so == 0) {
                    $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataDlv->id_so)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailDlv = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('sales_order_detail', 'sales_order_detail.id_item', '=', 'delivery_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'delivery_detail.id',
                                            'delivery_detail.id_item',
                                            'delivery_detail.id_satuan',
                                            'delivery_detail.qty_item',
                                            'delivery_detail.id_pengiriman',
                                            'delivery_detail.keterangan',
                                            'sales_order_detail.qty_order',
                                            'sales_order_detail.qty_outstanding',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['delivery_detail.id_pengiriman', '=', $id],
                                            ['sales_order_detail.id_so', '=', $dataDlv->id_so]
                                        ])
                                        ->get();

                $alokasiDlv = DeliveryAllocation::leftJoin('product', 'delivery_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_allocation.id_satuan', 'product_unit.id')
                                                ->where([
                                                    ['delivery_allocation.id_delivery', '=', $id]
                                                ])
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

                $dataAlokasiDlv = [];
                foreach($alokasiDlv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_delivery' => $dataAlokasi->id_delivery,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'id_satuan' => $dataAlokasi->id_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiDlv, $dataAlloc);
                }

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['dataAlamat'] = $dataAlamat;
                $data['listIndex'] = $list;
                $data['detailDlv'] = $detailDlv;
                $data['dataAlokasiDlv'] = $dataAlokasiDlv;


                $log = ActionLog::create([
                    'module' => 'Delivery',
                    'action' => 'Detail',
                    'desc' => 'Detail Delivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.delivery.staging', $data);
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses != null) {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataCustomer = Customer::all();
                $dataDlv = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.id_so',
                                        'delivery.id_alamat',
                                        'delivery.tanggal_sj',
                                        'delivery.status_pengiriman',
                                        'delivery.flag_terkirim',
                                        'delivery.flag_invoiced',
                                        'delivery.flag_revisi',
                                        'delivery.flag_terms_so',
                                        'delivery.tanggal_kirim',
                                        'delivery.diterima_oleh',
                                        'delivery.updated_by',
                                        'delivery.jumlah_total_sj',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.metode_kirim',
                                        'sales_order.no_po_customer',
                                        'customer.nama_customer',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['delivery.id', '=', $id],
                                    ])
                                    ->first();
                $dataAlamat = CustomerDetail::find($dataDlv->id_alamat);

                if ($dataDlv->flag_terms_so == 0) {
                    $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataDlv->id_so)->get();
                }

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailDlv = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('sales_order_detail', 'sales_order_detail.id_item', '=', 'delivery_detail.id_item')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'delivery_detail.id',
                                            'delivery_detail.id_item',
                                            'delivery_detail.qty_item',
                                            'delivery_detail.keterangan',
                                            'delivery_detail.id_pengiriman',
                                            'sales_order_detail.qty_order',
                                            'sales_order_detail.qty_outstanding',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['delivery_detail.id_pengiriman', '=', $id],
                                            ['sales_order_detail.id_so', '=', $dataDlv->id_so]
                                        ])
                                        ->get();

                $alokasiDlv = DeliveryAllocation::leftJoin('product', 'delivery_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_allocation.id_satuan', 'product_unit.id')
                                                ->where([
                                                    ['delivery_allocation.id_delivery', '=', $id]
                                                ])
                                                ->get();

                if (count($alokasiDlv) < 1) {
                    return redirect()->route('Delivery.Staging', [$dataDlv->id])->with('warning', 'Lakukan Alokasi Pengiriman untuk '.strtoupper($dataDlv->kode_pengiriman).' Terlebih dahulu!');
                }

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

                $dataAlokasiDlv = [];
                foreach($alokasiDlv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_delivery' => $dataAlokasi->id_delivery,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                    ];
                    array_push($dataAlokasiDlv, $dataAlloc);
                }

                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataDlv'] = $dataDlv;
                $data['dataTerms'] = $dataTerms;
                $data['dataAlamat'] = $dataAlamat;
                $data['listIndex'] = $list;
                $data['detailDlv'] = $detailDlv;
                $data['dataAlokasiDlv'] = $dataAlokasiDlv;


                $log = ActionLog::create([
                    'module' => 'Delivery',
                    'action' => 'Detail',
                    'desc' => 'Detail Delivery',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.sales.delivery.detail', $data);
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
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
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataDelivery = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.id_so',
                                        'delivery.id_alamat',
                                        'delivery.tanggal_sj',
                                        'delivery.status_pengiriman',
                                        'delivery.flag_terkirim',
                                        'delivery.flag_invoiced',
                                        'delivery.flag_revisi',
                                        'delivery.tanggal_kirim',
                                        'delivery.flag_terms_so',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.metode_kirim',
                                        'sales_order.jenis_kirim',
                                        'sales_order.no_po_customer',
                                        'customer.sales',
                                        'customer.nama_customer',
                                        'customer.telp_customer',
                                        'customer.fax_customer',
                                        'customer.email_customer',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['delivery.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataDelivery->flag_terms_so == 0) {
                    $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataDelivery->id_so)->get();
                }

                $dataEkspedisi = new stdClass();

                if ($dataDelivery->metode_kirim == "ekspedisi") {
                    $dataEkspedisi = ExpeditionBranch::leftJoin('expedition', 'expedition_branch.id_expedisi', '=', 'expedition.id')
                                                    ->select(
                                                        'expedition_branch.id',
                                                        'expedition_branch.nama_cabang'
                                                    )
                                                    ->where([
                                                        ['expedition_branch.id', '=', $dataDelivery->jenis_kirim]
                                                    ])
                                                    ->first();
                }

                $dataAlamat = CustomerDetail::find($dataDelivery->id_alamat);
                $dataAlamatPenagihan = CustomerDetail::where([
                                                ['id_customer', '=', $dataDelivery->id_customer],
                                                ['jenis_alamat', '=', 'NPWP']
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
                                            ->where('flag_so', 'Y')
                                            ->first();

                $dataKeterangan = SalesOrderDetail::select('id_item', 'id_satuan', 'keterangan')
                                                    ->where([
                                                        ['id_so', '=',  $dataDelivery->id_so]
                                                    ]);

                $detailDelivery = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                ->leftJoinSub($dataKeterangan, 'dataKeterangan', function($dataKeterangan) {
                                                    $dataKeterangan->on('delivery_detail.id_item', '=', 'dataKeterangan.id_item');
                                                    $dataKeterangan->on('delivery_detail.id_satuan', '=', 'dataKeterangan.id_satuan');
                                                })
                                                ->select(
                                                    'delivery_detail.id',
                                                    'delivery_detail.id_item',
                                                    'delivery_detail.qty_item',
                                                    'delivery_detail.keterangan',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product.jenis_item',
                                                    'product_unit.nama_satuan',
                                                    'product.keterangan_item',
                                                    'dataKeterangan.keterangan'
                                                )
                                                ->where([
                                                    ['delivery_detail.id_pengiriman', '=', $id]
                                                ])
                                                ->get();

                $dataSales = Sales::find($dataDelivery->sales);
                $dataAlamat = CustomerDetail::find($dataDelivery->id_alamat);

                $data['dataDelivery'] = $dataDelivery;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['dataAlamatPenagihan'] = $dataAlamatPenagihan;
                $data['detailDelivery'] = $detailDelivery;
                $data['dataSales'] = $dataSales;
                $data['dataEkspedisi'] = $dataEkspedisi;

                $log = ActionLog::create([
                    'module' => 'Pengiriman',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Pengiriman',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperDelivery::cetakPdfDlv($data);

                $fpdf->Output('I', strtoupper($dataDelivery->kode_pengiriman).".pdf");
                exit;
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function cetakOrder($id, Fpdf $fpdf)
    {
        if (Auth::check()) {

            $hakAkses = DB::table('module')
                                ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select('*')
                                ->where([
                                            ['module.url', '=', '/Delivery'],
                                            ['module_access.user_id', '=', Auth::user()->id]
                                        ])
                                ->first();

            $user = Auth::user()->user_group;

            if ($hakAkses->print == "Y") {
                SetMenu::setDaftarMenu(Auth::user()->id);
                SetMenu::setDaftarMenuHeader(Auth::user()->id);
                $data = array();

                $dataDelivery = Delivery::leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                    ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                    ->leftJoin('customer_detail', 'delivery.id_alamat', '=', 'customer_detail.id')
                                    ->select(
                                        'delivery.id',
                                        'delivery.kode_pengiriman',
                                        'delivery.id_so',
                                        'delivery.id_alamat',
                                        'delivery.tanggal_sj',
                                        'delivery.status_pengiriman',
                                        'delivery.flag_terkirim',
                                        'delivery.flag_invoiced',
                                        'delivery.flag_revisi',
                                        'delivery.tanggal_kirim',
                                        'sales_order.id_customer',
                                        'sales_order.no_so',
                                        'sales_order.metode_kirim',
                                        'sales_order.jenis_kirim',
                                        'sales_order.no_po_customer',
                                        'customer.sales',
                                        'customer.nama_customer',
                                        'customer.telp_customer',
                                        'customer.fax_customer',
                                        'customer.email_customer',
                                        'customer_detail.alamat_customer',
                                    )
                                    ->where([
                                        ['delivery.id', '=', $id],
                                    ])
                                    ->first();
                if ($dataDelivery->flag_terms_so == "0") {
                    $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
                }
                else {
                    $dataTerms = SalesOrderTerms::where('id_so', $dataDelivery->id_so)->get();
                }

                $dataAlamat = CustomerDetail::find($dataDelivery->id_alamat);
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
                                            ->where('flag_so', 'Y')
                                            ->first();

                $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

                $detailDelivery = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                                })
                                                ->select(
                                                    'delivery_detail.id',
                                                    'delivery_detail.id_item',
                                                    'delivery_detail.qty_item',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product.jenis_item',
                                                    'product_unit.nama_satuan',
                                                    'product.keterangan_item',
                                                    'dataSpek.value_spesifikasi'
                                                )
                                                ->where([
                                                    ['delivery_detail.id_pengiriman', '=', $id]
                                                ])
                                                ->get();

                $alokasiDlv = DeliveryAllocation::leftJoin('product', 'delivery_allocation.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_allocation.id_satuan', 'product_unit.id')
                                                ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                    $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                                })
                                                ->where([
                                                    ['delivery_allocation.id_delivery', '=', $id]
                                                ])
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

                $dataAlokasiDlv = [];
                foreach($alokasiDlv as $dataAlokasi) {
                    $txtIndex = "-";
                    foreach ($list as $txt) {
                        if ($txt["id"] == $dataAlokasi->id_index) {
                            $txtIndex = $txt["nama_index"];
                        }
                    }
                    $dataAlloc = [
                        'id_delivery' => $dataAlokasi->id_delivery,
                        'id_detail' => $dataAlokasi->id_detail,
                        'id_item' => $dataAlokasi->id_item,
                        'kode_spek' => $dataAlokasi->value_spesifikasi,
                        'kode_item' => $dataAlokasi->kode_item,
                        'nama_item' => $dataAlokasi->nama_item,
                        'jenis_item' => $dataAlokasi->jenis_item,
                        'nama_satuan' => $dataAlokasi->nama_satuan,
                        'qty_item' => $dataAlokasi->qty_item,
                        'id_index' => $dataAlokasi->id_index,
                        'txt_index' => $txtIndex,
                        'value_spesifikasi' => $dataAlokasi->value_spesifikasi,
                    ];
                    array_push($dataAlokasiDlv, $dataAlloc);
                }

                $dataSales = Sales::find($dataDelivery->sales);
                $dataAlamat = CustomerDetail::find($dataDelivery->id_alamat);

                if ($dataDelivery->metode_kirim == "ekspedisi") {
                    $dataEkspedisi = ExpeditionBranch::find($dataDelivery->jenis_kirim);
                    $dataPengiriman = $dataEkspedisi->nama_cabang;
                }
                else {
                    $dataPengiriman = $dataDelivery->metode_kirim;
                }

                $data['dataDelivery'] = $dataDelivery;
                $data['dataTerms'] = $dataTerms;
                $data['dataPreference'] = $dataPreference;
                $data['dataAlamat'] = $dataAlamat;
                $data['detailDelivery'] = $detailDelivery;
                $data['dataSales'] = $dataSales;
                $data['dataAlokasiDlv'] = $dataAlokasiDlv;
                $data['dataPengiriman'] = $dataPengiriman;

                $log = ActionLog::create([
                    'module' => 'Order Pengiriman',
                    'action' => 'Cetak',
                    'desc' => 'Cetak Order Pengiriman',
                    'username' => Auth::user()->user_name
                ]);

                $fpdf = HelperDelivery::cetakPdfOrderDlv($data);

                $fpdf->Output('I', 'Order Kirim_'.strtoupper($dataDelivery->kode_pengiriman).".pdf");
                exit;
            }
            else {
                return redirect('/Delivery')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function RestoreDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idDlv');
            $restore = DeliveryDetail::onlyTrashed()->where([['id_pengiriman', '=', $id]]);
            $restore->restore();
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function getSalesOrder(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $dataSo = SalesOrder::where([
                                ['id_customer', '=', $idCustomer],
                                ['status_so', '=', 'posted']
                            ])
                            ->orderBy('id', 'desc')
                            ->get();

        return response()->json($dataSo);
    }

    public function getProduct(Request $request)
    {
        $idSalesOrder = $request->input('idSalesOrder');

        $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

        $dataProduct = SalesOrderDetail::leftJoin('product', 'sales_order_detail.id_item', 'product.id')
                                        ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                            $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                        })
                                        ->select(
                                            'product.*',
                                            'dataSpek.value_spesifikasi'
                                        )
                                        ->where([
                                            ['sales_order_detail.id_so', '=', $idSalesOrder],
                                            ['product.deleted_at', '=', null]
                                        ])
                                        ->orderBy('product.nama_item', 'asc')
                                        ->get();

        return response()->json($dataProduct);
    }

    public function getDefaultAddress(Request $request)
    {
        $idSalesOrder = $request->input('idSalesOrder');

        $idAlamat = SalesOrder::find($idSalesOrder);

        $defaultAddress = CustomerDetail::where([
                                            ['id', '=', $idAlamat->id_alamat]
                                        ])
                                        ->get();

        return response()->json($defaultAddress);
    }

    public function getCustomerAddress(Request $request)
    {
        $idCustomer = $request->input('id_customer');

        $customerAddress = CustomerDetail::where([
                                            ['id_customer', '=', $idCustomer]
                                        ])
                                        ->get();

        return response()->json($customerAddress);
    }

    public function getRequestDate(Request $request)
    {
        $idSalesOrder = $request->input('idSalesOrder');

        $salesOrder = SalesOrder::find($idSalesOrder);

        $requestDate = $salesOrder->tanggal_request;

        return response()->json($salesOrder);
    }

    public function getDataItem(Request $request)
    {
        $idProduct = $request->input('id_product');
        $idSatuan = $request->input('id_satuan');
        $idSo = $request->input('idSalesOrder');

        $dataProduct = Product::leftJoin('sales_order_detail', 'sales_order_detail.id_item', '=', 'product.id')
                                ->leftJoin('product_unit', 'sales_order_detail.id_satuan', 'product_unit.id')
                                        ->select(
                                            'product_unit.nama_satuan',
                                            'sales_order_detail.qty_outstanding',
                                        )
                                        ->where([
                                            ['product.id', '=', $idProduct],
                                            ['sales_order_detail.id_so', '=', $idSo],
                                            ['sales_order_detail.id_satuan', '=', $idSatuan],
                                        ])
                                        ->get();

        return response()->json($dataProduct);
    }

    public function StoreDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDelivery');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $countItem = DeliveryDetail::select(DB::raw("COUNT(*) AS angka"))
                                            ->where([
                                                ['id_pengiriman', '=' , $id],
                                                ['id_item', '=', $idItem],
                                                ['id_satuan', '=', $idSatuan]
                                            ])
                                            ->first();

                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new DeliveryDetail();
                    $listItem->id_pengiriman = $id;
                    $listItem->id_item = $idItem;
                    $listItem->id_satuan = $idSatuan;
                    $listItem->qty_item = $qty;
                    $listItem->created_by = $user;
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Delivery Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Delivery Detail',
                        'username' => Auth::user()->user_name
                    ]);

                    $data = "success";
                }
            }
            else {
                //Legend
                // 'value1' => $detail->id_invoice,
                // 'value2' => $detail->id_sj,
                // 'value3' => $detail->qty_sj,
                // 'value4' => $detail->subtotal_sj,

                $countItem = TempTransaction::select(DB::raw("COUNT(*) AS angka"))
                                ->where([
                                    ['module', '=', 'delivery'],
                                    ['value1', '=' , $id],
                                    ['value2', '=', $idItem],
                                    ['value3', '=', $idSatuan]
                                ])
                                ->first();
                $count = $countItem->angka;

                if ($count > 0) {
                    $data = "failDuplicate";
                }
                else {

                    $listItem = new TempTransaction();
                    $listItem->module = 'delivery';
                    $listItem->value1 = $id;
                    $listItem->value2 = $idItem;
                    $listItem->value3 = $idSatuan;
                    $listItem->value4 = $qty;
                    $listItem->action = 'tambah';
                    $listItem->save();

                    $log = ActionLog::create([
                        'module' => 'Delivery Detail',
                        'action' => 'Simpan',
                        'desc' => 'Simpan Delivery Detail',
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

    public function UpdateDeliveryDetail(Request $request)
    {
        $data = "";
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDelivery');
            $idItem = $request->input('idItem');
            $idSatuan = $request->input('idSatuan');
            $idDetail = $request->input('idDetail');
            $qty = $request->input('qtyItem');
            $user = Auth::user()->user_name;

            $qty = str_replace(",", ".", $qty);

            if ($id == "") {
                $id = 'DRAFT';

                $listItem = DeliveryDetail::find($idDetail);
                $listItem->id_pengiriman = $id;
                $listItem->id_item = $idItem;
                $listItem->id_satuan = $idSatuan;
                $listItem->qty_item = $qty;
                $listItem->updated_by = $user;
                $listItem->save();

            }

            else {
                $listItem = TempTransaction::find($idDetail);
                $listItem->value1 = $id;
                $listItem->value2 = $idItem;
                $listItem->value3 = $idSatuan;
                $listItem->value4 = $qty;
                $listItem->updated_by = $user;
                if ($listItem->id_detail != null) {
                    $listItem->action = 'update';
                }
                $listItem->save();

            }

            $log = ActionLog::create([
                'module' => 'Delivery Detail',
                'action' => 'Update',
                'desc' => 'Update Delivery Detail',
                'username' => Auth::user()->user_name
            ]);
        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function SetDeliveryDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $id = $request->input('idDelivery');
            $idSo = $request->input('idSalesOrder');
            $user = Auth::user()->user_name;
            if ($id == "") {
                $id = 'DRAFT';
            }

            if ($id != "DRAFT") {
                $update = DB::table('temp_transaction')
                            ->where([
                                ['value1', '=', $id],
                                ['module', '=', "delivery"]
                            ])
                            ->update([
                                'action' => "hapus",
                                'deleted_by' => Auth::user()->user_name,
                                'deleted_at' => now()
                            ]);

                $detail = SalesOrderDetail::select(
                                                'sales_order_detail.id_item',
                                                'sales_order_detail.id_satuan',
                                                'sales_order_detail.qty_outstanding'
                                            )
                                            ->where([
                                                ['sales_order_detail.id_so', '=', $idSo],
                                                ['sales_order_detail.qty_outstanding', '>', 0]
                                            ])
                                            ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailDlv) {
                    $dataDetail = [
                        'module' => "delivery",
                        'value1' => $id,
                        'value2' => $detailDlv->id_item,
                        'value3' => $detailDlv->id_satuan,
                        'value4' => $detailDlv->qty_outstanding,
                        'action' => "tambah",
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                TempTransaction::insert($listDetail);
            }
            else {
                $delete = DB::table('delivery_detail')
                            ->where('id_pengiriman', '=', $id)
                            ->when($id == "DRAFT", function($q) use ($user) {
                                $q->where('delivery_detail.created_by', $user);
                            })
                            ->delete();

                $detail = SalesOrderDetail::select(
                                                'sales_order_detail.id_item',
                                                'sales_order_detail.id_satuan',
                                                'sales_order_detail.qty_outstanding'
                                            )
                                            ->where([
                                                ['sales_order_detail.id_so', '=', $idSo],
                                                ['sales_order_detail.qty_outstanding', '>', 0]
                                            ])
                                            ->get();
                $data = $detail;
                $listDetail = [];
                foreach ($detail As $detailDlv) {

                    $dataDetail = [
                        'id_pengiriman' => $id,
                        'id_item' => $detailDlv->id_item,
                        'id_satuan' => $detailDlv->id_satuan,
                        'qty_item' => $detailDlv->qty_outstanding,
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name,
                    ];
                    array_push($listDetail, $dataDetail);
                }
                DeliveryDetail::insert($listDetail);
            }
        });

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function GetDeliveryDetail(Request $request)
    {
        $id = $request->input('idDelivery');
        $idSo = $request->input('idSalesOrder');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            if ($id == "") {
                $id = 'DRAFT';
            }

            $detail = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                    ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                    ->leftJoin('sales_order_detail', function($join) {
                                        $join->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                        $join->on('sales_order_detail.id_satuan', '=', 'delivery_detail.id_satuan');
                                    })
                                    ->select(
                                        'delivery_detail.id',
                                        'delivery_detail.id_item',
                                        'delivery_detail.id_satuan',
                                        'delivery_detail.qty_item',
                                        'sales_order_detail.qty_item as qty_order',
                                        'sales_order_detail.qty_outstanding',
                                        'sales_order_detail.keterangan',
                                        'product.kode_item',
                                        'product.nama_item',
                                        'product_unit.nama_satuan'
                                    )
                                    ->where([
                                        ['delivery_detail.id_pengiriman', '=', $id],
                                        ['sales_order_detail.id_so', '=', $idSo]
                                    ])
                                    ->when($id == "DRAFT", function($q) use ($user) {
                                        $q->where('delivery_detail.created_by', $user);
                                    })
                                    ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('sales_order_detail', function($join) {
                                        $join->on('sales_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('sales_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'sales_order_detail.qty_item as qty_order',
                                            'sales_order_detail.qty_outstanding',
                                            'sales_order_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'delivery'],
                                            ['sales_order_detail.id_so', '=', $idSo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function confirm(Request $request)
    {
        $data = new stdClass();
        $penerima = $request->input('namaPenerima');
        $tanggal = $request->input('tanggal');

        $exception = DB::transaction(function () use ($request, &$data, $penerima, $tanggal) {
            $id = $request->input('idDelivery');


            $dataDlv = Delivery::find($id);
            $dataDlv->diterima_oleh = $penerima;
            $dataDlv->tanggal_kirim = now();
            $dataDlv->flag_terkirim = '1';
            $dataDlv->updated_by = Auth::user()->user_name;
            $dataDlv->save();

            $data = $dataDlv;
        });

        if(is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }
    }

    public function EditDeliveryDetail(Request $request)
    {
        $id = $request->input('idDetail');
        $idSo = $request->input('idSalesOrder');
        $mode = $request->input('mode');

        if ($mode == "") {

            $detail = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                        ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                        ->leftJoin('sales_order_detail', function($join) {
                                            $join->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                            $join->on('sales_order_detail.id_satuan', '=', 'delivery_detail.id_satuan');
                                        })
                                        ->select(
                                            'delivery_detail.id',
                                            'delivery_detail.id_item',
                                            'delivery_detail.id_satuan',
                                            'delivery_detail.qty_item',
                                            'sales_order_detail.qty_outstanding',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['delivery_detail.id', '=', $id],
                                            ['sales_order_detail.id_so', '=', $idSo]
                                        ])
                                        ->get();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                    ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                    ->leftJoin('sales_order_detail', function($join) {
                                        $join->on('sales_order_detail.id_item', '=', 'temp_transaction.value2');
                                        $join->on('sales_order_detail.id_satuan', '=', 'temp_transaction.value3');
                                    })
                                        ->select(
                                            'temp_transaction.id',
                                            'temp_transaction.value2',
                                            'temp_transaction.value3',
                                            'temp_transaction.value4',
                                            'sales_order_detail.qty_item as qty_order',
                                            'sales_order_detail.qty_outstanding',
                                            'sales_order_detail.keterangan',
                                            'product.kode_item',
                                            'product.nama_item',
                                            'product_unit.nama_satuan'
                                        )
                                        ->where([
                                            ['temp_transaction.id', '=', $id],
                                            ['temp_transaction.module', '=', 'delivery'],
                                            ['sales_order_detail.id_so', '=', $idSo]
                                        ])
                                        ->get();
        }

        return response()->json($detail);
    }

    public function DeleteDeliveryDetail(Request $request)
    {
        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
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
                $delete = DB::table('delivery_detail')->where('id', '=', $id)->delete();
            }


        });

        if(is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }

    }

    public function GetDeliveryFooter(Request $request)
    {
        $id = $request->input('idDelivery');
        $mode = $request->input('mode');
        $user = Auth::user()->user_name;

        if ($mode != "edit") {
            $detail = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                            ->select(
                                                DB::raw('COALESCE(SUM(delivery_detail.qty_item),0) AS qtyItem'),
                                            )
                                            ->where([
                                                ['delivery_detail.id_pengiriman', '=', $id]
                                            ])
                                            ->when($id == "DRAFT", function($q) use ($user) {
                                                $q->where('delivery_detail.created_by', $user);
                                            })
                                            ->groupBy('delivery_detail.id_pengiriman')
                                            ->first();
        }
        else {
            $detail = TempTransaction::leftJoin('product', 'temp_transaction.value2', '=', 'product.id')
                                        ->leftJoin('product_unit', 'temp_transaction.value3', 'product_unit.id')
                                        ->select(
                                            DB::raw('COALESCE(SUM(temp_transaction.value4),0) AS qtyItem')
                                        )
                                        ->where([
                                            ['temp_transaction.value1', '=', $id],
                                            ['temp_transaction.module', '=', 'delivery']
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

    public function getTermsByOpt(Request $request)
    {
        $id = $request->input('idDelivery');
        $idSo = $request->input('idSalesOrder');
        $flag = $request->input('flagTerms');

        if ($flag == 0) {
            $dataTerms = DeliveryTerms::where('id_delivery', $id)->get();
        }
        else {
            $dataTerms = SalesOrderTerms::where('id_so', $idSo)->get();
        }

        return response()->json($dataTerms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer'=>'required',
            'salesOrder'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect('/Delivery')->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, &$data) {
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $idSalesOrder = $request->input('salesOrder');
            $tglSj = $request->input('tanggal_sj');
            $qtyKirim = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "use") {
                $flagTermsUsage = 1;
            }
            else {
                $flagTermsUsage = 0;
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyKirim = str_replace(",", ".", $qtyKirim);

            $blnPeriode = date("m", strtotime($tglSj));
            $thnPeriode = date("Y", strtotime($tglSj));
            $tahunPeriode = date("y", strtotime($tglSj));

            $countKode = DB::table('delivery')
                            ->select(DB::raw("MAX(RIGHT(kode_pengiriman,2)) AS angka"))
                            // ->whereMonth('tanggal_sj', $blnPeriode)
                            // ->whereYear('tanggal_sj', $thnPeriode)
                            ->whereDate('tanggal_sj', $tglSj)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSj)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSj))));

            if ($counter < 10) {
                $nmrDlv = "sj-tss-".$kodeTgl."0".$counter;
            }
            else {
                $nmrDlv = "sj-tss-".$kodeTgl.$counter;
            }

            $delivery = new Delivery();
            $delivery->kode_pengiriman = $nmrDlv;
            $delivery->id_so = $idSalesOrder;
            $delivery->id_alamat = $idAlamat;
            $delivery->jumlah_total_sj = $qtyKirim;
            $delivery->tanggal_sj = $tglSj;
            $delivery->status_pengiriman = 'draft';
            $delivery->flag_invoiced = '0';
            $delivery->flag_terkirim = '0';
            $delivery->flag_terms_so = $flagTermsUsage;
            $delivery->created_by = $user;
            $delivery->save();

            $data = $delivery;

            $setDetail = DB::table('delivery_detail')
                            ->where([
                                        ['id_pengiriman', '=', 'DRAFT'],
                                        ['created_by', '=', $user],
                                    ])
                            ->update([
                                'id_pengiriman' => $delivery->id,
                                'updated_by' => $user
                            ]);

            if ($flagTermsUsage == 0) {
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_delivery' => $delivery->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    DeliveryTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Delivery',
                'action' => 'Simpan',
                'desc' => 'Simpan Delivery',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('Delivery.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/Delivery')->with('error', $exception);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer'=>'required',
            'salesOrder'=>'required',
            'id_alamat'=>'required',
            'tanggal_sj'=>'required',
        ]);

        $tglSj = $request->input('tanggal_sj');

        $bulanIndonesia = Carbon::parse($tglSj)->locale('id')->isoFormat('MMMM');

        //CekAksesPeriode
        $aksesTransaksi = Helper::cekAksesPeriode($tglSj);
        if (!$aksesTransaksi) {
            return redirect()->route('Delivery.edit', [$id])->with('danger', 'Transaksi gagal!. Akses Transaksi Bulan '.$bulanIndonesia.' belum dibuka!');
        }

        $data = new stdClass();
        $exception = DB::transaction(function () use ($request, $id, &$data) {
            $idCustomer = $request->input('customer');
            $idAlamat = $request->input('id_alamat');
            $idSalesOrder = $request->input('salesOrder');
            $tglSj = $request->input('tanggal_sj');
            $qtyKirim = $request->input('qtyTtl');
            $flagTerms = $request->input('terms_usage');
            $user = Auth::user()->user_name;

            if ($flagTerms == "use") {
                $flagTermsUsage = 1;
            }
            else {
                $flagTermsUsage = 0;
            }

            $termsRaw = trim($request->input('tnc'));
            $terms = explode("\n", $termsRaw);
            $terms = array_filter($terms, 'trim');
            $qtyKirim = str_replace(",", ".", $qtyKirim);


            $countKode = DB::table('delivery')
                            ->select(DB::raw("MAX(RIGHT(kode_pengiriman,2)) AS angka"))
                            // ->whereMonth('tanggal_sj', $blnPeriode)
                            // ->whereYear('tanggal_sj', $thnPeriode)
                            ->whereDate('tanggal_sj', $tglSj)
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tglSj)->format('ymd');
            $romawiBulan = strtolower(Helper::romawi(date("m", strtotime($tglSj))));

            if ($counter < 10) {
                $nmrDlv = "sj-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrDlv = "sj-cv-".$kodeTgl.$counter;
            }


            $delivery = Delivery::find($id);
            if ($tglSj != $delivery->tanggal_sj) {
                $delivery->kode_pengiriman = $nmrDlv;
            }
            $delivery->id_so = $idSalesOrder;
            $delivery->id_alamat = $idAlamat;
            $delivery->jumlah_total_sj = $qtyKirim;
            $delivery->tanggal_sj = $tglSj;
            $delivery->updated_by = $user;
            $delivery->flag_terms_so = $flagTermsUsage;
            $delivery->save();

            $data = $delivery;

            // $deletedDetail = DeliveryDetail::onlyTrashed()->where([['id_pengiriman', '=', $id]]);
            // $deletedDetail->forceDelete();

            $tempDetail = DB::table('temp_transaction')->where([
                                            ['module', '=', 'delivery'],
                                            ['value1', '=', $id],
                                            ['action', '!=' , null]
                                        ])
                                        ->get();
            if ($tempDetail != "") {
                foreach ($tempDetail as $detail) {
                    if ($detail->action == "update") {
                        $listItem = DeliveryDetail::find($detail->id_detail);
                        $listItem->id_pengiriman = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->updated_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "tambah") {
                        $listItem = new DeliveryDetail();
                        $listItem->id_pengiriman = $detail->value1;
                        $listItem->id_item = $detail->value2;
                        $listItem->id_satuan = $detail->value3;
                        $listItem->qty_item = $detail->value4;
                        $listItem->created_by = $user;
                        $listItem->save();
                    }
                    else if ($detail->action == "hapus") {
                        $delete = DB::table('delivery_detail')->where('id', '=', $detail->id_detail)->delete();
                    }
                }
            }

            $deleteTemp = DB::table('temp_transaction')
                                ->where([
                                    ['module', '=', 'delivery'],
                                    ['value1', '=', $id]
                                ])->delete();


            if ($flagTermsUsage == 0) {
                $delete = DB::table('delivery_terms')->where('id_delivery', '=', $delivery->id)->delete();
                if ($terms != "") {
                    $listTerms = [];
                    foreach ($terms as $tnc) {
                        $dataTerms = [
                            'id_delivery' => $delivery->id,
                            'terms_and_cond' => $tnc,
                            'created_at' => now(),
                            'created_by' => $user
                        ];
                        array_push($listTerms, $dataTerms);
                    }
                    DeliveryTerms::insert($listTerms);
                }
            }

            $log = ActionLog::create([
                'module' => 'Delivery',
                'action' => 'Update',
                'desc' => 'Update Delivery',
                'username' => Auth::user()->user_name
            ]);
        });

        if (is_null($exception)) {
            return redirect()->route('Delivery.Staging', [$data->id])->with('success', 'Data '.strtoupper($data->kode_pengiriman).' Telah Diubah!');
        }
        else {
            return redirect('/Delivery')->with('error', $exception);
        }
    }

    public function postAllocation(Request $request, $id)
    {
        $data = new stdClass();
        $btnAction = $request->input('submit_action');
        if ($btnAction == "ubah") {
            return redirect()->route('Delivery.edit', [$id]);
        }
        $dlv = Delivery::find($id);

        $exception = DB::transaction(function () use ($request, &$data, $id) {

            $delivery = Delivery::find($id);
            $arrayDetail = $request->input('isi');
            $arrayKeterangan = $request->input('isi2');
            $listAlokasi = [];
            $listDetail = [];
            $ttlAlokasi = 0;
            if ($arrayDetail != "") {
                $delete = DB::table('delivery_allocation')
                            ->where([
                                ['delivery_allocation.id_delivery', '=', $id]
                            ])
                            ->delete();
                foreach ($arrayDetail as $detilAlokasi) {
                    $dataAlokasi = [
                        'id_delivery' => $id,
                        'id_detail' => $detilAlokasi["idDetail"],
                        'id_item' => $detilAlokasi["idItem"],
                        'id_satuan' => $detilAlokasi["idSatuan"],
                        'qty_item' => $detilAlokasi["qty"],
                        'id_index' => $detilAlokasi["idIndex"],
                        'created_at' => now(),
                        'created_by' => Auth::user()->user_name
                    ];
                    array_push($listAlokasi, $dataAlokasi);

                    $ttlAlokasi = $ttlAlokasi + $detilAlokasi["qty"];
                }

                DeliveryAllocation::insert($listAlokasi);
            }

            if ($arrayKeterangan != "") {
                foreach ($arrayKeterangan as $detail) {
                    $dlvDetail = DeliveryDetail::find($detail["id_detail"]);
                    $dlvDetail->keterangan = $detail["keterangan"];
                    $dlvDetail->save();
                }
            }

            $log = ActionLog::create([
                'module' => 'Delivery',
                'action' => 'Alokasi',
                'desc' => 'Alokasi Delivery',
                'username' => Auth::user()->user_name
            ]);

        });

        if (is_null($exception)) {
            return redirect()->route('Delivery.Detail', [$dlv->id])->with('success', 'Data Alokasi '.strtoupper($dlv->kode_pengiriman).' Telah Disimpan!');
        }
        else {
            return redirect('/Delivery')->with('error', $exception);
        }
    }

    public function posting(Request $request, $id)
    {
        $data = new stdClass();
        $msg = "";
        $status = "";
        $exception = DB::transaction(function () use ($request, $id, &$data, &$msg, &$status) {
            $btnAction = $request->input('submit_action');
            $delivery = Delivery::find($id);

            $cekSjInvoiced = SalesInvoiceDetail::leftJoin('sales_invoice', 'sales_invoice_detail.id_invoice', 'sales_invoice.id')
                                                    ->where([
                                                        ['sales_invoice_detail.id_sj', '=', $id],
                                                        ['sales_invoice.status_invoice', '!=', 'draft']
                                                    ])
                                                    ->count();
            if ($btnAction == "posting") {
                $detailDlv = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                            ->leftJoin('sales_order_detail', function($join) {
                                                $join->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                                $join->on('sales_order_detail.id_satuan', '=', 'delivery_detail.id_satuan');
                                            })
                                            ->select(
                                                'delivery_detail.id',
                                                'delivery_detail.id_item',
                                                'delivery_detail.id_satuan',
                                                'delivery_detail.qty_item',
                                                'sales_order_detail.qty_item as qty_order',
                                                'sales_order_detail.qty_outstanding',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan'
                                            )
                                            ->where([
                                                ['delivery_detail.id_pengiriman', '=', $id],
                                                ['sales_order_detail.id_so', '=', $delivery->id_so]
                                            ])
                                            ->get();
                $transaksi = [];
                $failedItem = [];
                foreach ($detailDlv As $detail) {
                    $detailOuts = SalesOrderDetail::where([
                                                    ['id_so', '=', $delivery->id_so],
                                                    ['id_item', '=', $detail->id_item],
                                                    ['id_satuan', '=', $detail->id_satuan]
                                                ])
                                                ->first();

                    $cekOuts = $detailOuts->qty_outstanding - $detail->qty_item;

                    if ($cekOuts < 0) {
                        $dataProduct = Product::find($detail->id_item);
                        array_push($failedItem, $dataProduct->nama_item);
                    }
                }

                $listDetail = [];
                $ttlAlokasi = 0;

                $alokasiDlv = DeliveryAllocation::where([
                                                    ['delivery_allocation.id_delivery', '=', $id]
                                                ])
                                                ->get();

                $transactionData = [];
                if ($alokasiDlv != "") {
                    foreach ($alokasiDlv as $detilAlokasi) {
                        // $dataStok = [
                        //     'kode_transaksi' => $delivery->kode_pengiriman,
                        //     'id_item' => $detilAlokasi->id_item,
                        //     'id_satuan' => $detilAlokasi->id_satuan,
                        //     'qty_item' => $detilAlokasi->qty_item,
                        //     'id_index' => $detilAlokasi->id_index,
                        //     'tgl_transaksi' => $delivery->tanggal_sj,
                        //     'jenis_transaksi' => "pengiriman",
                        //     'transaksi' => "out",
                        //     'created_at' => now(),
                        //     'created_by' => Auth::user()->user_name,
                        // ];
                        // array_push($listDetail, $dataStok);

                        //$ttlAlokasi = $ttlAlokasi + $detilAlokasi->qty_item;

                        $stockTransaction = HelperDelivery::createStockTransaction2($id, $detilAlokasi);

                        if (count($stockTransaction) > 0 ) {
                            array_push($transactionData, $stockTransaction);
                            $errorSourceAssign = 0;
                        }
                        else {
                            $errorSourceAssign = 1;
                        }

                    }
                }

                if (count($failedItem) > 0) {
                    $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Gagal Diposting! Item ('.strtoupper(implode(', ', $failedItem)).')';
                    $status = 'warning';
                }
                elseif (count($alokasiDlv) < 1) {
                    $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Gagal Diposting! Lakukan Alokasi Pengiriman Terlebih dahulu!';
                    $status = 'warning';
                }
                elseif ($errorSourceAssign == 1) {
                    $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Gagal Diposting! Terdapat Masalah saat pemilihan sumber stok barang!';
                    $status = 'warning';
                }
                else {
                    foreach ($detailDlv As $detail) {
                        $detailOuts = SalesOrderDetail::where([
                                                        ['id_so', '=', $delivery->id_so],
                                                        ['id_item', '=', $detail->id_item]
                                                    ])
                                                    ->first();

                        $cekOuts = $detailOuts->qty_outstanding - $detail->qty_item;

                        if ($cekOuts >= 0) {
                            $detailOuts->qty_outstanding = $detailOuts->qty_outstanding - $detail->qty_item;
                            $detailOuts->save();
                        }
                    }

                    foreach ($transactionData as $dataSJ) {
                        StockTransaction::insert($dataSJ);
                    }

                    $totalOuts = SalesOrder::where([
                                                    ['id', '=', $delivery->id_so],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_so = $totalOuts->outstanding_so - $delivery->jumlah_total_sj;
                    if ($totalOuts->outstanding_so == 0) {
                        $totalOuts->status_so = 'full';
                    }

                    $delivery->status_pengiriman = "posted";
                    $delivery->save();

                    $totalOuts->save();

                    $log = ActionLog::create([
                        'module' => 'Delivery',
                        'action' => 'Posting',
                        'desc' => 'Posting Delivery',
                        'username' => Auth::user()->user_name
                    ]);
                    $msg = 'Data '.strtoupper($delivery->no_so).' Telah Diposting!';
                    $status = 'success';
                }
            }
            elseif ($btnAction == "ubah") {
                $status = "ubah";
            }
            elseif ($btnAction == "revisi") {
                // if ($delivery->flag_terkirim == 1) {
                //     $msg = 'Pengiriman '.strtoupper($delivery->kode_pengiriman).' Tidak dapat Direvisi karena Pengiriman Barang No. '.strtoupper($delivery->kode_pengiriman).' Telah diterima Pelanggan!';
                //     $status = "warning";
                // }
                if ($cekSjInvoiced == 0) {
                    $delivery->status_pengiriman = "draft";
                    $delivery->flag_revisi = '1';
                    $delivery->updated_by = Auth::user()->user_name;
                    $delivery->save();

                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $delivery->kode_pengiriman)->delete();

                    $detailRcv = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                ->leftJoin('sales_order_detail', function($join) {
                                                    $join->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                                    $join->on('sales_order_detail.id_satuan', '=', 'delivery_detail.id_satuan');
                                                })
                                                ->select(
                                                    'delivery_detail.id',
                                                    'delivery_detail.id_item',
                                                    'delivery_detail.id_satuan',
                                                    'delivery_detail.qty_item',
                                                    'sales_order_detail.qty_outstanding',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan'
                                                )
                                                ->where([
                                                    ['delivery_detail.id_pengiriman', '=', $id],
                                                    ['sales_order_detail.id_so', '=', $delivery->id_so]
                                                ])
                                                ->get();

                    foreach ($detailRcv As $detail) {

                        $detailOuts = SalesOrderDetail::where([
                                                            ['id_so', '=', $delivery->id_so],
                                                            ['id_item', '=', $detail->id_item],
                                                            ['id_satuan', '=', $detail->id_satuan]
                                                        ])
                                                        ->first();

                        $detailOuts->qty_outstanding = $detailOuts->qty_outstanding + $detail->qty_item;
                        $detailOuts->save();

                    }

                    $totalOuts = SalesOrder::where([
                                                    ['id', '=', $delivery->id_so],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_so = $totalOuts->outstanding_so + $delivery->jumlah_total_sj;
                    if ($totalOuts->outstanding_so == 0) {
                        $totalOuts->status_so = 'full';
                    }
                    else {
                        $totalOuts->status_so = 'posted';
                    }
                    $totalOuts->save();

                    $log = ActionLog::create([
                        'module' => 'Pengiriman Barang',
                        'action' => 'Revisi',
                        'desc' => 'Revisi Pengiriman Barang',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Telah Direvisi!';
                    $status = 'success';
                }
                else {
                    $msg = 'Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' Tidak dapat Direvisi karena terdapat Invoice Penjualan atas Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' !';
                    $status = "warning";
                }
            }
            elseif ($btnAction == "batal") {
                if ($cekSjInvoiced == 0) {
                    $delivery->status_pengiriman = "batal";
                    $delivery->updated_by = Auth::user()->user_name;
                    $delivery->save();

                    $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $delivery->kode_pengiriman)->delete();

                    $detailRcv = DeliveryDetail::leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                                ->leftJoin('product_unit', 'delivery_detail.id_satuan', 'product_unit.id')
                                                ->leftJoin('sales_order_detail', function($join) {
                                                    $join->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                                    $join->on('sales_order_detail.id_satuan', '=', 'delivery_detail.id_satuan');
                                                })
                                                ->select(
                                                    'delivery_detail.id',
                                                    'delivery_detail.id_item',
                                                    'delivery_detail.id_satuan',
                                                    'delivery_detail.qty_item',
                                                    'sales_order_detail.qty_outstanding',
                                                    'product.kode_item',
                                                    'product.nama_item',
                                                    'product_unit.nama_satuan'
                                                )
                                                ->where([
                                                    ['delivery_detail.id_pengiriman', '=', $id],
                                                    ['sales_order_detail.id_so', '=', $delivery->id_so]
                                                ])
                                                ->get();

                    foreach ($detailRcv As $detail) {

                        $detailOuts = SalesOrderDetail::where([
                                                            ['id_so', '=', $delivery->id_so],
                                                            ['id_item', '=', $detail->id_item],
                                                            ['id_satuan', '=', $detail->id_satuan]
                                                        ])
                                                        ->first();

                        $detailOuts->qty_outstanding = $detailOuts->qty_outstanding + $detail->qty_item;
                        $detailOuts->save();

                    }

                    $totalOuts = SalesOrder::where([
                                                    ['id', '=', $delivery->id_so],
                                                ])
                                                ->first();

                    $totalOuts->outstanding_so = $totalOuts->outstanding_so + $delivery->jumlah_total_sj;
                    if ($totalOuts->outstanding_so == 0) {
                        $totalOuts->status_so = 'full';
                    }
                    else {
                        $totalOuts->status_so = 'posted';
                    }
                    $totalOuts->save();

                    $log = ActionLog::create([
                        'module' => 'Pengiriman',
                        'action' => 'Batal',
                        'desc' => 'Batal Pengiriman Barang',
                        'username' => Auth::user()->user_name
                    ]);

                    $msg = 'Pengiriman '.strtoupper($delivery->kode_pengiriman).' Telah Dibatalkan!';
                    $status = "success";
                }
                else {
                    $msg = 'Pengiriman '.strtoupper($delivery->kode_pengiriman).' Tidak dapat Dibatalkan karena terdapat Invoice Penjualan atas Pengiriman Barang '.strtoupper($delivery->kode_pengiriman).' !';
                    $status = "warning";
                }
            }
            else {
                $status = "ubahStaging";
            }
        });

        if (is_null($exception)) {
            if ($status == "ubah") {
                return redirect()->route('Delivery.edit', [$id]);
            }
            elseif ($status == "ubahStaging") {
                return redirect()->route('Delivery.Staging', [$id]);
            }
            else {
                return redirect()->back()->with($status, $msg);
            }
        }
        else {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function ResetDeliveryDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->input('idSJ');


            if ($id != "DRAFT") {
                // $detail = DeliveryDetail::where([
                //                             ['id_pengiriman', '=' ,$id]
                //                         ])
                //                         ->update([
                //                             'deleted_at' => now(),
                //                             'deleted_by' => Auth::user()->user_name
                //                         ]);
                $deleteTemp = TempTransaction::where([
                                                ['module', '=', 'delivery'],
                                                ['value1', '=', $id]
                                            ])
                                            ->update([
                                                'action' => 'hapus',
                                                'deleted_at' => now(),
                                                'deleted_by' => Auth::user()->user_name
                                            ]);
            }
            else {
                $delete = DB::table('delivery_detail')->where('id_pengiriman', '=', $id)->delete();
            }
        });

        if (is_null($exception)) {
            return response()->json("success");
        }
        else {
            return response()->json($exception);
        }
    }

    public function getProductDetail(Request $request)
    {
        $idProduct = $request->input('idProduct');
        $idSo = $request->input('idSo');

        $detail = ProductDetail::leftJoin('product_unit', 'product_detail.id_satuan', '=', 'product_unit.id')
                                ->select(
                                    'product_unit.id',
                                    'product_unit.kode_satuan',
                                    'product_unit.nama_satuan',
                                )
                                ->where([
                                    ['product_detail.id_product', '=', $idProduct]
                                ])
                                ->whereIn('product_detail.id_satuan', function($subQuery) use ($idProduct, $idSo) {
                                    $subQuery->select('id_satuan')->from('sales_order_detail')
                                    ->where([
                                        ['id_so', '=', $idSo],
                                        ['id_item', '=', $idProduct]
                                    ]);
                                })
                                ->get();

        return response()->json($detail);
    }

    public function exportDataDelivery(Request $request)
    {
        $kodeTgl = Carbon::now()->format('ymd');
        return Excel::download(new DeliveryExport($request), 'Delivery_'.$kodeTgl.'.xlsx');
    }

    public function getStockItem(Request $request)
    {
        $idProduct = $request->input('id_item');
        $idIndex = $request->input('id_index');
        $idSatuan = $request->input('id_satuan');


        $stokIn = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in'],
                                                ['id_index', '=', $idIndex],
                                                ['id_satuan', '=', $idSatuan]
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

        $stokOut = StockTransaction::select('id_item', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out'],
                                        ['id_index', '=', $idIndex],
                                        ['id_satuan', '=', $idSatuan]
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_satuan');

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

        $stokIn = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_in'))
                                    ->where([
                                                ['transaksi', '=', 'in']
                                            ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_index');

        $stokOut = StockTransaction::select('id_item', 'id_index', DB::raw('SUM(qty_item) AS stok_out'))
                                    ->where([
                                        ['transaksi', '=', 'out']
                                    ])
                                    ->groupBy('id_item')
                                    ->groupBy('id_index');

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
                            ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                                $join_in->on('product.id', '=', 'stokIn.id_item');
                            })
                            ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                                $join_out->on('product.id', '=', 'stokOut.id_item');
                                $join_out->on('stokIn.id_index', '=', 'stokOut.id_index');
                            })
                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                            })
                            ->select('product.id',
                                'product.kode_item',
                                'product.nama_item',
                                'product.jenis_item',
                                'product_brand.nama_merk',
                                'product_category.nama_kategori',
                                'stokIn.id_index',
                                'dataSpek.value_spesifikasi'
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
                if ($dataStock->id_index != null) {
                    array_push($listIndex, $indexList);
                }
            }
        }

        if (count($listIndex) < 1) {
            $listIndex = $list;
        }

        return response()->json($listIndex);
    }
}
