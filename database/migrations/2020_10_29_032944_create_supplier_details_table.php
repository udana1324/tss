<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_detail', function (Blueprint $table) {
            $table->id();
            $table->string("id_supplier");
            $table->string("alamat_supplier");
            $table->string("kelurahan")->nullable();
            $table->string("kecamatan")->nullable();
            $table->string("kota")->nullable();
            $table->string("kode_pos")->nullable();
            $table->string("jenis_alamat")->nullable();
            $table->string("pic_alamat")->nullable();
            $table->string("telp_pic")->nullable();
            $table->string("default")->nullable();
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
        Schema::dropIfExists('supplier_detail');
    }
}
