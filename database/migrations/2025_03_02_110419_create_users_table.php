<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('role_id')->nullable(); // Optional, if not using Spatie
            $table->unsignedBigInteger('parent_id')->nullable(); // e.g., Vendor/Creator
            $table->unsignedBigInteger('created_by')->nullable(); // Track user who created
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('core_password')->nullable(); // Store original password if needed (⚠️ Security risk)

            $table->date('joining_date')->nullable();
            $table->string('address')->nullable();
            $table->text('desigination')->nullable();
            $table->string('photo')->nullable();

            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });


    

    
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

