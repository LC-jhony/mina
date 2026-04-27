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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_model_id')->constrained('vehicle_brand_models')->restrictOnDelete();
            $table->string('plate', 20)->unique();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage')->default(0);
            $table->enum('status', ['available', 'on_trip', 'in_maintenance', 'out_of_service'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
