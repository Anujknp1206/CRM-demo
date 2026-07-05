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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained();
            $table->foreignId('employee_id')->nullable()->constrained();
            $table->string('issue_no')->unique();
            $table->foreignId('bom_id')->nullable()->constrained();
            $table->date('issue_date');
            $table->time('issue_time');
            $table->enum('status', ['draft', 'partial', 'completed'])->default('draft');
            $table->text('remark')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
