<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery', function (Blueprint $table) {
            $table->id();
            $table->string("kode_pengiriman");
            $table->string("no_sj_manual")->nullable();
            $table->string("id_so");
            $table->string("id_alamat");
            $table->integer("jumlah_total_sj");
            $table->date("tanggal_sj");
            $table->date("tanggal_kirim")->nullable();
            $table->string("metode_pengiriman");
            $table->string("status_pengiriman");
            $table->integer("flag_revisi");
            $table->integer("flag_terms_so");
            $table->integer("flag_invoiced");
            $table->integer("flag_terkirim");
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
        Schema::dropIfExists('delivery');
    }
}
