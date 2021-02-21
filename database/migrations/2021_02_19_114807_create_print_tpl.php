<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintTpl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_tpl', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tpl_name')->unique();
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('padding')->default("0mm 5mm 0mm 5mm");
            $table->longText('tpl_content');
            $table->longText('params_examples');
            $table->unsignedInteger('updated_at');
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_tpl');
    }
}
