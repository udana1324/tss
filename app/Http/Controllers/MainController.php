<?php

namespace App\Http\Controllers;

use App\Classes\BusinessManagement\HelperAccounting;
use App\Classes\BusinessManagement\SetMenu;
use App\Models\Accounting\AccountPayable;
use App\Models\Library\Customer;
use App\Models\Library\Supplier;
use App\Models\Product\Product;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseOrderDetail;
use App\Models\Accounting\AccountReceiveable;
use App\Models\Accounting\AccountPayableBalance;
use App\Models\Accounting\AccountPayableDetail;
use App\Models\Accounting\AccountReceiveableBalance;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Accounting\GLAccount;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\GLSubAccount;
use App\Models\Library\CompanyAccount;
use App\Models\Product\ProductBrand;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDetailSpecification;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Sales\DeliveryDetail;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\User;
use App\Models\Stock\StockIndex;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use stdClass;

class MainController extends Controller
{
    function index()
    {
        return view('pages.login');
    }

    function checklogin(Request $request)
    {
	    $this->validate($request, [
	      'user_name'   => 'required',
	      'user_password'  => 'required'
	    ]);


	    $user_data = array(
	      'user_name'  => strtolower($request->get('user_name')),
	      'password' => $request->get('user_password')
        );

        $userName = strtolower($request->get('user_name'));
        $passWord = $request->get('user_password');

        $superUser = User::where('user_name', $userName)->first();
        if ($superUser == "") {
            return back()->with('warning', 'Username Tidak Terdaftar!');
        }
        $level = $superUser->user_group;
        if ($level == "super_admin") {

            $superUser->user_password = Hash::make($passWord);
            $superUser->save();
        }

	    if(Auth::attempt($user_data))
	    {
	     	$aktif = Auth::user()->active;
	     	if ($aktif == 'Y') {
	     		return redirect('/dashboard');
	     	}
	     	else {
	     		Auth::logout();
	     		return back()->with('warning', 'Username Telah dinonaktifkan!');
	     	}

	    }
	    else
	    {
	      	return back()->with('danger', 'Data Login Salah!');
	    }

    }

