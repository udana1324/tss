<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_access', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('menu_id');
            $table->string('add', 1);
            $table->string('edit', 1);
            $table->string('delete', 1);
            $table->string('posting', 1);
            $table->string('print', 1);
            $table->string('export', 1);
            $table->string('approve', 1);
            $table->string('active');
            $table->string('action');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_access');
    }
}
