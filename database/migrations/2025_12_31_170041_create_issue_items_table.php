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
        Schema::create('issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_item_id')
                ->constrained('bom_items')
                ->cascadeOnDelete();
            $table->foreignId('issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('condition_id')->constrained();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->foreignId('unit_id')->constrained();
            $table->decimal('requested_qty', 12, 2);
            $table->decimal('issued_qty', 12, 2)->default(0);
            $table->decimal('pending_qty', 12, 2)->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_items');
    }
};
