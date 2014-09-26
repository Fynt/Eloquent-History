<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('history', function($table)
    {
      $table->increments('id');
      $table->integer('user_id')->unsigned()->nullable()->index();
      $table->string('action', 30);
      $table->integer('object_id')->unsigned();
      $table->string('object_table', 64)->index();
      $table->timestamps();

      $table->index(array('object_id', 'object_table'));
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('history');
  }

}
