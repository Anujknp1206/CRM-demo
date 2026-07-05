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
        Schema::create('bom_parts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('bom_id')->constrained()->cascadeOnDelete();
            $table->decimal('progress_percent', 5, 2)
                ->default(0);
            $table->string('part_name')->nullable();
            $table->string('hi_part_name')->nullable();
            $table->decimal('weightage', 4, 2)->default(0);
            $table->foreignId('spec_id')->nullable()
                ->constrained('specifications')
                ->nullOnDelete();
            $table->foreignId('shift_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_parts');
    }
};
