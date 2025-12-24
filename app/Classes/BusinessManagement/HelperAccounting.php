<?php

namespace App\Classes\BusinessManagement;

use App\Models\Accounting\AccountPayable;
use App\Models\Accounting\AccountPayableBalance;
use App\Models\Accounting\AccountPayableDetail;
use App\Models\Accounting\AccountReceiveable;
use App\Models\Accounting\AccountReceiveableBalance;
use App\Models\Accounting\AccountReceiveableDetail;
use App\Models\Accounting\GLAccountSettings;
use App\Models\Accounting\GLAccountSettingsDetail;
use App\Models\Accounting\GLJournal;
use App\Models\Accounting\GLJournalDetail;
use App\Models\Accounting\GLKasBank;
use App\Models\Accounting\GLKasBankDetail;
use App\Models\ActionLog;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Sales\SalesInvoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class HelperAccounting
{
    public static function AutoGenerateJournal(Request $request) {

        $jenisPeriode = $request->input('jenisPeriode');
        $tglStart = $request->input('tglStart');
        $tglEnd = $request->input('tglEnd');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $idSetting = $request->input('id_setting');
        $dataCount = 0;

        $setting = GLAccountSettingsDetail::where([
                                            ['id_settings', '=', $idSetting]
                                        ])
                                        ->get();

        $idSumber = [];

        if ($setting != null) {

            foreach ($setting as $detail) {
                if ($detail->module_source == "penjualan") {
                    $invoices = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                            ->leftJoin('customer', 'sales_order.id_customer', 'customer.id')
                                            ->select(
                                                'sales_invoice.id',
                                                'customer.nama_customer',
                                                'sales_invoice.tanggal_invoice',
                                                'sales_invoice.dpp',
                                                'sales_invoice.ppn',
                                                'sales_invoice.grand_total'
                                            )
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
                                            ->where([
                                                ['sales_invoice.flag_entry', '!=', 1]
                                            ])
                                            ->get();



                    $transaksi = [];
                    foreach ($invoices as $items) {
                        if ($detail->field_source == "dpp") {
                            $nominal = $items->dpp;
                        }
                        elseif ($detail->field_source == "ppn") {
                            $nominal = $items->ppn;
                        }
                        elseif ($detail->field_source == "grand_total") {
                            $nominal = $items->grand_total;
                        }
                        else {
                            $nominal = 0;
                        }
                        $dataDetails = [
                            'id_account' => $detail->id_account,
                            'id_sumber' => $items->id,
                            'sumber' => 'sales_invoice',
                            'deskripsi' => 'Penjualan ke '.strtoupper($items->nama_customer),
                            'side' => $detail->side,
                            'nominal' => $nominal,
                            'tanggal_transaksi' => $items->tanggal_invoice,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name
                        ];
                        array_push($transaksi, $dataDetails);
                    }


                    GLJournal::insert($transaksi);

                    $idSumber = $invoices->pluck('id');

                    $dataCount = $dataCount + count($invoices);
                }
                elseif ($detail->module_source == "pembelian") {
                    $invoices = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', 'purchase_order.id')
                                                ->leftJoin('supplier', 'purchase_order.id_supplier', 'supplier.id')
                                                ->select(
                                                    'purchase_invoce.id',
                                                    'supplier.nama_supplier',
                                                    'purchase_invoce.tanggal_invoice',
                                                    'purchase_invoce.dpp',
                                                    'purchase_invoce.ppn',
                                                    'purchase_invoce.grand_total'
                                                )
                                                ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                    $q->whereBetween('purchase_invoice.tanggal_invoice', [$tglStart, $tglEnd]);
                                                })
                                                ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                    $q->whereMonth('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('m'));
                                                    $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($bulan)->format('Y'));
                                                })
                                                ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                    $q->whereYear('purchase_invoice.tanggal_invoice', Carbon::parse($tahun)->format('Y'));
                                                })
                                                ->where([
                                                    ['purchase_invoice.flag_entry', '!=', 1]
                                                ])
                                                ->get();

                    $transaksi = [];
                    foreach ($invoices as $items) {
                        if ($detail->field_source == "dpp") {
                            $nominal = $items->dpp;
                        }
                        elseif ($detail->field_source == "ppn") {
                            $nominal = $items->ppn;
                        }
                        elseif ($detail->field_source == "grand_total") {
                            $nominal = $items->grand_total;
                        }
                        else {
                            $nominal = 0;
                        }
                        $dataDetails = [
                            'id_account' => $detail->id_account,
                            'id_sumber' => $items->id,
                            'sumber' => 'purchase_invoice',
                            'deskripsi' => 'Pembelian ke '.strtoupper($items->nama_customer),
                            'side' => $detail->side,
                            'nominal' => $nominal,
                            'tanggal_transaksi' => $items->tanggal_invoice,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name
                        ];
                        array_push($transaksi, $dataDetails);
                    }

                    GLJournal::insert($transaksi);

                    $idSumber = $invoices->pluck('id');
                    $dataCount = $dataCount + count($invoices);
                }
                elseif ($detail->module_source == "pemasukan") {
                    $ar = AccountReceiveableDetail::leftJoin('account_receiveable', 'account_receiveable_detail.id_ar', '=', 'account_receiveable.id')
                                                    ->leftJoin('customer', 'account_receiveable.id_customer', 'customer.id')
                                                    ->select(
                                                        'account_receiveable.id',
                                                        'customer.nama_customer',
                                                        'account_receiveable.tanggal',
                                                        'account_receiveable_detail.nominal_bayar'
                                                    )
                                                    ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                        $q->whereBetween('account_receiveable.tanggal', [$tglStart, $tglEnd]);
                                                    })
                                                    ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                        $q->whereMonth('account_receiveable.tanggal', Carbon::parse($bulan)->format('m'));
                                                        $q->whereYear('account_receiveable.tanggal', Carbon::parse($bulan)->format('Y'));
                                                    })
                                                    ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                        $q->whereYear('account_receiveable.tanggal', Carbon::parse($tahun)->format('Y'));
                                                    })
                                                    ->where([
                                                        ['account_receiveable_detail.flag_entry', '!=', 1]
                                                    ])
                                                    ->get();

                    $transaksi = [];
                    foreach ($ar as $items) {
                        $dataDetails = [
                            'id_account' => $detail->id_account,
                            'id_sumber' => $items->id,
                            'sumber' => 'account_receiveable',
                            'deskripsi' => 'Pembayaran Piutang dari '.strtoupper($items->nama_customer),
                            'side' => $detail->side,
                            'nominal' => $items->nominal_bayar,
                            'tanggal_transaksi' => $items->tanggal_invoice,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name
                        ];
                        array_push($transaksi, $dataDetails);
                    }

                    $idSumber = $ar->pluck('id');
                    $dataCount = $dataCount + count($ar);
                }
                elseif ($detail->module_source == "pengeluaran") {
                    $ap = AccountPayableDetail::leftJoin('account_payable', 'account_payable_detail.id_ap', '=', 'account_payable.id')
                                                ->leftJoin('supplier', 'account_payable.id_supplier', 'supplier.id')
                                                ->select(
                                                    'account_payable.id',
                                                    'supplier.nama_supplier',
                                                    'account_payable.tanggal',
                                                    'account_payable_detail.nominal_bayar'
                                                )
                                                ->when($jenisPeriode == "harian", function($q) use ($tglStart, $tglEnd) {
                                                    $q->whereBetween('account_payable.tanggal', [$tglStart, $tglEnd]);
                                                })
                                                ->when($jenisPeriode == "bulanan", function($q) use ($bulan) {
                                                    $q->whereMonth('account_payable.tanggal', Carbon::parse($bulan)->format('m'));
                                                    $q->whereYear('account_payable.tanggal', Carbon::parse($bulan)->format('Y'));
                                                })
                                                ->when($jenisPeriode == "tahunan", function($q) use ($tahun) {
                                                    $q->whereYear('account_payable.tanggal', Carbon::parse($tahun)->format('Y'));
                                                })
                                                ->where([
                                                    ['account_payable_detail.flag_entry', '!=', 1]
                                                ])
                                                ->get();

                    $transaksi = [];
                    foreach ($ap as $items) {
                        $dataDetails = [
                            'id_account' => $detail->id_account,
                            'id_sumber' => $items->id,
                            'sumber' => 'account_payable',
                            'deskripsi' => 'Pembayaran Hutang ke '.strtoupper($items->nama_supplier),
                            'side' => $detail->side,
                            'nominal' => $items->nominal_bayar,
                            'tanggal_transaksi' => $items->tanggal_invoice,
                            'created_at' => now(),
                            'created_by' => Auth::user()->user_name
                        ];
                        array_push($transaksi, $dataDetails);
                    }

                    GLJournal::insert($transaksi);

                    $idSumber = $ap->pluck('id');
                    $dataCount = $dataCount + count($ap);
                }
            }

            $result["text"] = "success";
            $result["idSumber"] = $idSumber;
            return $result;
        }
        else {
            return $result["text"] = "failNoSetting";
        }
    }

    public static function InsertARBalance($idInvoice, $mode)
    {
        try {
            DB::beginTransaction();
            $cekInvoice = AccountReceiveableBalance::where('id_invoice', '=', $idInvoice)->first();

            if ($cekInvoice == null) {
                $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                                ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
                                                ->leftJoin('expedition_cost', 'expedition_cost_detail.id_cost', '=', 'expedition_cost.id')
                                                ->select(
                                                    'sales_invoice.id',
                                                    DB::raw("SUM(CASE
                                                                    WHEN expedition_cost_detail.flag_tagih = 'Y'
                                                                        THEN expedition_cost_detail.subtotal
                                                                    ELSE 0
                                                                END) AS BiayaEkspedisi")
                                                )
                                                ->where([
                                                    ['expedition_cost.status_biaya', '=', 'posted']
                                                ])
                                                ->groupBy('sales_invoice.id');

                $invoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                        ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                            $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                        })
                                        ->select(
                                            'sales_invoice.id',
                                            //'sales_invoice.grand_total',
                                            'sales_invoice.tanggal_invoice',
                                            'sales_invoice.tanggal_jt',
                                            'sales_invoice.durasi_jt',
                                            'sales_invoice.created_by',
                                            'sales_invoice.flag_pembayaran',
                                            'sales_order.id_customer',
                                            DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total')
                                        )
                                        ->where([
                                            ['sales_invoice.id', '=', $idInvoice]
                                        ])
                                        ->first();
                $items = new stdClass();

                if ($invoice != null && $mode == 'posting') {
                    // $arBalance = new AccountReceiveableBalance();
                    // $arBalance->id_invoice = $invoice->id;
                    // $arBalance->id_customer = $invoice->id_customer;
                    // $arBalance->nominal_invoice = $invoice->grand_total;
                    // $arBalance->nominal_outstanding = $invoice->grand_total;
                    // $arBalance->tanggal_invoice = $invoice->tanggal_invoice;
                    // $arBalance->tanggal_jt = $invoice->tanggal_jt;
                    // $arBalance->created_by = $invoice->created_by;
                    // $arBalance->save();
                    $items = AccountReceiveableBalance::firstOrCreate(
                        [
                            'id_invoice' => $invoice->id,
                            'id_customer' => $invoice->id_customer
                        ],
                        [
                            'nominal_invoice' => $invoice->grand_total,
                            'nominal_outstanding' => $invoice->grand_total,
                            'tanggal_invoice' => $invoice->tanggal_invoice,
                            'tanggal_jt' => $invoice->tanggal_jt,
                            'created_by' => $invoice->created_by
                        ]
                    );
                }
                else if ($invoice != null && $mode == 'cancel_payment') {
                    if ($invoice->flag_pembayaran == 0) {
                        // $arBalance = new AccountReceiveableBalance();
                        // $arBalance->id_invoice = $invoice->id;
                        // $arBalance->id_customer = $invoice->id_customer;
                        // $arBalance->nominal_invoice = $invoice->grand_total;
                        // $arBalance->nominal_outstanding = $invoice->grand_total;
                        // $arBalance->tanggal_invoice = $invoice->tanggal_invoice;
                        // $arBalance->tanggal_jt = $invoice->tanggal_jt;
                        // $arBalance->created_by = $invoice->created_by;
                        // $arBalance->save();

                        $items = AccountReceiveableBalance::firstOrCreate(
                            [
                                'id_invoice' => $invoice->id,
                                'id_customer' => $invoice->id_customer
                            ],
                            [
                                'nominal_invoice' => $invoice->grand_total,
                                'nominal_outstanding' => $invoice->grand_total,
                                'tanggal_invoice' => $invoice->tanggal_invoice,
                                'tanggal_jt' => $invoice->tanggal_jt,
                                'created_by' => $invoice->created_by
                            ]
                        );
                    }
                    else if ($invoice->flag_pembayaran == 2) {
                        $totalPembayaranInv = AccountReceiveableDetail::where([
                            ['id_invoice', '=', $idInvoice]
                        ])
                        ->sum('nominal_bayar');

                        // $arBalance = new AccountReceiveableBalance();
                        // $arBalance->id_invoice = $invoice->id;
                        // $arBalance->id_customer = $invoice->id_customer;
                        // $arBalance->nominal_invoice = $invoice->grand_total;
                        // $arBalance->nominal_outstanding = $invoice->grand_total - $totalPembayaranInv;
                        // $arBalance->tanggal_invoice = $invoice->tanggal_invoice;
                        // $arBalance->tanggal_jt = $invoice->tanggal_jt;
                        // $arBalance->created_by = $invoice->created_by;
                        // $arBalance->save();

                        $items = AccountReceiveableBalance::firstOrCreate(
                            [
                                'id_invoice' => $invoice->id,
                                'id_customer' => $invoice->id_customer
                            ],
                            [
                                'nominal_invoice' => $invoice->grand_total,
                                'nominal_outstanding' => $invoice->grand_total - $totalPembayaranInv,
                                'tanggal_invoice' => $invoice->tanggal_invoice,
                                'tanggal_jt' => $invoice->tanggal_jt,
                                'created_by' => $invoice->created_by
                            ]
                        );
                    }
                }
            }
            else {

                $dataBiayaEkspedisi = SalesInvoice::leftJoin('sales_invoice_detail', 'sales_invoice_detail.id_invoice', '=', 'sales_invoice.id')
                                                ->leftJoin('expedition_cost_detail', 'expedition_cost_detail.id_sj', '=', 'sales_invoice_detail.id_sj')
                                                ->leftJoin('expedition_cost', 'expedition_cost_detail.id_cost', '=', 'expedition_cost.id')
                                                ->select(
                                                    'sales_invoice.id',
                                                    DB::raw("SUM(CASE
                                                                    WHEN expedition_cost_detail.flag_tagih = 'Y'
                                                                        THEN expedition_cost_detail.subtotal
                                                                    ELSE 0
                                                                END) AS BiayaEkspedisi")
                                                )
                                                ->where([
                                                    ['expedition_cost.status_biaya', '=', 'posted']
                                                ])
                                                ->groupBy('sales_invoice.id');

                $invoice = SalesInvoice::leftJoin('sales_order', 'sales_invoice.id_so', 'sales_order.id')
                                        ->leftJoinSub($dataBiayaEkspedisi, 'dataBiayaEkspedisi', function($dataBiayaEkspedisi) {
                                            $dataBiayaEkspedisi->on('sales_invoice.id', '=', 'dataBiayaEkspedisi.id');
                                        })
                                        ->select(
                                            'sales_invoice.id',
                                            //'sales_invoice.grand_total',
                                            'sales_invoice.tanggal_invoice',
                                            'sales_invoice.tanggal_jt',
                                            'sales_invoice.durasi_jt',
                                            'sales_invoice.created_by',
                                            'sales_invoice.flag_pembayaran',
                                            'sales_order.id_customer',
                                            DB::raw('(sales_invoice.grand_total + COALESCE(dataBiayaEkspedisi.BiayaEkspedisi,0)) AS grand_total')
                                        )
                                        ->where([
                                            ['sales_invoice.id', '=', $idInvoice]
                                        ])
                                        ->first();


                if ($invoice != null && $mode == 'cancel_payment') {
                    if ($invoice->flag_pembayaran == 0) {
                        $cekInvoice->nominal_outstanding = $invoice->grand_total;
                        $cekInvoice->updated_by = Auth::user()->user_name;
                        $cekInvoice->save();
                    }
                    else if ($invoice->flag_pembayaran == 2) {
                        $totalPembayaranInv = AccountReceiveableDetail::where([
                            ['id_invoice', '=', $idInvoice]
                        ])
                        ->sum('nominal_bayar');

                        $cekInvoice->nominal_outstanding = $invoice->grand_total - $totalPembayaranInv;
                        $cekInvoice->save();
                    }
                }
            }

            DB::commit();
            return ['error' => 'success'];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function PaymentARBalance($idInvoice, $nominal)
    {
        try {
            DB::beginTransaction();
            $arBalance = AccountReceiveableBalance::where('id_invoice', '=', $idInvoice)->first();

            if ($arBalance != null) {
                $invoice = SalesInvoice::find($idInvoice);

                if ($invoice != null && $nominal > 0) {
                    $arBalance->nominal_outstanding = $arBalance->nominal_outstanding - $nominal;
                    $arBalance->save();
                }
            }

            DB::commit();

            return ['error' => 'success'];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function RemoveARBalance($idInvoice, $mode)
    {
        try {
            DB::beginTransaction();
            $arBalance = AccountReceiveableBalance::where('id_invoice', '=', $idInvoice)->first();
            $txt = '';
            if ($arBalance != null) {
                $invoice = SalesInvoice::find($idInvoice);

                if ($invoice != null && $mode == "revisi") {

                    if ($invoice->flag_pembayaran == 0) {
                        $arBalance->delete();
                        $txt = 'success';
                    }
                    else {
                        $txt = 'failDueToPayment';
                    }
                }
                else if ($invoice != null && $mode == "payment") {
                    if ($invoice->flag_pembayaran == 1 && $arBalance->nominal_outstanding == 0) {
                        $arBalance->delete();
                    }
                    $txt = 'success';
                }
            }

            if ($txt != 'success') {
                DB::rollBack();
                return ['error' => $txt];
            }

            DB::commit();

            return ['error' => $txt];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function InsertAPBalance($idInvoice, $mode)
    {
        try {
            DB::beginTransaction();
            $cekInvoice = AccountPayableBalance::where('id_invoice', '=', $idInvoice)->first();

            if ($cekInvoice == null) {

                $invoice = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', 'purchase_order.id')
                                        ->select(
                                            'purchase_invoice.id',
                                            'purchase_invoice.grand_total',
                                            'purchase_invoice.tanggal_invoice',
                                            'purchase_invoice.tanggal_jt',
                                            'purchase_invoice.durasi_jt',
                                            'purchase_invoice.created_by',
                                            'purchase_invoice.flag_pembayaran',
                                            'purchase_order.id_supplier'
                                        )
                                        ->where([
                                            ['purchase_invoice.id', '=', $idInvoice]
                                        ])
                                        ->first();

                $items = new stdClass();

                if ($invoice != null && $mode == 'posting') {
                    // $apBalance = new AccountPayableBalance();
                    // $apBalance->id_invoice = $invoice->id;
                    // $apBalance->id_supplier = $invoice->id_supplier;
                    // $apBalance->nominal_invoice = $invoice->grand_total;
                    // $apBalance->nominal_outstanding = $invoice->grand_total;
                    // $apBalance->tanggal_invoice = $invoice->tanggal_invoice;
                    // $apBalance->tanggal_jt = $invoice->tanggal_jt;
                    // $apBalance->created_by = $invoice->created_by;
                    // $apBalance->save();

                    $items = AccountPayableBalance::firstOrCreate(
                        [
                            'id_invoice' => $invoice->id,
                            'id_supplier' => $invoice->id_supplier
                        ],
                        [
                            'nominal_invoice' => $invoice->grand_total,
                            'nominal_outstanding' => $invoice->grand_total,
                            'tanggal_invoice' => $invoice->tanggal_invoice,
                            'tanggal_jt' => $invoice->tanggal_jt,
                            'created_by' => $invoice->created_by
                        ]
                    );
                }
                else if ($invoice != null && $mode == 'cancel_payment') {
                    if ($invoice->flag_pembayaran == 0) {
                        // $apBalance = new AccountPayableBalance();
                        // $apBalance->id_invoice = $invoice->id;
                        // $apBalance->id_supplier = $invoice->id_supplier;
                        // $apBalance->nominal_invoice = $invoice->grand_total;
                        // $apBalance->nominal_outstanding = $invoice->grand_total;
                        // $apBalance->tanggal_invoice = $invoice->tanggal_invoice;
                        // $apBalance->tanggal_jt = $invoice->tanggal_jt;
                        // $apBalance->created_by = $invoice->created_by;
                        // $apBalance->save();

                        $items = AccountPayableBalance::firstOrCreate(
                            [
                                'id_invoice' => $invoice->id,
                                'id_supplier' => $invoice->id_supplier
                            ],
                            [
                                'nominal_invoice' => $invoice->grand_total,
                                'nominal_outstanding' => $invoice->grand_total,
                                'tanggal_invoice' => $invoice->tanggal_invoice,
                                'tanggal_jt' => $invoice->tanggal_jt,
                                'created_by' => $invoice->created_by
                            ]
                        );
                    }
                    else if ($invoice->flag_pembayaran == 2) {
                        $totalPembayaranInv = AccountPayableDetail::where([
                            ['id_invoice', '=', $idInvoice]
                        ])
                        ->sum('nominal_bayar');

                        // $apBalance = new AccountPayableBalance();
                        // $apBalance->id_invoice = $invoice->id;
                        // $apBalance->id_supplier = $invoice->id_supplier;
                        // $apBalance->nominal_invoice = $invoice->grand_total;
                        // $apBalance->nominal_outstanding =
                        // $apBalance->tanggal_invoice = $invoice->tanggal_invoice;
                        // $apBalance->tanggal_jt = $invoice->tanggal_jt;
                        // $apBalance->created_by = $invoice->created_by;
                        // $apBalance->save();

                        $items = AccountPayableBalance::firstOrCreate(
                            [
                                'id_invoice' => $invoice->id,
                                'id_supplier' => $invoice->id_supplier
                            ],
                            [
                                'nominal_invoice' => $invoice->grand_total,
                                'nominal_outstanding' => $invoice->grand_total - $totalPembayaranInv,
                                'tanggal_invoice' => $invoice->tanggal_invoice,
                                'tanggal_jt' => $invoice->tanggal_jt,
                                'created_by' => $invoice->created_by
                            ]
                        );
                    }
                }
            }
            else {

                $invoice = PurchaseInvoice::leftJoin('purchase_order', 'purchase_invoice.id_po', 'purchase_order.id')
                                        ->select(
                                            'purchase_invoice.id',
                                            'purchase_invoice.grand_total',
                                            'purchase_invoice.tanggal_invoice',
                                            'purchase_invoice.tanggal_jt',
                                            'purchase_invoice.durasi_jt',
                                            'purchase_invoice.created_by',
                                            'purchase_invoice.flag_pembayaran',
                                            'purchase_order.id_supplier'
                                        )
                                        ->where([
                                            ['purchase_invoice.id', '=', $idInvoice]
                                        ])
                                        ->first();


                if ($invoice != null && $mode == 'cancel_payment') {
                    if ($invoice->flag_pembayaran == 0) {
                        $cekInvoice->nominal_outstanding = $invoice->grand_total;
                        $cekInvoice->updated_by = Auth::user()->user_name;
                        $cekInvoice->save();
                    }
                    else if ($invoice->flag_pembayaran == 2) {
                        $totalPembayaranInv = AccountPayableDetail::where([
                            ['id_invoice', '=', $idInvoice]
                        ])
                        ->sum('nominal_bayar');

                        $cekInvoice->nominal_outstanding = $invoice->grand_total - $totalPembayaranInv;
                        $cekInvoice->save();
                    }
                }
            }

            DB::commit();
            return ['error' => 'success'];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function PaymentAPBalance($idInvoice, $nominal) {
        $apBalance = AccountPayableBalance::where('id_invoice', '=', $idInvoice)->first();

        if ($apBalance != null) {
            $invoice = PurchaseInvoice::find($idInvoice);

            if ($invoice != null && $nominal > 0) {
                $apBalance->nominal_outstanding = $apBalance->nominal_outstanding - $nominal;
                $apBalance->save();
            }
        }

        return '';
    }

    public static function RemoveAPBalance($idInvoice, $mode)
    {
        try {
            DB::beginTransaction();
            $apBalance = AccountPayableBalance::where('id_invoice', '=', $idInvoice)->first();
            $txt = '';
            if ($apBalance != null) {
                $invoice = PurchaseInvoice::find($idInvoice);

                if ($invoice != null && $mode == "revisi") {
                    $txt = '';
                    if ($invoice->flag_pembayaran == 0) {
                        $apBalance->delete();
                        $txt = 'success';
                    }
                    else {
                        $txt = 'failDueToPayment';
                    }
                }
                else if ($invoice != null && $mode == "payment") {
                    if ($invoice->flag_pembayaran == 1 && $apBalance->nominal_outstanding == 0) {
                        $apBalance->delete();
                    }
                    $txt = 'success';
                }
            }

            if ($txt != 'success') {
                DB::rollBack();
                return ['error' => $txt];
            }

            DB::commit();

            return ['error' => $txt];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function PostGLKasBank($module, $akun, $akunTransaksi, $akunDetail, $tgl, $nominal, $idArAp) {
        try {
            DB::beginTransaction();

            if ($akunTransaksi == 0 || $akunDetail == 0) {
                return ['error' => 'Akun Tidak dapat ditemukan'];
            }

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));

            $countKode = DB::table('gl_kas_bank')
                        ->select(DB::raw("MAX(RIGHT(nomor_kas_bank,2)) AS angka"))
                        //->whereYear('tanggal_transaksi', $thnPeriode)
                        ->whereDate('tanggal_transaksi', $tgl)
                        ->first();

            $count = $countKode->angka;
            $counter = $count + 1;

            $kodeTgl = Carbon::parse($tgl)->format('ymd');

            if ($counter < 10) {
                $nmrKB = "kb-cv-".$kodeTgl."0".$counter;
            }
            else {
                $nmrKB = "kb-cv-".$kodeTgl.$counter;
            }

            if ($module == "piutang") {
                $jenisTransaksi = 1;
                $keterangan = "Pembayaran Piutang Dagang";
            }
            elseif ($module == "hutang") {
                $jenisTransaksi = 2;
                $keterangan = "Pembayaran Hutang Dagang";
            }
            elseif ($module == "penjualan") {
                $jenisTransaksi = 2;
                $keterangan = "Penjualan";
            }

            $kasBank = new GLKasBank();
            $kasBank->nomor_kas_bank = $nmrKB;
            $kasBank->id_account = $akun;
            $kasBank->id_ar_ap = $idArAp;
            $kasBank->id_account_sub = $akunTransaksi;
            $kasBank->jenis_transaksi = $jenisTransaksi;
            $kasBank->nominal_transaksi = $nominal;
            $kasBank->tanggal_transaksi = $tgl;
            $kasBank->status = 'posted';
            $kasBank->jenis = 'system';
            $kasBank->created_by = Auth::user()->user_name;
            $kasBank->save();

            $kasBankDetail = new GLKasBankDetail();
            $kasBankDetail->id_kas_bank = $kasBank->id;
            $kasBankDetail->id_account = $akunDetail;
            $kasBankDetail->nominal = $nominal;
            $kasBankDetail->keterangan = $keterangan;
            $kasBankDetail->save();

            DB::commit();
            return ['error' => ''];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function PostJournal($module, $moduleId, $akunDebet, $akunKredit, $tgl, $nominal, $jenis)
    {
        try {
            DB::beginTransaction();

            if ($akunDebet == "" || $akunKredit == "") {
                return ['error' => 'Akun Tidak dapat ditemukan'];
            }

            $blnPeriode = date("m", strtotime($tgl));
            $thnPeriode = date("Y", strtotime($tgl));

            $countKode = DB::table('gl_journal')
                            ->select(DB::raw("MAX(RIGHT(kode_ref,4)) AS angka"))
                            ->whereMonth('tanggal_transaksi', $blnPeriode)
                            ->whereYear('tanggal_transaksi', $thnPeriode)
                            ->where([
                                ['sumber', '=', $module]
                            ])
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;
            $romawiTahun = strtolower(Helper::romawi(date("y", strtotime($tgl))));

            if ($module == "sales_invoice")
                $kodeSumber = "jpl";
            elseif ($module == "purchase_invoice")
                $kodeSumber = "jpb";
            elseif ($module == "kas_masuk")
                $kodeSumber = "kkm";
            elseif ($module == "kas_keluar")
                $kodeSumber = "kkk";
            elseif ($module == "bank_keluar")
                $kodeSumber = "bbk";
            elseif ($module == "bank_masuk")
                $kodeSumber = "bbm";

            if ($counter < 10) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."000".$counter;
            }
            elseif ($counter < 100) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."00".$counter;
            }
            elseif ($counter < 1000) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."0".$counter;
            }
            else {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'.$counter;
            }

            $journal = new GLJournal();
            $journal->kode_ref = $nmrRef;
            $journal->nominal = $nominal;
            $journal->tanggal_transaksi = $tgl;
            $journal->status = 'posted';
            $journal->jenis = $jenis;
            $journal->id_sumber = $moduleId;
            $journal->sumber = $module;
            $journal->created_by = 'SYSTEM';
            $journal->save();

            if ($akunDebet != "") {

                if ($module == "purchase_invoice") {

                    $inv = PurchaseInvoice::find($moduleId);
                    $settings = GLAccountSettings::find(1);

                    if ($inv->flag_ppn != "N") {
                        if ($settings->id_account_pajak_masuk != null) {
                            $journalDpp = new GLJournalDetail();
                            $journalDpp->id_journal = $journal->id;
                            $journalDpp->id_account = $akunDebet;
                            $journalDpp->nominal = $inv->dpp;
                            $journalDpp->tanggal_transaksi = $tgl;
                            $journalDpp->id_sumber = $moduleId;
                            $journalDpp->sumber = $module;
                            $journalDpp->side = "debet";
                            $journalDpp->created_by = 'SYSTEM';
                            $journalDpp->save();

                            $journalPPn = new GLJournalDetail();
                            $journalPPn->id_journal = $journal->id;
                            $journalPPn->id_account = $settings->id_account_pajak_masuk;
                            $journalPPn->nominal = $inv->ppn;
                            $journalPPn->tanggal_transaksi = $tgl;
                            $journalPPn->id_sumber = $moduleId;
                            $journalPPn->sumber = $module;
                            $journalPPn->side = "debet";
                            $journalPPn->created_by = 'SYSTEM';
                            $journalPPn->save();
                        }
                    }
                    else {
                        $journalModule = new GLJournalDetail();
                        $journalModule->id_journal = $journal->id;
                        $journalModule->id_account = $akunDebet;
                        $journalModule->nominal = $nominal;
                        $journalModule->tanggal_transaksi = $tgl;
                        $journalModule->id_sumber = $moduleId;
                        $journalModule->sumber = $module;
                        $journalModule->side = "debet";
                        $journalModule->created_by = 'SYSTEM';
                        $journalModule->save();
                    }

                }
                else {
                    $journalModule = new GLJournalDetail();
                    $journalModule->id_journal = $journal->id;
                    $journalModule->id_account = $akunDebet;
                    $journalModule->nominal = $nominal;
                    $journalModule->tanggal_transaksi = $tgl;
                    $journalModule->id_sumber = $moduleId;
                    $journalModule->sumber = $module;
                    $journalModule->side = "debet";
                    $journalModule->created_by = 'SYSTEM';
                    $journalModule->save();
                }

            }

            if ($akunKredit != "") {
                if ($module == "sales_invoice") {
                    $inv = SalesInvoice::find($moduleId);
                    $settings = GLAccountSettings::find(1);

                    if ($inv->flag_ppn != "N") {
                        if ($settings->id_account_pajak_keluar != null) {
                            $journalDpp = new GLJournalDetail();
                            $journalDpp->id_journal = $journal->id;
                            $journalDpp->id_account = $akunKredit;
                            $journalDpp->nominal = $inv->dpp;
                            $journalDpp->tanggal_transaksi = $tgl;
                            $journalDpp->id_sumber = $moduleId;
                            $journalDpp->sumber = $module;
                            $journalDpp->side = "credit";
                            $journalDpp->created_by = 'SYSTEM';
                            $journalDpp->save();

                            $journalPPn = new GLJournalDetail();
                            $journalPPn->id_journal = $journal->id;
                            $journalPPn->id_account = $settings->id_account_pajak_keluar;
                            $journalPPn->nominal = $inv->ppn;
                            $journalPPn->tanggal_transaksi = $tgl;
                            $journalPPn->id_sumber = $moduleId;
                            $journalPPn->sumber = $module;
                            $journalPPn->side = "credit";
                            $journalPPn->created_by = 'SYSTEM';
                            $journalPPn->save();
                        }
                    }
                    else {
                        $journalModule = new GLJournalDetail();
                        $journalModule->id_journal = $journal->id;
                        $journalModule->id_account = $akunKredit;
                        $journalModule->nominal = $nominal;
                        $journalModule->tanggal_transaksi = $tgl;
                        $journalModule->id_sumber = $moduleId;
                        $journalModule->sumber = $module;
                        $journalModule->side = "credit";
                        $journalModule->created_by = 'SYSTEM';
                        $journalModule->save();
                    }
                }
                else {
                    $journalTransaksi = new GLJournalDetail();
                    $journalTransaksi->id_journal = $journal->id;
                    $journalTransaksi->id_account = $akunKredit;
                    $journalTransaksi->nominal = $nominal;
                    $journalTransaksi->tanggal_transaksi = $tgl;
                    $journalTransaksi->id_sumber = $moduleId;
                    $journalTransaksi->sumber = $module;
                    $journalTransaksi->side = "credit";
                    $journalTransaksi->created_by = 'SYSTEM';
                    $journalTransaksi->save();
                }
            }

            DB::commit();
            return ['error' => ''];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function RemoveJournal($module, $moduleId)
    {
        try {
            DB::beginTransaction();
            $entry = GLJournal::where([
                                    ['sumber', '=', $module],
                                    ['id_sumber', '=', $moduleId],
                                ])
                                ->first();

            $txt = '';
            if ($entry != null) {
                DB::table('gl_journal_detail')
                    ->where('id_journal', '=', $entry->id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => Auth::user()->user_name,
                    ]);

                $entry->deleted_by = Auth::user()->user_name;
                $entry->save();
                $entry->delete();
                $txt = 'success';
            }


            if ($txt != 'success') {
                DB::rollBack();
                return ['error' => $txt];
            }

            DB::commit();

            return ['error' => $txt];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function PostJournalKasBank($id, $user)
    {
        try {
            DB::beginTransaction();

            if ($id == "") {
                return ['error' => 'Akun Tidak dapat ditemukan'];
            }

            $kasBank = GLKasBank::find($id);


            $blnPeriode = date("m", strtotime($kasBank->tanggal_transaksi));
            $thnPeriode = date("Y", strtotime($kasBank->tanggal_transaksi));



            if ($kasBank->id_account == 1 && $kasBank->jenis_transaksi == 1)
                $module = "kas_masuk";
            elseif ($kasBank->id_account == 1 && $kasBank->jenis_transaksi == 2)
                $module = "kas_keluar";
            elseif ($kasBank->id_account == 2 && $kasBank->jenis_transaksi == 2)
                $module = "bank_keluar";
            elseif ($kasBank->id_account == 2 && $kasBank->jenis_transaksi == 1)
                $module = "bank_masuk";

            $countKode = DB::table('gl_journal')
                            ->select(DB::raw("MAX(RIGHT(kode_ref,4)) AS angka"))
                            ->whereMonth('tanggal_transaksi', $blnPeriode)
                            ->whereYear('tanggal_transaksi', $thnPeriode)
                            ->where([
                                ['sumber', '=', $module]
                            ])
                            ->first();
            $count = $countKode->angka;
            $counter = $count + 1;

            if ($kasBank->id_account == 1 && $kasBank->jenis_transaksi == 1)
                $kodeSumber = "kkm";
            elseif ($kasBank->id_account == 1 && $kasBank->jenis_transaksi == 2)
                $kodeSumber = "kkk";
            elseif ($kasBank->id_account == 2 && $kasBank->jenis_transaksi == 2)
                $kodeSumber = "bbk";
            elseif ($kasBank->id_account == 2 && $kasBank->jenis_transaksi == 1)
                $kodeSumber = "bbm";

            if ($counter < 10) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."000".$counter;
            }
            elseif ($counter < 100) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."00".$counter;
            }
            elseif ($counter < 1000) {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'."0".$counter;
            }
            else {
                $nmrRef = $kodeSumber.'/'.$thnPeriode.'/'.$blnPeriode.'/'.$counter;
            }

            $journal = new GLJournal();
            $journal->kode_ref = $nmrRef;
            $journal->nominal = $kasBank->nominal_transaksi;
            $journal->tanggal_transaksi = $kasBank->tanggal_transaksi;
            $journal->status = 'posted';
            $journal->jenis = 'input';
            $journal->id_sumber = $kasBank->id;
            $journal->sumber = $module;
            $journal->created_by = $user;
            $journal->save();

            $detailKasBank = GLKasBankDetail::where([
                                                    ['gl_kas_bank_detail.id_kas_bank', '=', $id]
                                                ])
                                                ->get();

            if ($detailKasBank != null) {
                $journalModule = new GLJournalDetail();
                $journalModule->id_journal = $journal->id;
                $journalModule->id_account = $kasBank->id_account_sub;
                $journalModule->nominal = $kasBank->nominal_transaksi;
                $journalModule->tanggal_transaksi = $kasBank->tanggal_transaksi;
                $journalModule->id_sumber = $kasBank->id;
                $journalModule->sumber = $module;
                $journalModule->deskripsi = str_replace("_", " ", $module);
                $journalModule->side = $kasBank->jenis_transaksi == 1 ? "debet" : "credit";
                $journalModule->created_by = $user;
                $journalModule->save();

                foreach ($detailKasBank as $detail) {
                    $journalModule = new GLJournalDetail();
                    $journalModule->id_journal = $journal->id;
                    $journalModule->id_account = $detail->id_account;
                    $journalModule->nominal = $detail->nominal;
                    $journalModule->tanggal_transaksi = $kasBank->tanggal_transaksi;
                    $journalModule->id_sumber = $kasBank->id;
                    $journalModule->sumber = $module;
                    $journalModule->deskripsi = $detail->keterangan;
                    $journalModule->side = $kasBank->jenis_transaksi == 1 ? "credit" : "debet";
                    $journalModule->created_by = $user;
                    $journalModule->save();
                }
            }

            DB::commit();
            return ['error' => ''];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }

    public static function RemoveJournalKasBank($moduleId)
    {
        try {
            DB::beginTransaction();
            $entry = GLJournal::where([
                                    ['jenis', '=', 'input'],
                                    ['id_sumber', '=', $moduleId],
                                ])
                                ->first();

            $txt = '';
            if ($entry != null) {
                DB::table('gl_journal_detail')
                    ->where('id_journal', '=', $entry->id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => Auth::user()->user_name,
                    ]);

                $entry->deleted_by = Auth::user()->user_name;
                $entry->save();
                $entry->delete();
                $txt = 'success';
            }


            if ($txt != 'success') {
                DB::rollBack();
                return ['error' => $txt];
            }

            DB::commit();

            return ['error' => $txt];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage()];
        }
    }
}
