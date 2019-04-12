<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errands', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('user_id')->unsigned()->index();
            $table->text('content');
            $table->text('hidden_content');
            $table->string('appointment_time');
            $table->enum('gender_limit',['male','female','noLimit']);
            $table->decimal('expense');
            $table->string('location_name');
            $table->string('location_address');
            $table->double('location_latitude',10,6);
            $table->double('location_longitude',10,6);
            $table->string('payment_out_trade_no');
            $table->enum('status',['waitingPay','pending','done']);
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
        Schema::dropIfExists('errands');
    }
}
