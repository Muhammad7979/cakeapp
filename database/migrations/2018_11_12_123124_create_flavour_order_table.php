<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlavourOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flavour_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number',100)->index();
            $table->string('flavour_sku',100)->index();
            $table->string('flavour_name',100);
            $table->string('flavour_price',100);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();


            $table->unique(['order_number','flavour_sku']);
//            $table->foreign('order_number')->references('order_number')->on('orders')->onDelete('cascade');
//            $table->foreign('flavour_id')->references('id')->on('flavours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flavour_order');
    }
}
