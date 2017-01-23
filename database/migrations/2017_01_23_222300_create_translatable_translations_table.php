<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTranslatableTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translatable_translations', function ($table) {

            $table->increments('id');
            $table->string('translatable_type');
            $table->integer('translatable_id');
            $table->string('key');
            $table->text('translation');
            $table->text('language')->nullable();
            $table->timestamps();

            $table->index(['translatable_id', 'translatable_model']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translatable_translations');
    }
}