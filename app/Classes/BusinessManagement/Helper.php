<?php

namespace App\Classes\BusinessManagement;

use App\Models\Accounting\TaxSettings;
use App\Models\ActionLog;
use App\Models\Sales\SalesCashierDetail;
use App\Models\Setting\Module;
use App\Models\Stock\StockTransaction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;

class Helper
{
    public static function romawi($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public static function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim(Helper::penyebut($nilai));
        } else {
            $hasil = trim(Helper::penyebut($nilai));
        }
        return $hasil;
    }

    public static function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = Helper::penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = Helper::penyebut($nilai/10)." puluh". Helper::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . Helper::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = Helper::penyebut($nilai/100) . " ratus" . Helper::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . Helper::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = Helper::penyebut($nilai/1000) . " ribu" . Helper::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = Helper::penyebut($nilai/1000000) . " juta" . Helper::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = Helper::penyebut($nilai/1000000000) . " milyar" . Helper::penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = Helper::penyebut($nilai/1000000000000) . " trilyun" . Helper::penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    public static function number_to_words($number)
    {
        $before_comma = trim(Helper::to_word($number));
        $after_comma = trim(Helper::comma($number));
        if ($after_comma == "" || $after_comma == null) {
            return ucwords($results = $before_comma);
        }
        elseif (stristr($number,'.') < 1) {
            return ucwords($results = $before_comma);
        }
        else {
            return ucwords($results = $before_comma.' koma '.$after_comma);
        }

    }

    public static function to_word($number)
    {
        $words = "";
        $arr_number = array(
        "",
        "satu",
        "dua",
        "tiga",
        "empat",
        "lima",
        "enam",
        "tujuh",
        "delapan",
        "sembilan",
        "sepuluh",
        "sebelas");

        if($number<12)
        {
            $words = " ".$arr_number[$number];
        }
        else if($number<20)
        {
            $words = Helper::to_word($number-10)." belas";
        }
        else if($number<100)
        {
            $words = Helper::to_word($number/10)." puluh ".Helper::to_word($number%10);
        }
        else if($number<200)
        {
            $words = "seratus ".Helper::to_word($number-100);
        }
        else if($number<1000)
        {
            $words = Helper::to_word($number/100)." ratus ".Helper::to_word($number%100);
        }
        else if($number<2000)
        {
            $words = "seribu ".Helper::to_word($number-1000);
        }
        else if($number<1000000)
        {
            $words = Helper::to_word($number/1000)." ribu ".Helper::to_word($number%1000);
        }
        else if($number<1000000000)
        {
            $words = Helper::to_word($number/1000000)." juta ".Helper::to_word($number%1000000);
        }
        else
        {
            $words = "undefined";
        }
        return $words;
    }

    public static function comma($number)
    {
        $after_comma = stristr($number,'.');
        $arr_number = array(
        "nol",
        "satu",
        "dua",
        "tiga",
        "empat",
        "lima",
        "enam",
        "tujuh",
        "delapan",
        "sembilan");

        $results = "";
        $length = strlen($after_comma);
        $i = 1;
        while($i<$length)
        {
            $get = substr($after_comma,$i,1);
            $results .= " ".$arr_number[$get];
            $i++;
        }
        return $results;
    }

    public static function cekAksesPeriode ($date) {
        $blnPeriode = date("M", strtotime($date));

        $field = strtolower($blnPeriode);

        $akses = DB::table('transaction_period')
                            ->select($field)
                            ->where([
                                [$field, '=', 'Y']
                            ])
                            ->first();

        if($akses != null) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function CheckPPNPeriod($date) {
        $taxSettings = TaxSettings::leftJoin('tax_settings_ppn', 'tax_settings.ppn_percentage_id', '=', 'tax_settings_ppn.id')->first();

        if ($date > $taxSettings->ppn_start_date && ($taxSettings->ppn_end_date == null or $date <= $taxSettings->ppn_end_date)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function SubmitStockTransaction($mode, $transaction) {
        if ($mode == "post") {

            $details = SalesCashierDetail::select(
                                        'sales_cashier_detail.id',
                                        'sales_cashier_detail.id_item',
                                        'sales_cashier_detail.id_satuan',
                                        'sales_cashier_detail.qty_item'
                                    )
                                    ->where([
                                        ['sales_cashier_detail.id_sc', '=', $transaction->id]
                                    ])
                                    ->get();

            $transactionData = [];

            foreach ($details as $detail) {

                $stockTransaction = HelperDelivery::createStockTransaction($transaction, $detail);

                if (count($stockTransaction) > 0 ) {
                    array_push($transactionData, $stockTransaction);
                    $errorSourceAssign = 0;
                }
                else {
                    $errorSourceAssign = 1;
                }

                if ($errorSourceAssign == 1) {
                    $msg = 'Penjualan Barang '.strtoupper($transaction->no_ref).' Gagal Diposting! Terdapat Masalah saat pemilihan sumber stok barang!';
                    $status = 'warning';
                }

            }

            foreach ($transactionData as $dataSJ) {
                StockTransaction::insert($dataSJ);
            }

            $log = ActionLog::create([
                'module' => 'Sales Cashier',
                'action' => 'Posting',
                'desc' => 'Posting Sales Cashier',
                'username' => Auth::user()->user_name
            ]);
            $msg = 'Penjualan '.strtoupper($transaction->no_ref).' Telah Diposting!';
            $status = 'success';
        }
        else if ($mode == "update") {
            $delete = DB::table('stock_transaction')->where('kode_transaksi', '=', $transaction->no_ref)->delete();

            $details = SalesCashierDetail::select(
                                        'sales_cashier_detail.id',
                                        'sales_cashier_detail.id_item',
                                        'sales_cashier_detail.id_satuan',
                                        'sales_cashier_detail.qty_item'
                                    )
                                    ->where([
                                        ['sales_cashier_detail.id_sc', '=', $transaction->id]
                                    ])
                                    ->get();

            $transactionData = [];

            foreach ($details as $detail) {

                $stockTransaction = HelperDelivery::createStockTransaction($transaction, $detail);

                if (count($stockTransaction) > 0 ) {
                    array_push($transactionData, $stockTransaction);
                    $errorSourceAssign = 0;
                }
                else {
                    $errorSourceAssign = 1;
                }

                if ($errorSourceAssign == 1) {
                    $msg = 'Penjualan Barang '.strtoupper($transaction->no_ref).' Gagal Diposting! Terdapat Masalah saat pemilihan sumber stok barang!';
                    $status = 'warning';
                }

            }

            foreach ($transactionData as $dataSJ) {
                StockTransaction::insert($dataSJ);
            }

            $log = ActionLog::create([
                'module' => 'Sales Cashier',
                'action' => 'Posting',
                'desc' => 'Posting Sales Cashier',
                'username' => Auth::user()->user_name
            ]);
            $msg = 'Penjualan '.strtoupper($transaction->no_ref).' Telah Diposting!';
            $status = 'success';
        }

    }
}