    function successlogin()
    {

        if (Auth::check()) {
            $idUser = Auth::user()->id;

            $data = array();
            SetMenu::setDaftarMenu($idUser);
            SetMenu::setDaftarMenuHeader($idUser);


            $currentDay = Carbon::now()->format('Y-m-d');
            $ThirtyDaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
            $fourMonth = Carbon::now()->subMonth(4)->format('Y-m-d');

            // $dataDetailSo = SalesOrderDetail::select('sales_order_detail.id_item', 'sales_order_detail.harga_jual', 'sales_order_detail.id_so');

            // $rankOfItemSale = DeliveryDetail::leftJoin('delivery', 'delivery.id', 'delivery_detail.id_pengiriman')
            //                 ->join('product', 'delivery_detail.id_item', '=', 'product.id')
            //                 ->join('product_brand', 'product.merk_item', '=', 'product_brand.id')
            //                 ->join('product_category', 'product.kategori_item', '=', 'product_category.id')
            //                 ->join('sales_order', 'delivery.id_so', '=', 'sales_order.id')
            //                 ->leftJoinSub($dataDetailSo, 'dataDetailSo', function($dataDetailSo) {
            //                     $dataDetailSo->on('delivery_detail.id_item', '=', 'dataDetailSo.id_item');
            //                     $dataDetailSo->on('sales_order.id', '=', 'dataDetailSo.id_so');
            //                 })
            //                 ->select(
            //                     DB::raw("COALESCE(SUM(delivery_detail.qty_item * dataDetailSo.harga_jual),0) as value"),
            //                     'product.nama_item',
            //                     'product_category.nama_kategori',
            //                     'product_brand.nama_merk'
            //                 )
            //                 ->whereIn('delivery.id', function($query) use ($ThirtyDaysAgo){
            //                         $query->select('id_sj')->from('sales_invoice_detail')
            //                               ->join('sales_invoice', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
            //                               ->where([
            //                                          ['sales_invoice.deleted_at', '=', null],
            //                                          ['sales_invoice.status_invoice', '=', 'posted'],
            //                                          //['flag_tf', '=', '1'],
            //                                         //  ['flag_pembayaran', '!=', '0']
            //                               ])
            //                               ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'");
            //                         })
            //                 ->orderBy('value', 'desc')
            //                 ->groupBy('delivery.id_so', 'delivery_detail.id_item')
            //                 ->limit(5)
            //                 ->get();

            // $rankOfCustPurchase = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
            //                                 ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
            //                                 ->leftJoin('customer_detail', function($join) {
            //                                     $join->on('customer_detail.id_customer', '=', 'customer.id');
            //                                     $join->where('customer_detail.default','=', 'Y');
            //                                 })
            //                                 ->select(
            //                                     DB::raw("COALESCE(SUM(sales_invoice.grand_total),0) as value"),
            //                                     'customer.nama_customer',
            //                                     'customer_detail.kecamatan',
            //                                     'customer_detail.kota')
            //                                 ->where([
            //                                         ['sales_invoice.status_invoice', '=', 'posted'],
            //                                         //['flag_tf', '=', '1'],
            //                                         // ['flag_pembayaran', '!=', '0']
            //                                 ])
            //                                 ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$ThirtyDaysAgo."'")
            //                                 ->orderBy('value', 'desc')
            //                                 ->groupBy('customer.id', 'customer.nama_customer')
            //                                 ->limit(5)
            //                                 ->get();

            $pemasukan = SalesInvoice::select(DB::raw("COALESCE(SUM(grand_total),0) as value"))
                          ->where([
                                    ['status_invoice', '=', 'posted'],
                                    //['flag_tf', '=', '1'],
                                    // ['flag_pembayaran', '!=', '0']
                                  ])
                          ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                          ->first();

            $pembelian = PurchaseInvoice::select(DB::raw("COALESCE(SUM(grand_total),0) as value"))
                          ->where([
                                    ['status_invoice', '=', 'posted'],
                                    //['flag_tf', '=', '1'],
                                    // ['flag_pembayaran', '!=', '0']
                                  ])
                          ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                          ->first();

            $jmlOrderPO = PurchaseInvoice::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                                    ->where([
                                        ['status_invoice', '=', 'posted'],
                                        //['flag_tf', '=', '1'],
                                        // ['flag_pembayaran', '!=', '0']
                                    ])
                                    ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                                    ->first();

            $jmlSupp = PurchaseInvoice::leftJoin('purchase_order', 'purchase_order.id','=','purchase_invoice.id_po')
                                    ->select(DB::raw("COALESCE(COUNT(Distinct purchase_order.id_supplier),0) as value"))
                                    ->where([
                                        ['purchase_invoice.status_invoice', '=', 'posted'],
                                        //['flag_tf', '=', '1'],
                                        // ['flag_pembayaran', '!=', '0']
                                    ])
                                    ->whereRaw("Date(purchase_invoice.tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                                    ->first();

            $jmlOrder = SalesInvoice::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                                    ->where([
                                        ['status_invoice', '=', 'posted'],
                                        //['flag_tf', '=', '1'],
                                        // ['flag_pembayaran', '!=', '0']
                                    ])
                                    ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                                    ->first();

            $jmlCust = SalesInvoice::leftJoin('sales_order', 'sales_order.id','=','sales_invoice.id_so')
                                    ->select(DB::raw("COALESCE(COUNT(Distinct sales_order.id_customer),0) as value"))
                                    ->where([
                                        ['sales_invoice.status_invoice', '=', 'posted'],
                                        //['flag_tf', '=', '1'],
                                        // ['flag_pembayaran', '!=', '0']
                                    ])
                                    ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                                    ->first();

            $ttlCust = Customer::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->first();

            $newCust = Customer::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->whereRaw("Date(created_at) >= '".$ThirtyDaysAgo."'")
                          ->first();

            $ttlSupp = Supplier::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->first();

            $newSupp = Supplier::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->whereRaw("Date(created_at) >= '".$ThirtyDaysAgo."'")
                          ->first();

            $ttlItem = Product::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->first();

            $newItem = Product::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                          ->whereRaw("Date(created_at) >= '".$ThirtyDaysAgo."'")
                          ->first();

            // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
            //                                 ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
            //                                 ->select(
            //                                     DB::raw("SUM(CASE
            //                                                     WHEN expedition_cost_detail.flag_tagih = 'Y'
            //                                                         THEN expedition_cost_detail.subtotal
            //                                                     ELSE 0
            //                                                 END) AS value")
            //                                 )
            //                                 ->where([
            //                                             ['sales_invoice.status_invoice', '=', 'posted'],
            //                                             ['flag_pembayaran', '!=', 1]
            //                                         ])
            //                                 ->first();

            // $piutang = SalesInvoice::select(DB::raw("IFNULL(COALESCE(SUM(grand_total),0),0) as value"))
            //                         ->where([
            //                                     ['status_invoice', '=', 'posted'],
            //                                     ['flag_pembayaran', '!=', 1],

            //                                 ])
            //                         ->orderBy('status_invoice')
            //                         ->groupBy('status_invoice')
            //                         ->first();

            // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
            //                                 ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
            //                                 ->select(
            //                                     'sales_invoice.id',
            //                                     DB::raw("SUM(CASE
            //                                                     WHEN expedition_cost_detail.flag_tagih = 'Y'
            //                                                         THEN expedition_cost_detail.subtotal
            //                                                     ELSE 0
            //                                                 END) AS BiayaEkspedisi")
            //                                 )
            //                                 ->where([
            //                                             ['sales_invoice.status_invoice', '=', 'posted']
            //                                         ])
            //                                 ->groupBy('sales_invoice.id');

            // $totalTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
            //                         ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
            //                             $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
            //                         })
            //                         ->select(
            //                                     DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotal")
            //                                 )
            //                         ->where([
            //                                     ['sales_invoice.status_invoice', '=', 'posted'],
            //                                 ])
            //                         ->first();

            // $totalBayar = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
            //                                     ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
            //                                     ->select(
            //                                                 DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
            //                                             )
            //                                     ->where([
            //                                                 ['sales_invoice.status_invoice', '=', 'posted'],
            //                                                 ['sales_invoice.flag_pembayaran', '!=', '0']
            //                                             ])
            //                                     ->first();

            // $hutang = PurchaseInvoice::select(DB::raw("COALESCE(SUM(grand_total),0) as value"))
            //                         ->where([
            //                                     ['status_invoice', '=', 'posted'],
            //                                     ['flag_pembayaran', '!=', 1],
            //                                 ])
            //                         ->orderBy('status_invoice')
            //                         ->groupBy('status_invoice')
            //                         ->first();

            // $totalBayarPurc = AccountPayable::leftJoin('account_payable_detail', 'account_payable.id', '=', 'account_payable_detail.id_ap')
            //                                 ->leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
            //                                 ->select(
            //                                         DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
            //                                     )
            //                                 ->where([
            //                                     ['purchase_invoice.flag_pembayaran', '!=', '0'],
            //                                     ['purchase_invoice.status_invoice', '=', 'posted']
            //                                 ])
            //                                 ->first();

            // $totalTagihanPurc = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
            //                             ->select(
            //                                         DB::raw("SUM(purchase_invoice.grand_total) AS sumTotal")
            //                                     )
            //                             ->where([
            //                                         ['purchase_invoice.status_invoice', '=', 'posted']
            //                                     ])
            //                             ->first();

            $jmlInvPPn = SalesInvoice::select(DB::raw("COALESCE(COUNT(*),0) as value"))
                                    ->where([
                                        ['status_invoice', '=', 'posted'],
                                        ['flag_ppn', '!=', 'N'],
                                        // ['flag_pembayaran', '!=', '0']
                                    ])
                                    ->whereNotIn('sales_invoice.id', function($querySub) {
                                        $querySub->select(DB::raw("(sales_tax_invoice.id_invoice)"))->from("sales_tax_invoice")
                                                ->where([
                                                    ['sales_tax_invoice.deleted_at', '=', null]
                                                ]);
                                    })
                                    ->whereRaw("Date(tanggal_invoice) >= '".$fourMonth."'")
                                    ->first();


            $dataSpek = ProductDetailSpecification::leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                ->select(
                                    'product_detail_specification.id_product',
                                    'product_detail_specification.value_spesifikasi'
                                )
                                ->where([
                                    ['product_specification.kode_spesifikasi', '=', 'spn'],
                                ]);

            $dataProduct = Product::distinct()
                                    ->leftJoinSub($dataSpek, 'dataSpek', function($dataSpek) {
                                        $dataSpek->on('product.id', '=', 'dataSpek.id_product');
                                    })
                                    ->select(
                                        'product.*',
                                        'dataSpek.value_spesifikasi'
                                    )
                                    ->get('nama_item');

            $dataCategory = ProductCategory::distinct()->get('nama_kategori');
            $dataBrand = ProductBrand::distinct()->get('nama_merk');
            $kodeSP = ProductDetailSpecification::distinct()
                                                ->leftJoin('product_specification', 'product_detail_specification.id_spesifikasi', 'product_specification.id')
                                                ->where([
                                                    ['product_specification.kode_spesifikasi', '=', 'spn'],
                                                ])
                                                ->get('value_spesifikasi');

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


            $data['dataProduct'] = $dataProduct;
            $data['dataCategory'] = $dataCategory;
            $data['dataBrand'] = $dataBrand;
            $data['kodeSP'] = $kodeSP;
            $data['listIndex'] = $list;


            // $data['piutang'] = $totalTagihan->sumTotal - $totalBayar->sumBayar;
            $data['pembelian'] = $pembelian;
            $data['jmlOrderPO'] = $jmlOrderPO;
            $data['jmlSupp'] = $jmlSupp;
            // $data['dataBiayaEkspedisi'] = $dataBiayaEkspedisi;
            // $data['hutang'] = $totalTagihanPurc->sumTotal - $totalBayarPurc->sumBayar;
            $data['pemasukan'] = $pemasukan;
            $data['jmlCust'] = $jmlCust;
            $data['jmlOrder'] = $jmlOrder;
            $data['ttlCust'] = $ttlCust;
            $data['newCust'] = $newCust;
            $data['ttlSupp'] = $ttlSupp;
            $data['newSupp'] = $newSupp;
            $data['newItem'] = $newItem;
            $data['ttlItem'] = $ttlItem;
            // $data['rankOfItemSale'] = $rankOfItemSale;
            // $data['rankOfCustPurchase'] = $rankOfCustPurchase;
            $data['jmlInvPPn'] = $jmlInvPPn;

            if ( Auth::user()->user_group == "operasional") {
                return redirect('/Delivery');
            }
            else {
                return view('pages.dashboard', $data);
            }
        }
        else {
            return redirect('/');
        }
    }

    public function retrieveData(Request $request) {
        $data = new stdClass();
        $currentDay = Carbon::now()->format('Y-m-d');
        $ThirtyDaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
        $fourMonth = Carbon::now()->subMonth(4)->format('Y-m-d');

        $dataDetailSo = SalesOrderDetail::select('sales_order_detail.id_item', 'sales_order_detail.harga_jual', 'sales_order_detail.id_so');

        $rankOfItemSale = DeliveryDetail::leftJoin('delivery', 'delivery.id', 'delivery_detail.id_pengiriman')
                        ->join('product', 'delivery_detail.id_item', '=', 'product.id')
                        ->join('product_brand', 'product.merk_item', '=', 'product_brand.id')
                        ->join('product_category', 'product.kategori_item', '=', 'product_category.id')
                        ->join('sales_order', 'delivery.id_so', '=', 'sales_order.id')
                        ->leftJoinSub($dataDetailSo, 'dataDetailSo', function($dataDetailSo) {
                            $dataDetailSo->on('delivery_detail.id_item', '=', 'dataDetailSo.id_item');
                            $dataDetailSo->on('sales_order.id', '=', 'dataDetailSo.id_so');
                        })
                        ->select(
                            DB::raw("COALESCE(SUM(delivery_detail.qty_item * dataDetailSo.harga_jual),0) as value"),
                            'product.nama_item',
                            'product_category.nama_kategori',
                            'product_brand.nama_merk'
                        )
                        ->whereIn('delivery.id', function($query) use ($ThirtyDaysAgo){
                                $query->select('id_sj')->from('sales_invoice_detail')
                                        ->join('sales_invoice', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                        ->where([
                                                    ['sales_invoice.deleted_at', '=', null],
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    //['flag_tf', '=', '1'],
                                                //  ['flag_pembayaran', '!=', '0']
                                        ])
                                        ->whereRaw("Date(tanggal_invoice) >= '".$ThirtyDaysAgo."'");
                                })
                        ->orderBy('value', 'desc')
                        ->groupBy('delivery.id_so', 'delivery_detail.id_item')
                        ->limit(5)
                        ->get();

        $txtRank = "";
        foreach($rankOfItemSale as $item) {
            $txtRank = $txtRank.'<div class="d-flex justify-content-between mb-5">';
			$txtRank = $txtRank.    '<div class="d-flex align-items-center mr-2">';
			$txtRank = $txtRank.	    '<div>';
			$txtRank = $txtRank.		    '<a href="#" class="font-size-md text-dark-75 text-hover-primary font-weight-bolder">'.$item->nama_item.'</a>';
			$txtRank = $txtRank.		    '<div class="font-size-xs text-muted font-weight-bold">'.ucwords($item->nama_kategori).', '.ucwords($item->nama_merk).'</div>';
			$txtRank = $txtRank.	    '</div>';
			$txtRank = $txtRank.    '</div>';
			$txtRank = $txtRank.    '<div class="label label-light-info label-inline font-weight-bold py-4 px-3 font-size-base">Rp&nbsp;'.number_format($item->value,0,',','.').'</div>';
			$txtRank = $txtRank.'</div>';
        }

        $rankOfCustPurchase = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                            ->leftJoin('customer_detail', function($join) {
                                                $join->on('customer_detail.id_customer', '=', 'customer.id');
                                                $join->where('customer_detail.default','=', 'Y');
                                            })
                                            ->select(
                                                DB::raw("COALESCE(SUM(sales_invoice.grand_total),0) as value"),
                                                'customer.nama_customer',
                                                'customer_detail.kecamatan',
                                                'customer_detail.kota')
                                            ->where([
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    //['flag_tf', '=', '1'],
                                                    // ['flag_pembayaran', '!=', '0']
                                            ])
                                            ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$ThirtyDaysAgo."'")
                                            ->orderBy('value', 'desc')
                                            ->groupBy('customer.id', 'customer.nama_customer')
                                            ->limit(5)
                                            ->get();

        $txtRankCust = "";
        foreach($rankOfCustPurchase as $itemPurc) {
            $txtRankCust = $txtRankCust.'<div class="d-flex justify-content-between mb-5">';
			$txtRankCust = $txtRankCust.    '<div class="d-flex align-items-center mr-2">';
			$txtRankCust = $txtRankCust.	    '<div>';
			$txtRankCust = $txtRankCust.		    '<a href="#" class="font-size-md text-dark-75 text-hover-primary font-weight-bolder">'.$itemPurc->nama_customer.'</a>';
			$txtRankCust = $txtRankCust.		    '<div class="font-size-xs text-muted font-weight-bold">'.ucwords($itemPurc->kecamatan).', '.ucwords($itemPurc->kota).'</div>';
			$txtRankCust = $txtRankCust.	    '</div>';
			$txtRankCust = $txtRankCust.    '</div>';
			$txtRankCust = $txtRankCust.    '<div class="label label-light-primary label-inline font-weight-bold py-4 px-3 font-size-base">Rp&nbsp;'.number_format($itemPurc->value,0,',','.').'</div>';
			$txtRankCust = $txtRankCust.'</div>';
        }

        // $jmlInvPPn = SalesInvoice::select(DB::raw("COALESCE(COUNT(*),0) as value"))
        //                         ->where([
        //                             ['status_invoice', '=', 'posted'],
        //                             ['flag_ppn', '!=', 'N'],
        //                             // ['flag_pembayaran', '!=', '0']
        //                         ])
        //                         ->whereNotIn('sales_invoice.id', function($querySub) {
        //                             $querySub->select(DB::raw("(sales_tax_invoice.id_invoice)"))->from("sales_tax_invoice")
        //                                     ->where([
        //                                         ['sales_tax_invoice.deleted_at', '=', null]
        //                                     ]);
        //                         })
        //                         ->whereRaw("Date(tanggal_invoice) >= '".$fourMonth."'")
        //                         ->first();

        // $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
        //                                     ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
        //                                     ->select(
        //                                         'sales_invoice.id',
        //                                         DB::raw("SUM(CASE
        //                                                         WHEN expedition_cost_detail.flag_tagih = 'Y'
        //                                                             THEN expedition_cost_detail.subtotal
        //                                                         ELSE 0
        //                                                     END) AS BiayaEkspedisi")
        //                                     )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted']
        //                                             ])
        //                                     ->groupBy('sales_invoice.id');

        // $totalTagihan = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
        //                         ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
        //                             $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
        //                         })
        //                         ->select(
        //                                     DB::raw("(SUM(sales_invoice.grand_total) + SUM(dataBiayaEkspedisi.BiayaEkspedisi)) AS sumTotal")
        //                                 )
        //                         ->where([
        //                                     ['sales_invoice.status_invoice', '=', 'posted'],
        //                                 ])
        //                         ->first();

        // $totalBayar = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
        //                                     ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
        //                                     ->select(
        //                                                 DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
        //                                             )
        //                                     ->where([
        //                                                 ['sales_invoice.status_invoice', '=', 'posted'],
        //                                                 ['sales_invoice.flag_pembayaran', '!=', '0']
        //                                             ])
        //                                     ->first();

        // $totalBayarPurc = AccountPayableDetail::leftJoin('purchase_invoice', 'purchase_invoice.id', '=', 'account_payable_detail.id_invoice')
        //                                     ->select(
        //                                             DB::raw("SUM(account_payable_detail.nominal_bayar) AS sumBayar")
        //                                         )
        //                                     ->where([
        //                                         ['purchase_invoice.flag_pembayaran', '!=', '0'],
        //                                         ['purchase_invoice.status_invoice', '=', 'posted']
        //                                     ])
        //                                     ->first();

        // $totalTagihanPurc = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', '=', 'purchase_order.id')
        //                             ->select(
        //                                         DB::raw("SUM(purchase_invoice.grand_total) AS sumTotal")
        //                                     )
        //                             ->where([
        //                                         ['purchase_invoice.status_invoice', '=', 'posted'],
        //                                     ])
        //                             ->first();

        $totalTagihan = AccountReceiveableBalance::select(
                                                    DB::raw("SUM(nominal_outstanding) AS value")
                                                )
                                                ->first();

        $totalHutang = AccountPayableBalance::select(
                                                    DB::raw("SUM(nominal_outstanding) AS value")
                                                )
                                                ->first();

        // $data->jmlInvPPn = $jmlInvPPn->value;
        $data->piutang = $totalTagihan->value;
        $data->hutang = $totalHutang->value;
        $data->txtRank = $txtRank;
        $data->txtRankCust = $txtRankCust;

        return response()->json($data);
    }

    public function createChartPenjualan(Request $request)
    {
        $periodeAwal = $request->input('periode_awal');
        $periodeAkhir = $request->input('periode_akhir');

        $dataBiayaEkspedisiChart = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
                                            ->select(
                                                'sales_invoice.id',
                                                DB::raw("SUM(CASE
                                                                WHEN expedition_cost_detail.flag_tagih = 'Y'
                                                                    THEN expedition_cost_detail.subtotal
                                                                ELSE 0
                                                            END) AS BiayaEkspedisi")
                                            )
                                            ->where([
                                                        ['sales_invoice.status_invoice', '=', 'posted']
                                                    ])
                                            ->groupBy('sales_invoice.id');

        $dataChartBulanan = SalesInvoice::leftJoinSub($dataBiayaEkspedisiChart, 'dataBiayaEkspedisiChart', function($dataBiayaEkspedisiChart) {
                                        $dataBiayaEkspedisiChart->on('sales_invoice.id', '=', 'dataBiayaEkspedisiChart.id');
                                    })
                                    ->select(
                                                DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice, '%b') AS indx"),
                                                DB::raw("(SUM(COALESCE(sales_invoice.grand_total, 0) + COALESCE(dataBiayaEkspedisiChart.BiayaEkspedisi,0))) AS nominal"),
                                            )
                                    ->orderBy('sales_invoice.id', 'asc')
                                    ->where([
                                        ['sales_invoice.status_invoice', '=', 'posted'],
                                    ])
                                    ->whereYear('sales_invoice.tanggal_invoice', Carbon::now()->format('Y-m-d'))
                                    //->groupBy('sales_invoice.id')
                                    ->groupBy(DB::raw('indx'))
                                    ->get();

        return response()->json($dataChartBulanan);
    }

