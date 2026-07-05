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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number')->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'online']);
            $table->string('transaction_reference')->nullable();
            $table->date('payment_date');
            $table->time('payment_time');
            $table->enum('status', ['completed', 'pending', 'partial', 'failed'])
                ->default('pending');
            $table->text('note')->nullable();
            $table->boolean('is_post_dated')->default(false);
            $table->date('post_date')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
