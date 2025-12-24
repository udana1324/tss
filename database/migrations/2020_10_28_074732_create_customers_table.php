<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer');
            $table->string('nama_customer');
            $table->string('npwp_customer')->nullable();
            $table->string('telp_customer');
            $table->string('fax_customer')->nullable();
            $table->string('email_customer')->nullable();
            $table->string('kategori_customer');
            $table->double('limit_customer', 18,2)->nullable();
            $table->string('sales')->nullable();
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
        Schema::dropIfExists('customer');
    }
}
