<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->morphs('notifiable');
            // $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->string('icon')->nullable();
            $table->string('picture')->nullable();
            $table->string('priority')->nullable();
            $table->text('description')->nullable();
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('type');
            $table->string('icon')->nullable();
            $table->string('picture')->nullable();
            $table->string('priority');
            $table->text('description');
            $table->boolean('read');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
