<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Name of the menu item (e.g., "Home", "About Us")
            $table->string('slug')->nullable(); // The page slug or custom URL
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade'); // Parent menu item for submenus
            $table->enum('link_type', ['page', 'route', 'external', 'manual'])->default('manual');
            $table->string('link_value')->nullable(); // ID or URL depending on type
            $table->integer('order')->default(0); // Order in which the menu item will appear
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
