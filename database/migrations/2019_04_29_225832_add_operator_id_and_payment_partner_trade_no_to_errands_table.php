<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperatorIdAndPaymentPartnerTradeNoToErrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('errands', function (Blueprint $table) {
            $table->integer('operator_id')->unsigned()->index()->nullable();
            $table->string('payment_partner_trade_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('errands', function (Blueprint $table) {
            $table->dropColumn('operator_id');
            $table->dropColumn('payment_partner_trade_no');
        });
    }
}