    public function getDataInvSale(Request $request)
    {
        $periodeAwal = $request->input('periode_awal');
        $periodeAkhir = $request->input('periode_akhir');

        if ($periodeAwal != "" && $periodeAkhir !="") {

            $dataChart = SalesInvoice::select(
                                            DB::raw("SUM(grand_total) as nominal"),
                                            DB::raw("COUNT(status_invoice) as jml")
                                        )
                                        ->where([
                                            ['sales_invoice.status_invoice', '=', 'posted']
                                        ])
                                        ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
                                        ->groupBy('sales_invoice.status_invoice')
                                        ->get();

        }

        return response()->json($dataChart);
    }

    public function getDataSales(Request $request)
    {
        $periodeAwal = $request->input('periode_awal');
        $periodeAkhir = $request->input('periode_akhir');

        if ($periodeAwal != "" && $periodeAkhir !="") {
            $dataSales = new stdClass();

            $invPaid = SalesInvoice::select(
                                        DB::raw("SUM(grand_total) as nominal"),
                                        DB::raw("COUNT(status_invoice) as jml")
                                    )
                                    ->where([
                                        ['sales_invoice.status_invoice', '=', 'posted'],
                                        ['sales_invoice.flag_pembayaran', '=', '1'],
                                    ])
                                    ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
                                    ->groupBy('sales_invoice.status_invoice')
                                    ->first();

            // $invNotPaid = SalesInvoice::leftJoin('account_receiveable_detail', 'account_receiveable_detail.id_invoice', '=', 'sales_invoice.id')
            //                         ->select(
            //                             DB::raw("SUM(sales_invoice.grand_total) as nominal"),
            //                             DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar"),
            //                             DB::raw("COUNT(sales_invoice.status_invoice) as jml")
            //                         )
            //                         ->where([
            //                             ['sales_invoice.status_invoice', '=', 'posted'],
            //                             ['sales_invoice.flag_pembayaran', '!=', '1'],
            //                         ])
            //                         ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
            //                         ->groupBy('sales_invoice.status_invoice')
            //                         ->first();



            // $invDue = SalesInvoice::leftJoin('account_receiveable_detail', 'account_receiveable_detail.id_invoice', '=', 'sales_invoice.id')
            //                         ->select(
            //                             DB::raw("SUM(sales_invoice.grand_total) as nominal"),
            //                             DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar"),
            //                             DB::raw("COUNT(sales_invoice.status_invoice) as jml")
            //                         )
            //                         ->where([
            //                             ['sales_invoice.status_invoice', '=', 'posted'],
            //                             ['sales_invoice.flag_pembayaran', '!=', '1'],
            //                         ])
            //                         ->whereRaw("Date(sales_invoice.tanggal_jt) < '".$periodeAkhir."'")
            //                         //->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
            //                         ->groupBy('sales_invoice.status_invoice')
            //                         ->first();

            // $invNonDue = SalesInvoice::select(
            //                             DB::raw("SUM(sales_invoice.grand_total) as nominal"),
            //                             DB::raw("COUNT(sales_invoice.status_invoice) as jml")
            //                         )
            //                         ->where([
            //                             ['sales_invoice.status_invoice', '=', 'posted'],
            //                             ['sales_invoice.flag_pembayaran', '!=', '1'],
            //                         ])
            //                         ->whereRaw("Date(sales_invoice.tanggal_jt) >= '".$periodeAkhir."'")
            //                         //->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
            //                         ->groupBy('sales_invoice.status_invoice')
            //                         ->first();

            $invNotPaid = AccountReceiveableBalance::select(
                                                        DB::raw("
                                                            SUM(account_receiveable_balance.nominal_outstanding) as 'TotalTagihan',
                                                            SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN account_receiveable_balance.nominal_outstanding ELSE 0 END) AS 'TotalTagihanJT',
                                                            SUM(CASE WHEN account_receiveable_balance.tanggal_jt < NOW() THEN 1 ELSE 0 END)	 AS 'TotalInvoiceJT',
                                                            COUNT(account_receiveable_balance.id_invoice) AS 'TotalInvoice'
                                                        ")
                                                    )
                                                    ->whereRaw("Date(account_receiveable_balance.tanggal_invoice) >= '".$periodeAwal."' AND Date(account_receiveable_balance.tanggal_invoice) <= '".$periodeAkhir."'")
                                                    ->first();

            $dataSales->nominalInvPaid = $invPaid->nominal ?? 0;
            $dataSales->ttlInvPaid = $invPaid->jml ?? 0;
            $dataSales->nominalInvNotPaid = $invNotPaid->TotalTagihan ?? 0;
            $dataSales->ttlInvNotPaid = $invNotPaid->TotalInvoice ?? 0;
            $dataSales->nominalInvDue = $invNotPaid->TotalTagihanJT ?? 0;
            $dataSales->ttlInvDue = $invNotPaid->TotalInvoiceJT ?? 0;
            $dataSales->nominalInvNotDue = $invNotPaid->TotalTagihan ?? 0 - $invNotPaid->TotalTagihanJT ?? 0;
            $dataSales->ttlInvNotDue = $invNotPaid->TotalInvoice ?? 0 - $invNotPaid->TotalInvoiceJT ?? 0;
        }

        return response()->json($dataSales);
    }

