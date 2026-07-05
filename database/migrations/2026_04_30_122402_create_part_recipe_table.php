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
        Schema::create('part_recipe', function (Blueprint $table) {

            $table->id();

            $table->foreignId('recipe_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('weightage', 8, 2)
                ->nullable();
            $table->foreignId('part_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('part_recipe');
    }
};
