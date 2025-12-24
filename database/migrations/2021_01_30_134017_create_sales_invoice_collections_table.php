<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesInvoiceCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoice_collection', function (Blueprint $table) {
            $table->id();
            $table->string("kode_tf");
            $table->string("id_customer");
            $table->string("id_alamat");
            $table->string("pic_penerima");
            $table->double("nominal", 18,2);
            $table->date("tanggal");
            $table->string("flag_revisi");
            $table->string("flag_approved");
            $table->string("status");
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
        Schema::dropIfExists('sales_invoice_collection');
    }
}
