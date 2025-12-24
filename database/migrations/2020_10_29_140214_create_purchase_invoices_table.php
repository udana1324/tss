<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_invoice', function (Blueprint $table) {
            $table->id();
            $table->string("kode_invoice");
            $table->string("id_po");
            $table->double("dpp", 18,2);
            $table->double("ppn", 18,2);
            $table->double("grand_total", 18,2);
            $table->integer("ttl_qty");
            $table->string("flag_ppn");
            $table->date("tanggal_invoice");
            $table->integer("durasi_jt");
            $table->date("tanggal_jt");
            $table->string("flag_revisi");
            $table->string("flag_pembayaran");
            $table->integer("flag_terms_po");
            $table->string("status_invoice");
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
        Schema::dropIfExists('purchase_invoice');
    }
}
