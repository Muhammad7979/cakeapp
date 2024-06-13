<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentHistoryColumnsToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //

            $table->integer('pending_amount')->default(0);
            $table->date('pending_amount_paid_date')->nullable();
            $table->string('pending_amount_paid_time',20)->nullable();
            $table->string('pending_amount_paid_branch',30)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn('pending_amount');
            $table->dropColumn('pending_amount_paid_date');
            $table->dropColumn('pending_amount_paid_time');
            $table->dropColumn('pending_amount_paid_branch');
        });
    }
}
