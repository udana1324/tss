<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string("no_po");
            $table->string("id_supplier");
            $table->string("id_alamat");
            $table->integer("jumlah_total_po");
            $table->integer("outstanding_po");
            $table->date("tanggal_po");
            $table->date("tanggal_request");
            $table->date("tanggal_deadline");
            $table->string("flag_ppn");
            $table->double("nominal_po_dpp", 18,2);
            $table->double("nominal_po_ppn", 18,2);
            $table->double("nominal_po_ttl", 18,2);
            $table->decimal("persentase_diskon", 18,2);
            $table->string("metode_pembayaran");
            $table->integer("durasi_jt");
            $table->integer("flag_revisi");
            $table->string("status_po");
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
        Schema::dropIfExists('purchase_order');
    }
}
