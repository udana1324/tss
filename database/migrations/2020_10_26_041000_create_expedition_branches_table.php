<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpeditionBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expedition_branch', function (Blueprint $table) {
            $table->id();
            $table->string('id_expedisi');
            $table->string('nama_cabang');
            $table->string('alamat_cabang');
            $table->string('kota_cabang');
            $table->string('telp_cabang');
            $table->string('default');
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
        Schema::dropIfExists('expedition_branch');
    }
}
