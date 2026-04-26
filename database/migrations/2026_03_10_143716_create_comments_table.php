<?php
// database/migrations/2024_01_01_000004_create_comments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
            
            $table->index(['event_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};