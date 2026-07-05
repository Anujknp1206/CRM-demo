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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->nullable()->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();

            $table->unsignedBigInteger('user_id');          // creator
            $table->unsignedBigInteger('assigned_user_id'); // staff

            $table->date('order_date');
            $table->string('po_number')->nullable();
            $table->date('po_date')->nullable();
            $table->date('delivery_date')->nullable();

            // Customer snapshot
            $table->string('contact_person')->nullable();
            $table->text('delivery_address')->nullable();

            $table->text('remark')->nullable();
            $table->text('hi_remark')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('hi_terms_conditions')->nullable();

            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);

            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->integer('progress_percent')->default(0);
            $table->boolean('is_delayed')->default(false);
            $table->string('currency')->default('INR');
            $table->decimal('conversion_rate', 12, 4)->default(1);

            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])
                ->default('unpaid');
            $table->enum('status', [
                'pending',          // Order created
                'confirmed',        // Approved
                'planning',         // Planning created
                'in_production',    // Work started
                'on_hold',          // Work paused
                'delayed',          // Behind schedule
                'ready',            // Production done
                'dispatched',       // Delivered / shipped
                'cancelled'
            ])->default('pending');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
