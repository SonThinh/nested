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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->date('public_duration_start');
            $table->date('public_duration_end');
            $table->decimal('unit_price')->default(0)->unsigned();
            $table->string('tax_classification');
            $table->float('tax_rate')->unsigned();
            $table->string('tax_calculation_classification');
            $table->integer('stock_quantity')->unsigned();
            $table->integer('max_order')->default(0)->unsigned();
            $table->integer('min_order')->default(0)->unsigned();
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
