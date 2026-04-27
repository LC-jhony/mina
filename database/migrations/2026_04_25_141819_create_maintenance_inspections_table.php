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
        Schema::create('maintenance_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_order_id')->constrained('maintenance_orders')->cascadeOnDelete();
            $table->string('category'); // e.g., fluids, filters, brakes
            $table->string('item_key'); // e.g., engine_oil, air_filter
            $table->string('item_label'); // e.g., "Nivel de Aceite", "Filtro de Aire"
            $table->string('value')->nullable(); // e.g., "80%", "Saturado", "Bueno"
            $table->string('status')->default('good'); // Mapping to InspectionStatus Enum
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['maintenance_order_id', 'item_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_inspections');
    }
};
