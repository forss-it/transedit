<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranseditTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transedit_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('locale_id');
            $table->unsignedInteger('key_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('locale_id')->references('id')->on('transedit_locales');
            $table->foreign('key_id')->references('id')->on('transedit_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transedit_translations');
    }
}
