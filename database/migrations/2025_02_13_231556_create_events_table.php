<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('priority_id')->nullable()->constrained();
            $table->string('calendar_id')->nullable();
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('reservation_time')->nullable();
            $table->string('status')->nullable();
            $table->string('url')->nullable();
            $table->string('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
