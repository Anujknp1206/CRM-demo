<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rfi_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rfi_id')->constrained()->cascadeOnDelete();

            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete(); 

            $table->integer('current_quantity');
            $table->integer('min_quantity');
            $table->integer('requested_quantity')->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0); // 🔥 NEW
            $table->integer('approved_quantity')->nullable(); // ✅ NEW
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // ✅ NEW
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfi_items');
    }
};
