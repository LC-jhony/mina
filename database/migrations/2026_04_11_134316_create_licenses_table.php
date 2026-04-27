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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('license_categories')->restrictOnDelete();
            $table->string('license_number', 30)->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->timestamps();
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
