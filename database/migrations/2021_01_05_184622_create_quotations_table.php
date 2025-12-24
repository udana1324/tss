<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation', function (Blueprint $table) {
            $table->id();
            $table->string("no_quotation");
            $table->string("id_customer");
            $table->string("id_alamat");
            $table->decimal("jumlah_total_quotation", 18,2);
            $table->date("tanggal_quotation");
            $table->double("nominal_quotation", 18,2);
            $table->string("metode_pembayaran");
            $table->integer("flag_revisi");
            $table->string("status_quotation");
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
        Schema::dropIfExists('quotation');
    }
}
