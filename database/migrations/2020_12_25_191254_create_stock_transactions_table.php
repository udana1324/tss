<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi');
            $table->string('id_item');
            $table->decimal('qty_item',18,2);
            $table->date('tgl_transaksi');
            $table->string('jenis_transaksi');
            $table->string('transaksi');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transaction');
    }
}
