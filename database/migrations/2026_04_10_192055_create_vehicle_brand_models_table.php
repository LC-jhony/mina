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
        Schema::create('vehicle_brand_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 80);
            $table->string('model', 80);
            $table->string('vehicle_type', 60);
            $table->unsignedTinyInteger('passenger_capacity')->default(1);
            $table->timestamps();
            $table->unique(['brand', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_brand_models');
    }
};
