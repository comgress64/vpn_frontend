<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('fname');
            $table->string('lname');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('max_devices')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->dropColumn('fname');
            $table->dropColumn('lname');
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
            $table->dropColumn('max_devices');
        });
    }
}