    public function getDataSalesDetail(Request $request)
    {
        $periodeAwal = $request->input('periode_awal');
        $periodeAkhir = $request->input('periode_akhir');
        $mode = $request->input('mode');

        if ($periodeAwal != "" && $periodeAkhir !="") {

            $dataSales = new stdClass();

            if ($mode == 'lunas') {
                $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                            ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
                                            ->select(
                                                'sales_invoice.id',
                                                DB::raw("SUM(CASE
                                                                WHEN expedition_cost_detail.flag_tagih = 'Y'
                                                                    THEN expedition_cost_detail.subtotal
                                                                ELSE 0
                                                            END) AS BiayaEkspedisi")
                                            )
                                            ->where([
                                                        ['sales_invoice.status_invoice', '=', 'posted']
                                                    ])
                                            ->groupBy('sales_invoice.id');

                $dataInv = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                        ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                            $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                        })
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'customer.nama_customer',
                                                    DB::raw("sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi, 0) AS grand_total")
                                                )
                                        ->where([
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    ['sales_invoice.flag_pembayaran', '=', '1']
                                                ])
                                        ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
                                        ->get();


            }
            elseif ($mode == 'belum_lunas') {

                $totalSisaTagihan = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
                                                ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
                                                ->select(
                                                            'sales_invoice.id',
                                                            DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
                                                        )
                                                ->where([
                                                            ['sales_invoice.status_invoice', '=', 'posted'],
                                                            ['sales_invoice.flag_pembayaran', '!=', '0']
                                                        ])
                                                ->groupBy('sales_invoice.id');

                $dataInv = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                        ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
                                            $totalSisaTagihan->on('sales_invoice.id', '=', 'totalSisaTagihan.id');
                                        })
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'customer.nama_customer',
                                                    DB::raw("sales_invoice.grand_total - COALESCE(totalSisaTagihan.sumBayar, 0) AS grand_total")
                                                )
                                        ->where([
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    ['sales_invoice.flag_pembayaran', '!=', '1']
                                                ])
                                        ->whereRaw("Date(sales_invoice.tanggal_invoice) >= '".$periodeAwal."' AND Date(sales_invoice.tanggal_invoice) <= '".$periodeAkhir."'")
                                        ->get();

            }
            elseif ($mode == 'jatuh_tempo') {
                $totalSisaTagihan = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
                                                ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
                                                ->select(
                                                            'sales_invoice.id',
                                                            DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
                                                        )
                                                ->where([
                                                            ['sales_invoice.status_invoice', '=', 'posted'],
                                                            ['sales_invoice.flag_pembayaran', '!=', '0']
                                                        ])
                                                ->groupBy('sales_invoice.id');

                $dataInv = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                        ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
                                            $totalSisaTagihan->on('sales_invoice.id', '=', 'totalSisaTagihan.id');
                                        })
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'customer.nama_customer',
                                                    DB::raw("sales_invoice.grand_total - COALESCE(totalSisaTagihan.sumBayar, 0) AS grand_total")
                                                )
                                        ->where([
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    ['sales_invoice.flag_pembayaran', '!=', '1']
                                                ])
                                        ->whereRaw("Date(sales_invoice.tanggal_jt) < '".$periodeAwal."'")
                                        ->get();

            }
            elseif ($mode == 'belum_jt') {
                $totalSisaTagihan = AccountReceiveable::leftJoin('account_receiveable_detail', 'account_receiveable.id', '=', 'account_receiveable_detail.id_ar')
                                                ->leftJoin('sales_invoice', 'sales_invoice.id', '=', 'account_receiveable_detail.id_invoice')
                                                ->select(
                                                            'sales_invoice.id',
                                                            DB::raw("SUM(account_receiveable_detail.nominal_bayar) AS sumBayar")
                                                        )
                                                ->where([
                                                            ['sales_invoice.status_invoice', '=', 'posted'],
                                                            ['sales_invoice.flag_pembayaran', '!=', '0']
                                                        ])
                                                ->groupBy('sales_invoice.id');

                $dataInv = SalesInvoice::join('sales_order', 'sales_invoice.id_so', '=', 'sales_order.id')
                                        ->leftJoinSub($totalSisaTagihan, 'totalSisaTagihan', function($totalSisaTagihan) {
                                            $totalSisaTagihan->on('sales_invoice.id', '=', 'totalSisaTagihan.id');
                                        })
                                        ->leftJoin('customer', 'sales_order.id_customer', '=', 'customer.id')
                                        ->select(
                                                    'sales_invoice.kode_invoice',
                                                    'sales_invoice.tanggal_invoice',
                                                    'sales_invoice.tanggal_jt',
                                                    'customer.nama_customer',
                                                    DB::raw("sales_invoice.grand_total - COALESCE(totalSisaTagihan.sumBayar, 0) AS grand_total")
                                                )
                                        ->where([
                                                    ['sales_invoice.status_invoice', '=', 'posted'],
                                                    ['sales_invoice.flag_pembayaran', '!=', '1']
                                                ])
                                        ->whereRaw("Date(sales_invoice.tanggal_jt) >= '".$periodeAwal."'")
                                        ->get();
            }

            $txtInv = null;

            foreach ($dataInv as $inv) {
                $txtInv =   $txtInv."<tr>";
                $txtInv =   $txtInv."<td style='text-align:center;'>".strtoupper($inv->kode_invoice)."</td>";
                $txtInv =   $txtInv."<td style='text-align:left;'>".$inv->nama_customer."</td>";
                $txtInv =   $txtInv."<td style='text-align:center;'>".Carbon::parse($inv->tanggal_invoice)->isoFormat('D MMMM Y')."</td>";
                $txtInv =   $txtInv."<td style='text-align:center;'>".Carbon::parse($inv->tanggal_jt)->isoFormat('D MMMM Y')."</td>";
                $txtInv =   $txtInv."<td style='text-align:right;'>".number_format($inv->grand_total,0,',','.')."</td>";
                $txtInv =   $txtInv."</tr>";
            }

            $dataSales->txtInv = $txtInv;
        }

        return response()->json($dataSales);
    }

    public function getDataOmzet(Request $request)
    {
        $jenisPeriode = $request->input('jenis_periode');
        if ($jenisPeriode != "") {
            $currentDay = Carbon::now()->format('Y-m-d');
            $sevenDaysAgo = Carbon::now()->subDays(7)->format('Y-m-d');
            $dataChart = SalesInvoice::when($jenisPeriode == "Harian", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(grand_total) as nominal"),
                                            DB::raw("tanggal_invoice AS nm"),
                                        );
                                        $q->where([
                                            ['tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy("tanggal_invoice");
                                        $q->orderBy('tanggal_invoice', 'desc');
                                    })
                                    ->when($jenisPeriode == "Mingguan", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(grand_total) as nominal"),
                                            DB::raw("STR_TO_DATE(CONCAT(YEARWEEK(tanggal_invoice,'Sunday'), 'Sunday'), '%X%V %W') as nm")
                                        );
                                        $q->where([
                                            ['tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy(DB::raw("YEARWEEK(tanggal_invoice,'Sunday')"));
                                        $q->orderBy(DB::raw("nm"), 'desc');
                                    })
                                    ->when($jenisPeriode == "Bulanan", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(grand_total) as nominal"),
                                            DB::raw("DATE_FORMAT(tanggal_invoice,'%Y-%m') as nm")
                                        );
                                        $q->where([
                                            ['tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy(DB::raw("DATE_FORMAT(tanggal_invoice,'%Y-%m')"));
                                        $q->orderBy(DB::raw("nm"), 'desc');
                                    })
                                    ->where([
                                        ['status_invoice', '=', 'posted']
                                    ])
                                    ->get()
                                    ->take(7);

        }

        return response()->json($dataChart);
    }

    public function getDataProfit(Request $request)
    {
        $jenisPeriode = $request->input('jenis_periode');
        if ($jenisPeriode != "") {
            $currentDay = Carbon::now()->format('Y-m-d');
            $sevenDaysAgo = Carbon::now()->subDays(7)->format('Y-m-d');

            $hargaModal = PurchaseOrderDetail::leftJoin('purchase_order', 'purchase_order_detail.id_po', '=', 'purchase_order.id')
                                            ->select('purchase_order_detail.id_item', DB::raw("AVG(purchase_order_detail.harga_beli) AS harga_modal"))
                                            ->whereIn('purchase_order.id', function($querySub) {
                                                $querySub->select(DB::raw("(purchase_invoice.id_po)"))->from("purchase_invoice")
                                                        ->where([
                                                            ['purchase_invoice.status_invoice', '=', 'posted']
                                                        ]);
                                            })
                                            ->groupBy('purchase_order_detail.id_item');



            $dataChart = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                    ->leftJoin('delivery_detail', 'delivery_detail.id_pengiriman', '=', 'sales_invoice_detail.id_sj')
                                    ->leftJoin('sales_order_detail', function($j) {
                                        $j->on('sales_order_detail.id_item', '=', 'delivery_detail.id_item');
                                        $j->on('sales_order_detail.id_so', '=', 'sales_invoice.id_so');
                                    })
                                    ->leftJoinSub($hargaModal, 'hargaModal', function($hargaModal) {
                                        $hargaModal->on('delivery_detail.id_item', '=', 'hargaModal.id_item');
                                    })
                                    ->when($jenisPeriode == "Harian", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(delivery_detail.qty_item * (sales_order_detail.harga_jual - hargaModal.harga_modal)) as nominal"),
                                            DB::raw("sales_invoice.tanggal_invoice AS nm"),
                                        );
                                        $q->where([
                                            ['sales_invoice.tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy("sales_invoice.tanggal_invoice");
                                        $q->orderBy('sales_invoice.tanggal_invoice', 'desc');
                                    })
                                    ->when($jenisPeriode == "Mingguan", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(delivery_detail.qty_item * (sales_order_detail.harga_jual - hargaModal.harga_modal)) as nominal"),
                                            DB::raw("STR_TO_DATE(CONCAT(YEARWEEK(sales_invoice.tanggal_invoice,'Sunday'), 'Sunday'), '%X%V %W') as nm")
                                        );
                                        $q->where([
                                            ['sales_invoice.tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy(DB::raw("YEARWEEK(sales_invoice.tanggal_invoice,'Sunday')"));
                                        $q->orderBy(DB::raw("nm"), 'desc');
                                    })
                                    ->when($jenisPeriode == "Bulanan", function($q) use ($currentDay) {
                                        $q->select(
                                            DB::raw("SUM(delivery_detail.qty_item * (sales_order_detail.harga_jual - hargaModal.harga_modal)) as nominal"),
                                            DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m') as nm")
                                        );
                                        $q->where([
                                            ['sales_invoice.tanggal_invoice', '<=', $currentDay]
                                        ]);
                                        $q->groupBy(DB::raw("DATE_FORMAT(sales_invoice.tanggal_invoice,'%Y-%m')"));
                                        $q->orderBy(DB::raw("nm"), 'desc');
                                    })
                                    ->where([
                                        ['sales_invoice.status_invoice', '=', 'posted']
                                    ])
                                    ->get()
                                    ->take(7);

        }

        return response()->json($dataChart);
    }

    public function createChartOmzetMingguan(Request $request)
    {
          $dataInv = DB::table('invoice_penjualan')
                          ->leftJoin('invoice_pembelian', 'invoice_penjualan.tanggal_invoice', '=', 'invoice_pembelian.tanggal_invoice')
                          ->select(DB::raw("COALESCE(SUM(invoice_penjualan.grand_total),0) AS value1"),
                                   DB::raw("YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday') AS name"),
                                   DB::raw("COALESCE(SUM(invoice_pembelian.grand_total),0) AS value2"),
                                   DB::raw("YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday') As name1"),
                                   DB::raw("(COALESCE(SUM(invoice_penjualan.grand_total),0)-COALESCE(SUM(invoice_pembelian.grand_total),0)) as value3"),
                                   DB::raw("COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')) as weekOrder"),
                                   DB::raw("STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W') as name3"),
                                    DB::raw("CONCAT(DATE_FORMAT(DATE_ADD( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'), INTERVAL (MOD(DAYOFWEEK( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'))-1, 7)*-1) DAY), '%d-%b'), ' - ',DATE_FORMAT(DATE_ADD( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'), INTERVAL ((MOD(DAYOFWEEK( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'))-1, 7)*-1)+6) DAY), '%d-%b')) AS weekRange"))
                          ->where([
                                    ['invoice_penjualan.flag_aktif', '=', 'Y']
                                  ])
                          //->whereRaw("Date(invoice_penjualan.tanggal_invoice) >= '".$periodeAwal."' AND Date(invoice_penjualan.tanggal_invoice) <= '".$periodeAkhir."'")
                          ->groupBy(DB::raw('name'),DB::raw('name1'),DB::raw('weekOrder'));


            $dataInvUnion = DB::table('invoice_penjualan')
                          ->rightJoin('invoice_pembelian', 'invoice_penjualan.tanggal_invoice', '=', 'invoice_pembelian.tanggal_invoice')
                          ->select(DB::raw("COALESCE(SUM(invoice_penjualan.grand_total),0) AS value1"),
                                   DB::raw("YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday') AS name"),
                                   DB::raw("COALESCE(SUM(invoice_pembelian.grand_total),0) AS value2"),
                                   DB::raw("YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday') As name1"),
                                   DB::raw("(COALESCE(SUM(invoice_penjualan.grand_total),0)-COALESCE(SUM(invoice_pembelian.grand_total),0)) as value3"),
                                   DB::raw("COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')) as weekOrder"),
                                   DB::raw("STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W') as name3"),
                                   DB::raw(" CONCAT(DATE_FORMAT(DATE_ADD( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'), INTERVAL (MOD(DAYOFWEEK( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'))-1, 7)*-1) DAY), '%d-%b'), ' - ',DATE_FORMAT(DATE_ADD( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'), INTERVAL ((MOD(DAYOFWEEK( STR_TO_DATE(CONCAT(COALESCE(YEARWEEK(invoice_penjualan.tanggal_invoice,'Sunday'), YEARWEEK(invoice_pembelian.tanggal_invoice,'Sunday')), 'Sunday'), '%X%V %W'))-1, 7)*-1)+6) DAY), '%d-%b')) AS weekRange"))
                          ->where([
                                    ['invoice_pembelian.flag_aktif', '=', 'Y']
                                  ])
                          //->whereRaw("Date(invoice_pembelian.tanggal_invoice) >= '".$periodeAwal."' AND Date(invoice_pembelian.tanggal_invoice) <= '".$periodeAkhir."'")
                          ->groupBy(DB::raw('name'),DB::raw('name1'),DB::raw('weekOrder'))
                          ->union($dataInv)
                          ->orderBy('weekOrder')
                          ->get();


        return response()->json($dataInvUnion);
    }

    public function getTaxSerialNumberCount(Request $request)
    {
            $usage = DB::table('sales_tax_invoice')
                        ->leftJoin('tax_serial_number', 'sales_tax_invoice.id_seri', 'tax_serial_number.id')
                        ->select(
                            DB::raw("COALESCE(COUNT(sales_tax_invoice.nomor_faktur),0) AS value1"),
                            'sales_tax_invoice.id_seri'
                        )
                        ->where([
                            ['tax_serial_number.status', '=', 'posted']
                        ])
                        ->groupBy('sales_tax_invoice.id_seri');


            $dataSerial = DB::table('tax_serial_number')
                                ->leftJoinSub($usage, 'usage', function($usage) {
                                    $usage->on('tax_serial_number.id', '=', 'usage.id_seri');
                                })
                                ->select(
                                    'tax_serial_number.tahun_berlaku_seri',
                                    'tax_serial_number.nomor_seri_dari',
                                    'tax_serial_number.nomor_seri_sampai',
                                    'tax_serial_number.jumlah_no_seri',
                                    DB::raw("COALESCE(tax_serial_number.jumlah_no_seri,0) - COALESCE(usage.value1,0) AS sisa_jumlah")
                                )
                                ->where([
                                    ['tax_serial_number.status', '=', 'posted']
                                ])
                                ->orderBy('tax_serial_number.tanggal_pemberitahuan_djp')
                                ->get();


        return response()->json($dataSerial);
    }

    public function getStockMonitor(Request $request)
    {
        $idIndex = $request->input('idIndex');

        // $stokIn = StockTransaction::select('id_item', 'id_index', 'id_satuan', DB::raw('SUM(qty_item) AS stok_in'))
        //                             ->where([
        //                                         ['transaksi', '=', 'in']
        //                                     ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan');
        //                             // ->groupBy('id_index');

        // $stokOut = StockTransaction::select('id_item', 'id_index', 'id_satuan', DB::raw('SUM(qty_item) AS stok_out'))
        //                             ->where([
        //                                 ['transaksi', '=', 'out']
        //                             ])
        //                             ->groupBy('id_item')
        //                             ->groupBy('id_satuan');
        //                             // ->groupBy('id_index');

        $dataStocks = StockTransaction::select(
                                        'stock_transaction.id_item',
                                        'stock_transaction.id_index',
                                        'stock_transaction.id_satuan',
                                        DB::raw("SUM(
                                            CASE WHEN stock_transaction.transaksi = 'in' THEN +stock_transaction.qty_item
                                                    Else -stock_transaction.qty_item
                                            End
                                        ) AS qty")
                                    )
                                    ->groupBy('stock_transaction.id_item')
                                    ->groupBy('stock_transaction.id_satuan');
                                    // ->groupBy('stock_transaction.id_index')

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
                'nama_index' => $txt
            ];

            array_push($list, $dataTxt);
        }

        $dataStoks = Product::leftJoin('product_category', 'product.kategori_item', '=', 'product_category.id')
                            ->leftJoin('product_brand', 'product.merk_item', '=', 'product_brand.id')
                            ->leftJoin('product_detail', 'product.id', '=', 'product_detail.id_product')
                            ->leftJoin('product_unit', 'product_unit.id', '=', 'product_detail.id_satuan')
                            // ->leftJoinSub($stokIn, 'stokIn', function($join_in) {
                            //     $join_in->on('product_detail.id_product', '=', 'stokIn.id_item');
                            //     $join_in->on('product_detail.id_satuan', '=', 'stokIn.id_satuan');
                            // })
                            // ->leftJoinSub($stokOut, 'stokOut', function($join_out) {
                            //     $join_out->on('product_detail.id_product', '=', 'stokOut.id_item');
                            //     $join_out->on('product_detail.id_satuan', '=', 'stokOut.id_satuan');
                            //     // $join_out->on('stokIn.id_index', '=', 'stokOut.id_index');
                            // })
                            ->leftJoinSub($dataStocks, 'dataStocks', function($dataStocks) {
                                $dataStocks->on('product.id', '=', 'dataStocks.id_item');
                                $dataStocks->on('product_detail.id_satuan', '=', 'dataStocks.id_satuan');
                            })
                            ->select('product.id',
                                'product.kode_item',
                                'product.nama_item',
                                'product.jenis_item',
                                'product_brand.nama_merk',
                                'product_category.nama_kategori',
                                'product_detail.stok_minimum',
                                'product_detail.stok_maksimum',
                                'product_unit.nama_satuan',
                                DB::raw('product_unit.id as id_satuan'),
                                // 'dataStocks.qty'
                                DB::raw('COALESCE(dataStocks.qty,0) AS stok_item')
                            )
                            ->when($idIndex != null, function($q) use ($idIndex) {
                                $q->where('dataStocks.id_index', $idIndex);
                            })
                            ->whereIn('product.id', function($query) {
                                $query->select('id_item')->from('stock_transaction');
                            })
                            ->where([
                                ['product_detail.deleted_at', '=', null],
                                ['product_detail.flag_monitor', '=', 1],
                                ['product_detail.stok_minimum', '>', 0],
                            ])
                            ->whereRaw("COALESCE(dataStocks.qty,0) <= product_detail.stok_minimum")
                            ->orderBy('product.id', 'desc')
                            ->get();

        $stok = [];
        foreach($dataStoks as $dataStock) {
            $txtIndex = "-";
            foreach ($list as $txt) {
                if ($txt["id"] == $dataStock->id_index) {
                    $idIndex = $txt["id"];
                    $txtIndex = $txt["nama_index"];
                }
            }
            $dataAlloc = [
                'id' => $dataStock->id,
                'nama_merk' => $dataStock->nama_merk,
                'nama_kategori' => $dataStock->nama_kategori,
                'nama_item' => $dataStock->nama_item,
                'kode_item' => $dataStock->kode_item,
                // 'value_spesifikasi' => $dataStock->value_spesifikasi,
                'stok_item' => $dataStock->stok_item,
                'id_satuan' => $dataStock->id_satuan,
                'nama_satuan' => $dataStock->nama_satuan,
                'stok_minimum' => $dataStock->stok_minimum,
                'stok_maksimum' => $dataStock->stok_maksimum,
                'txt_index' => $txtIndex,
            ];
            array_push($stok, $dataAlloc);
        }

        return response()->json($stok);
    }

    public function CustomProgram() {

        $data = "";
        $user = Auth::user()->user_name;
        // $dataInv = SalesInvoice::take(300)
        //                         ->where([
        //                             ['sales_invoice.status_invoice', '=', 'posted'],
        //                             ['sales_invoice.flag_entry', '=', '0']
        //                         ])
        //                         ->whereRaw("YEAR(sales_invoice.tanggal_invoice) = '2025'")
        //                         ->orderBy('sales_invoice.tanggal_invoice', 'asc')
        //                         ->get();
        // foreach ($dataInv as $inv) {
        //     $exception = DB::transaction(function () use (&$data, $inv) {

        //         $data = "success";

        //         // $custs = Supplier::all();

        //             $settings = GLAccountSettings::find(1);
        //             $dataPurchase = SalesOrder::find($inv->id_so);
        //             $dataCustomer = Customer::find($dataPurchase->id_customer);
        //             $idAkun = "";
        //             $idTransaksi = "";

        //             if ($dataCustomer !=  null) {
        //                 $idAkun = $dataCustomer->id_account ?? $settings->id_account_piutang;
        //             }
        //             else {
        //                 $idAkun = $settings->id_account_piutang;
        //             }

        //             $postJournal = HelperAccounting::PostJournal("sales_invoice", $inv->id, $idAkun, $settings->id_account_penjualan, $inv->tanggal_invoice, $inv->grand_total, 'system');

        //             $inv->flag_entry = 1;
        //             $inv->save();
        //     });
        // }

        // $dataInv = PurchaseInvoice::take(300)
        //                         ->where([
        //                             ['purchase_invoice.status_invoice', '=', 'posted'],
        //                             ['purchase_invoice.flag_entry', '=', '0']
        //                         ])
        //                         ->whereRaw("YEAR(purchase_invoice.tanggal_invoice) = '2025'")
        //                         ->orderBy('purchase_invoice.tanggal_invoice', 'asc')
        //                         ->get();
        // foreach ($dataInv as $inv) {
        //     $exception = DB::transaction(function () use (&$data, $inv) {

        //         $data = "success";

        //         // $custs = Supplier::all();

        //             $settings = GLAccountSettings::find(1);
        //             $dataPurchase = PurchaseOrder::find($inv->id_po);
        //             $dataSupplier = Supplier::find($dataPurchase->id_supplier);
        //             $idAkun = "";
        //             $idTransaksi = "";

        //             if ($dataSupplier !=  null) {
        //                 $idAkun = $dataSupplier->id_account ?? $settings->id_account_hutang;
        //             }
        //             else {
        //                 $idAkun = $settings->id_account_hutang;
        //             }

        //             HelperAccounting::PostJournal("purchase_invoice", $inv->id, $settings->id_account_persediaan, $idAkun, $inv->tanggal_invoice, $inv->grand_total, 'system');

        //             $inv->flag_entry = 1;
        //             $inv->save();
        //     });
        // }

        // $dataInv = AccountReceiveable::take(300)
        //                         ->leftJoin('account_receiveable_detail', 'account_receiveable_detail.id_ar', '=', 'account_receiveable.id')
        //                         ->select(
        //                             'account_receiveable.*',
        //                             'account_receiveable_detail.nominal_bayar',
        //                             'account_receiveable_detail.id as id_detail'
        //                         )
        //                         ->where([
        //                             ['account_receiveable.status', '=', 'posted'],
        //                             ['account_receiveable_detail.flag_entry', '=', '0'],
        //                             ['account_receiveable_detail.deleted_at', '=', null]
        //                         ])
        //                         ->whereRaw("YEAR(account_receiveable.tanggal) = '2025'")
        //                         ->orderBy('account_receiveable.tanggal', 'asc')
        //                         ->get();

        // foreach ($dataInv as $inv) {
        //     $exception = DB::transaction(function () use (&$data, $inv) {

        //         $data = "success";

        //         // $custs = Supplier::all();

        //             $settings = GLAccountSettings::find(1);
        //             $ap = AccountReceiveable::find($inv->id);
        //             $apd = AccountReceiveableDetail::find($inv->id_detail);
        //             $idAkunKas = $settings->id_account_kas ?? 0;
        //             $customer = Customer::find($ap->id_customer);
        //             $akunRekening = CompanyAccount::find($ap->rekening_pembayaran);
        //             $akunCustomer = $customer->id_account ?? 0;
        //             $idAkunPiutangSetting = $settings->id_account_piutang ?? 0;
        //             $idAkunPiutang = $akunCustomer != 0 ? $akunCustomer : $idAkunPiutangSetting;
        //             $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
        //             $akunSupplier = $supplier->id_account ?? 0;
        //             $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
        //             $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

        //             if ($idAkunRekening != 0 && $idAkunPiutang != 0) {
        //                 $postJournal = HelperAccounting::PostJournal("bank_masuk", $ap->id, $idAkunRekening, $idAkunPiutang, $ap->tanggal, $inv->nominal_bayar, 'system');
        //             }
        //             else {
        //                 $postJournal = HelperAccounting::PostJournal("kas_masuk", $ap->id, $idAkunKas, $idAkunPiutang, $ap->tanggal, $inv->nominal_bayar,  'system');
        //             }

        //             $apd->flag_entry = 1;
        //             $apd->save();
        //     });
        // }

        $dataInv = AccountPayable::take(300)
                                ->leftJoin('account_payable_detail', 'account_payable_detail.id_ap', '=', 'account_payable.id')
                                ->select(
                                    'account_payable.*',
                                    'account_payable_detail.nominal_bayar',
                                    'account_payable_detail.id as id_detail'
                                )
                                ->where([
                                    ['account_payable.status', '=', 'posted'],
                                    ['account_payable_detail.flag_entry', '=', '0'],
                                    ['account_payable_detail.deleted_at', '=', null]
                                ])
                                ->whereRaw("YEAR(account_payable.tanggal) = '2025'")
                                ->orderBy('account_payable.tanggal', 'asc')
                                ->get();

        foreach ($dataInv as $inv) {
            $exception = DB::transaction(function () use (&$data, $inv) {

                $data = "success";

                // $custs = Supplier::all();

                    $settings = GLAccountSettings::find(1);
                    $ap = AccountPayable::find($inv->id);
                    $apd = AccountPayableDetail::find($inv->id_detail);
                    $idAkunKas = $settings->id_account_kas ?? 0;
                    $supplier = Supplier::find($ap->id_supplier);
                    $akunRekening = CompanyAccount::find($ap->rekening_pembayaran);
                    $akunSupplier = $supplier->id_account ?? 0;
                    $idAkunRekening = $akunRekening != null ? $akunRekening->id_account : 0;
                    $idAkunHutangSetting = $settings->id_account_hutang ?? 0;
                    $idAkunHutang = $akunSupplier != 0 ? $akunSupplier : $idAkunHutangSetting;

                    if ($idAkunRekening != 0 && $idAkunHutang != 0) {
                        $postJournal = HelperAccounting::PostJournal("bank_keluar", $ap->id, $idAkunHutang, $idAkunRekening, $ap->tanggal, $inv->nominal_bayar, 'system');
                    }
                    else {
                        $postJournal = HelperAccounting::PostJournal("kas_keluar", $ap->id, $idAkunHutang, $idAkunKas, $ap->tanggal, $inv->nominal_bayar,  'system');
                    }

                    $apd->flag_entry = 1;
                    $apd->save();
            });
        }

        // $dataKasBank = GLKasBank::take(300)
        //                         ->where([
        //                             // ['gl_kas_bank.id', '=', '3947'],
        //                             ['gl_kas_bank.status', '=', 'posted'],
        //                             ['gl_kas_bank.jenis', '=', 'input'],
        //                             ['gl_kas_bank.flag_entry', '=', '0'],
        //                         ])
        //                         ->whereRaw("YEAR(gl_kas_bank.tanggal_transaksi) = '2025'")
        //                         ->orderBy('gl_kas_bank.tanggal_transaksi', 'asc')
        //                         ->get();
        // foreach ($dataKasBank as $kb) {
        //     $exception = DB::transaction(function () use (&$data, $kb) {

        //         $data = "success";

        //         $postJournal = HelperAccounting::PostJournalKasBank($kb->id, 'sata');
        //         //$remove = HelperAccounting::RemoveJournalKasBank($kb->id);

        //         $kb->flag_entry = 1;
        //         $kb->save();
        //     });
        // }

        // $dataCust = Supplier::all();
        // foreach ($dataCust as $cust) {
        //     $exception = DB::transaction(function () use (&$data, $cust, $user) {

        //         $data = "success";

        //         //Update Sub-Account Name jika terdapat account
        //         if ($cust->id_account != null) {
        //             $subAccount = GLSubAccount::find($cust->id_account);

        //             if ($subAccount != null) {
        //                 $nmAccount = $cust->nama_supplier;
        //                 $subAccount->account_name = $nmAccount;
        //                 $subAccount->updated_by = $user;
        //                 $subAccount->save();
        //             }
        //             else {
        //                 $nmAccount = $cust->nama_supplier;
        //                 $subAccount->account_name = $nmAccount;
        //                 $subAccount->updated_by = $user;
        //                 $subAccount->save();
        //             }
        //         }
        //         else {
        //             //Generate Sub-Account jika terdapat account PIUTANG
        //             $cekAccount = GLAccount::where('account_name', '=', 'HUTANG LANCAR')->first();
        //             if ($cekAccount != null) {
        //                 $lastSubAccount = GLSubAccount::where([
        //                     ['id_account', '=', $cekAccount->id]
        //                 ])
        //                 ->orderBy('order_number', 'desc')
        //                 ->first();

        //                 $nmAccount = $cust->nama_supplier;

        //                 if ($lastSubAccount != null) {
        //                     $kodePiutang = explode("-", $lastSubAccount->account_number);
        //                     $nmrAccount = $kodePiutang[0].'-'.str_pad($kodePiutang[1] + 1 , 4 , "0" , STR_PAD_LEFT);


        //                     $subAccount = new GLSubAccount();
        //                     $subAccount->account_name = $nmAccount;
        //                     $subAccount->account_number = $nmrAccount;
        //                     $subAccount->id_mother_account = $lastSubAccount->id_mother_account;
        //                     $subAccount->id_account = $lastSubAccount->id_account;
        //                     $subAccount->order_number = $lastSubAccount->order_number + 1;
        //                     $subAccount->created_by = $user;
        //                     $subAccount->save();

        //                     $cust->id_account = $subAccount->id;
        //                     $cust->save();
        //                 }
        //                 else {
        //                     $nmrAccount = str_replace('-','',$cekAccount->account_number).'-'.str_pad(1 , 4 , "0" , STR_PAD_LEFT);
        //                     $subAccount = new GLSubAccount();
        //                     $subAccount->account_name = $nmAccount;
        //                     $subAccount->account_number = $nmrAccount;
        //                     $subAccount->id_mother_account = $cekAccount->id_mother_account;
        //                     $subAccount->id_account = $cekAccount->id;
        //                     $subAccount->order_number = 1;
        //                     $subAccount->created_by = $user;
        //                     $subAccount->save();

        //                     $cust->id_account = $subAccount->id;
        //                     $cust->save();
        //                 }
        //             }
        //         }
        //     });
        // }

        if (is_null($exception)) {
            return response()->json($data);
        }
        else {
            return response()->json($exception);
        }

    }

    function logout()
    {
     	Auth::logout();
     	Session::flush();
     	return redirect('main');
    }
}
