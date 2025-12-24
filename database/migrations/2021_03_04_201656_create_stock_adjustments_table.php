<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_adjustment', function (Blueprint $table) {
            $table->id();
            $table->string("kode_transaksi");
            $table->string("id_item");
            $table->decimal("qty_item", 18,2);
            $table->string("jenis_transaksi");
            $table->string("jenis_adjustment");
            $table->date("tgl_transaksi");
            $table->string("keterangan");
            $table->string("created_by")->nullable();
            $table->string("updated_by")->nullable();
            $table->timestamps();
            $table->string("deleted_by")->nullable();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_adjustment');
    }
}
