<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfMeta extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pdf_meta', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('published')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
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
		Schema::drop('pdf_meta');
	}

}
