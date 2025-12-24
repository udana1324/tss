<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order', function (Blueprint $table) {
            $table->id();
            $table->string("no_so");
            $table->string("id_customer");
            $table->string("no_po_customer");
            $table->string("id_alamat");
            $table->integer("jumlah_total_so");
            $table->integer("outstanding_so");
            $table->date("tanggal_so");
            $table->date("tanggal_request");
            $table->string("flag_ppn");
            $table->double("nominal_dp", 18,2);
            $table->double("sisa_dp", 18,2);
            $table->double("nominal_so_dpp", 18,2);
            $table->double("nominal_so_ppn", 18,2);
            $table->double("nominal_so_ttl", 18,2);
            $table->decimal("persentase_diskon", 18,2);
            $table->string("metode_pembayaran");
            $table->string("metode_kirim");
            $table->string("jenis_kirim");
            $table->string("path_po");
            $table->integer("durasi_jt");
            $table->integer("flag_revisi");
            $table->string("status_so");
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
        Schema::dropIfExists('sales_order');
    }
}
