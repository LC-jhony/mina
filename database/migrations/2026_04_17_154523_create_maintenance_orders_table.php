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
        Schema::create('maintenance_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->foreignId('mechanic_id')->constrained('mechanics')->restrictOnDelete();
            $table->foreignId('maintenance_type_id')->constrained('maintenance_types')->restrictOnDelete();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->unsignedInteger('mileage_at_service');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->timestamps();
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_orders');
    }
};
