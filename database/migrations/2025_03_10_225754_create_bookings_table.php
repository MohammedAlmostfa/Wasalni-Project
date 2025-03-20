<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates the 'bookings' table with the necessary columns and constraints.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->tinyInteger('status')->default(0);
            $table->integer('seats_number');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string("nots")->nullable();
            $table->timestamps();
            $table->index(['status', 'trip_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'bookings' table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
