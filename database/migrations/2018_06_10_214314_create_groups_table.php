<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('telegram_id');
            $table->string('type');
            $table->string('title');
            $table->string('language', 5);
            $table->string('currency', 3);
            $table->timestamp('created_at');
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_id');
            $table->string('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
