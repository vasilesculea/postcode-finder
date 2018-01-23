<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('postcode', 8)->index();
            $table->string('escaped_postcode', 7)->index();
            $table->decimal('lat', 10, 7)->default(0);
            $table->decimal('lng', 10, 7)->default(0);
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postcodes');
    }
}
