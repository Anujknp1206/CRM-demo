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
        Schema::create('recipes', function (Blueprint $table) {

            $table->id();

            $table->morphs('recipeable');

            $table->string('name'); // Standard, Heavy Duty etc
            $table->string('hi_name'); // Standard, Heavy Duty etc

            $table->text('notes')->nullable();
            $table->text('hi_notes')->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
