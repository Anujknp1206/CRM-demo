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
        Schema::create('item_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('from_unit_id')
                ->constrained('units')
                ->cascadeOnDelete();
            $table->foreignId('to_unit_id')
                ->constrained('units')
                ->cascadeOnDelete();
            $table->decimal('factor', 20, 6);
            $table->timestamps();
            $table->unique(
                ['item_id', 'from_unit_id', 'to_unit_id'],
                'item_unit_conversion_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_unit_conversions');
    }
};
