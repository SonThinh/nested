<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelHasFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_files', function (Blueprint $table) {
            $table->string('model_id');
            $table->string('model_type');
            $table->foreignUuid('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->index(['model_id', 'model_type']);
            $table->primary(['model_id', 'model_type', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_files');
    }
}
