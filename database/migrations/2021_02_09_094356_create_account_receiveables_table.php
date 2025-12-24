<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountReceiveablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_receiveable', function (Blueprint $table) {
            $table->id();
            $table->string("kode_ar");
            $table->string("id_customer");
            $table->string("rekening_pembayaran");
            $table->string("jenis_pembayaran");
            $table->string("keterangan");
            $table->double("nominal", 18,2);
            $table->double("nominal_potongan", 18,2);
            $table->date("tanggal");
            $table->string("flag_revisi");
            $table->string("flag_potongan");
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
        Schema::dropIfExists('account_receiveable');
    }
}
