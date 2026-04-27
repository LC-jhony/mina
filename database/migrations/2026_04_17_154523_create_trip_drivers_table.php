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
        Schema::create('trip_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->enum('leg', ['outbound', 'return']);
            $table->unsignedTinyInteger('position')->comment('1=primary, 2=secondary');
            $table->timestamps();
            $table->unique(['trip_id', 'leg', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_drivers');
    }
};
