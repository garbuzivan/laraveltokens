<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarbuzIvanLaravelTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
			$table->string('token')->unique();
			$table->integer('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
			$table->string('title')->nullable();
			$table->datetime('expiration')->nullable();
			$table->datetime('last_use')->nullable();
			$table->index('token');
			$table->index('user_id');
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
        Schema::dropIfExists('tokens');
    }
}
