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
        Schema::create('issue_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_id')
                ->constrained('issue_returns')
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('condition_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('unit_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->decimal('return_qty', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_return_items');
    }
};
