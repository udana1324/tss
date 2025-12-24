<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string("kode_item");
            $table->string("nama_item");
            $table->string("kategori_item")->nullable();
            $table->string("jenis_item")->nullable();
            $table->string("merk_item")->nullable();
            $table->string("satuan_item")->nullable();
            $table->decimal("panjang_item", 18,2)->nullable();
            $table->decimal("lebar_item", 18,2)->nullable();
            $table->decimal("tinggi_item", 18,2)->nullable();
            $table->decimal("berat_item", 18,2)->nullable();
            $table->double("harga_beli",18,2)->nullable();
            $table->double("harga_jual",18,2)->nullable();
            $table->decimal("stok_minimum",18,2)->nullable();
            $table->decimal("stok_maksimum",18,2)->nullable();
            $table->string("keterangan_item")->nullable();
            $table->string("product_image_path")->nullable();
            $table->string('active');
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
        Schema::dropIfExists('product');
    }
}
