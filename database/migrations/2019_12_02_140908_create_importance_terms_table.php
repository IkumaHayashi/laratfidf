<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportanceTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importance_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');
            $table->string('term', 50);
            $table->integer('frequency')->default(0);
            $table->double('tf', 8, 4)->default(0.0);
            $table->double('idf', 8, 4)->default(0.0);
            $table->double('tfidf', 8, 4)->virtualAs('tf * ( idf + 1 )');
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
        Schema::dropIfExists('importance_terms');
    }
}
