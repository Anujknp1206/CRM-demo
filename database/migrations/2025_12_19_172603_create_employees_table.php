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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // relations
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            // personal info
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('father_name');
            $table->text('address')->nullable();
            $table->string('pincode', 10)->nullable();

            $table->string('email')->nullable();
            $table->string('mobile', 15)->nullable();

            $table->string('previous_company')->nullable();
            $table->string('experience_years')->nullable();
            $table->string('reference_name')->nullable();

            // office info
            $table->date('joining_date')->nullable();
            $table->string('user_id')->nullable();
            $table->string('password')->nullable();
            $table->string('pan', 10)->nullable();

            // bank info
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('ifsc_code')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
