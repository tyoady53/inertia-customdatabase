<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extend_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('extend_id');
            $table->text('file_name');
            $table->string('type');
            $table->timestamps();

            $table->foreign('extend_id')->references('id')->on('extends');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extend_files');
    }
};
