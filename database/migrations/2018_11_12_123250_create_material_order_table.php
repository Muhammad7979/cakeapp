<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number',100)->index();
            $table->string('material_sku',100)->index();
            $table->string('material_name',100);
            $table->string('material_price',100);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();


            $table->unique(['order_number','material_sku']);
//            $table->foreign('order_number')->references('order_number')->on('orders')->onDelete('cascade');
//            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_order');
    }
}
