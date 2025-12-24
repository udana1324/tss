<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Library\Customer;
use App\Models\ActionLog;
use App\Classes\BusinessManagement\SetMenu;
use App\Exports\ReportSalesDetailExport;
use App\Models\Library\CustomerGroup;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Setting\Module;

class SalesReportDetailController extends Controller
{
    public function index()
    {
        if (Auth::check()) {

            $countAkses = DB::table('module')
                                    ->join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select('*')
                                    ->where([
                                                ['module.url', '=', '/ReportSalesDetail'],
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
                                                ['module.url', '=', '/ReportSalesDetail'],
                                                ['module_access.user_id', '=', Auth::user()->id]
                                            ])
                                    ->first();

                $dataCustomer = Customer::distinct()->get();
                $dataGroup = CustomerGroup::distinct()->orderBy('nama_group', 'asc')->get();

                $data['hakAkses'] = $hakAkses;
                $data['dataCustomer'] = $dataCustomer;
                $data['dataGroup'] = $dataGroup;
                $parentMenu = Module::find($hakAkses->parent);

                $data['parent'] = "parent".ucwords($parentMenu->menu);

                $log = ActionLog::create([
                    'module' => 'Ssales Report Detail',
                    'action' => 'Tampil',
                    'desc' => 'Tampilan Ssales Report Detail',
                    'username' => Auth::user()->user_name
                ]);

                return view('pages.report.reportSalesDetail', $data);
            }
            else {
                return redirect('/')->with('warning', 'Anda tidak memiliki Hak Akses untuk Halaman tersebut!');
            }
        }
        else {
            return redirect('/');
        }
    }

    public function getDataSalesReportDetail(Request $request)
    {
        $idCustomer = $request->input('idCustomer');
        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $jenis = $request->input('jenis');
        $grup = $request->input('grup');

        $transaction = "";

        if ($jenisPeriode != null) {
            if ($idCustomer == "All") {
                $idCustomer = "";
            }

            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                    ->select(
                                        'product_detail_specification.id_product',
                                        'product_detail_specification.value_spesifikasi'
                                    )
                                    ->where([
                                        ['product_specification.kode_spesifikasi', '=', 'spn'],
                                    ]);

            $transaction = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('delivery', 'sales_invoice_detail.id_sj', '=', 'delivery.id')
                                            ->leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', '=', 'delivery.id')
                                            ->leftJoin('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('product', 'delivery_detail.id_item', '=', 'product.id')
                                            ->leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                                            ->leftJoin('sales_order_detail', function($join) {
                                                $join->on('delivery_detail.id_item', '=', 'sales_order_detail.id_item');
                                                $join->on('delivery_detail.id_satuan', '=', 'sales_order_detail.id_satuan');
                                                $join->on('sales_order.id', '=', 'sales_order_detail.id_so');
                                            })
                                            ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                                $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                            })
                                            ->leftJoin('product_unit', 'sales_order_detail.id_satuan', '=', 'product_unit.id')
                                            ->select(
                                                'delivery.kode_pengiriman',
                                                'sales_order.no_so',
                                                'sales_invoice.kode_invoice',
                                                'sales_invoice.tanggal_invoice',
                                                'delivery_detail.qty_item',
                                                'product.kode_item',
                                                'product.nama_item',
                                                'product_unit.nama_satuan',
                                                'product_category.nama_kategori',
                                                'sales_order_detail.harga_jual',
                                                'customer.nama_customer',
                                                'dataSpek.value_spesifikasi'
                                            )
                                            ->where([
                                                ['sales_invoice.status_invoice', '=', 'posted']
                                            ])
                                            ->when($jenis == "customer" && $idCustomer != "", function($q) use ($idCustomer) {
                                                $q->where('customer.id', '=', $idCustomer);
                                            })
                                            ->when($jenis == "grup" && $grup != "", function($q) use ($grup) {
                                                $q->whereIn('customer.id', function($subQuery) use ($grup) {
                                                    $subQuery->select('id_customer')->from('customer_group_detail')
                                                    ->where('id_group', '=', $grup);
                                                });
                                            })
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
                                            ->orderBy('sales_invoice.tanggal_invoice', 'desc')
                                            ->get();
        }

        return response()->json($transaction);
    }

    public function exportDataSalesDetailReport(Request $request)
    {
        return Excel::download(new ReportSalesDetailExport($request), 'ReportSalesDetail.xlsx');
    }
}
