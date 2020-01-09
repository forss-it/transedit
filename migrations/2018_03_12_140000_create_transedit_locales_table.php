<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranseditLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transedit_locales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('language')->nullable();
            $table->timestamps();
        });

        \Dialect\TransEdit\Models\Locale::create(['name' => 'en', 'English']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transedit_locales');
    }
}
