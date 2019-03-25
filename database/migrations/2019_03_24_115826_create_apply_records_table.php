<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_records', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('position_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('name');
            $table->string('phone');
            $table->enum('gender', ['male', 'female']);
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
        Schema::dropIfExists('apply_records');
    }
}
