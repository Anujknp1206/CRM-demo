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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('bom_part_id')
                ->nullable()
                ->constrained('bom_parts')
                ->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()
                ->references('id')->on('employees')->nullOnDelete();
            $table->enum('status', [
                'pending',
                'assigned',
                'in_progress',
                'completed',
                'on_hold'
            ])->default('pending');
            $table->text('remarks')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->text('notes')->nullable();
            $table->text('hi_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
