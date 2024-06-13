<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number',100)->unique();
            $table->string('salesman',50);
            $table->string('customer_name',50);
            $table->string('customer_email',50)->nullable();
            $table->string('customer_address',150)->nullable();
            $table->string('customer_phone',66)->nullable();
            $table->integer('weight');
            $table->integer('quantity');
            $table->integer('total_price');
            $table->integer('advance_price');
            $table->integer('payment_type');
            $table->integer('payment_status');
            $table->integer('order_type');
            $table->integer('order_status');
            $table->timestamp('delivery_date')->nullable();
            $table->string('delivery_time',50);
            $table->string('remarks',100)->nullable();
            $table->integer('branch_id');
            $table->string('branch_code',50);
            $table->string('assigned_to',50);
            $table->integer('user_id');
            $table->integer('is_active')->default(0);
            $table->integer('priority');
            $table->integer('photo_id')->unsigned()->default(0);;
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
