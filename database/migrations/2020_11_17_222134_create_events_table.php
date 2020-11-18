<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {


        	$table->engine = 'InnoDB';
            $table->bigIncrements('id');

            $table->string('record_id', 32)->index();
			$table->foreign('record_id')->references('id')->on('records');

			$table->date('date');
			$table->string('name');
            $table->integer('number_of_events');
            $table->timestamps();
			$table->unique(['record_id', 'date', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
