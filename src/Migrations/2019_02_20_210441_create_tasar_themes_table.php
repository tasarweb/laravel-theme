<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasarThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasar_themes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dir')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('version')->nullable();
            $table->string('author')->nullable();
            $table->string('url')->nullable();
            $table->string('authorUrl')->nullable();
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('tasar_tables');
    }
}
