<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreferrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preference', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pt');
            $table->string('alamat_pt');
            $table->string('kelurahan_pt');
            $table->string('kecamatan_pt');
            $table->string('kota_pt');
            $table->string('npwp_pt');
            $table->string('rekening');
            $table->string('telp_pt');
            $table->string('email_pt');
            $table->string('website_pt');
            $table->string('flag_do');
            $table->string('flag_rcv');
            $table->string('flag_quo');
            $table->string('flag_so');
            $table->string('flag_po');
            $table->string('flag_inv_sale');
            $table->string('flag_inv_purc');
            $table->string('flag_inv_dp');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('preference');
    }
}
