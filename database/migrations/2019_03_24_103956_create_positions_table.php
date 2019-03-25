<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index();
            $table->string('title');
            $table->string('covers')->comment('封面图片');
            $table->string('detail_info');
            $table->string('contact_man')->comment('联系人姓名');
            $table->string('contact_phone')->comment('联系电话');
            $table->integer('quantity')->comment('数量');
            $table->integer('apply_quantity')->default(0)->comment('已报名数量');
            $table->string('salary')->comment('薪水');
            $table->string('work_address')->comment('工作地址');
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
        Schema::dropIfExists('positions');
    }
}
