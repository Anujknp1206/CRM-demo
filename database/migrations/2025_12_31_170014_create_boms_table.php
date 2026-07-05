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
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('shift_id')->nullable()
                ->constrained('shifts')->nullOnDelete();
            $table->foreignId('incharge_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            // 🔥 CHECKED BY (REVIEW)
            $table->foreignId('review_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('checked_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            $table->foreignId('priority_id')->nullable()
                ->constrained('priorities')->nullOnDelete();

            $table->date('delivery_date')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('order_id');

            $table->string('bom_number')->nullable();
            $table->text('remarks')->nullable();
            $table->text('hi_remarks')->nullable();

            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');

            $table->timestamps();

            // FK (optional but recommended)
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
