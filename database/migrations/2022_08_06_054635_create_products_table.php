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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku');
            $table->longText('description');
            $table->decimal('price_retail', 11, 2);
            $table->decimal('price_grosir', 11, 2);
            $table->decimal('price_modal', 11, 2);
            $table->decimal('price_dropship', 11, 2);
            $table->integer('stock');
            $table->decimal('weight');
            $table->unsignedBigInteger('store_id');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
