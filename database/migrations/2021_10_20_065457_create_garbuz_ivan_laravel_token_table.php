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
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->morphs('user');
            $table->string('title')->nullable();
            $table->datetime('expiration')->nullable();
            $table->datetime('last_use')->nullable();
            $table->index('token');
            $table->index('user_id');
            $table->timestamps();
        });
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->morphs('user');
            $table->integer('access_token_id')->nullable()->references('id')->on('access_tokens')->onDelete('cascade');
            $table->datetime('expiration')->nullable();
            $table->index('token');
            $table->index('user_id');
            $table->index('access_token_id');
            $table->timestamps();
        });
        Schema::create('global_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('title')->nullable();
            $table->datetime('expiration')->nullable();
            $table->datetime('last_use')->nullable();
            $table->index('token');
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
        Schema::dropIfExists('access_tokens');
        Schema::dropIfExists('refresh_tokens');
        Schema::dropIfExists('global_tokens');
    }
}
