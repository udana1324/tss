<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving', function (Blueprint $table) {
            $table->id();
            $table->string("kode_penerimaan");
            $table->string("id_po");
            $table->string("id_alamat");
            $table->string("no_sj_supplier");
            $table->integer("jumlah_total_sj");
            $table->date("tanggal_sj");
            $table->date("tanggal_terima");
            $table->string("status_penerimaan");
            $table->integer("flag_revisi");
            $table->integer("flag_terms_po");
            $table->integer("flag_invoiced");
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
        Schema::dropIfExists('receiving');
    }
}
