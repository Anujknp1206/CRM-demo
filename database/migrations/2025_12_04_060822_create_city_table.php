<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')
                ->constrained('states')
                ->onDelete('cascade');      // Delete cities when state deleted
            $table->string('name');          // City Name
            // $table->string('postal_code', 20)->nullable(); // ZIP/PIN
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
