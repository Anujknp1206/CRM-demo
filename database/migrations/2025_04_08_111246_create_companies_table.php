<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->string('pincode');
            $table->string('email');
            $table->string('website');
            $table->string('alternate_email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('gstin_no')->nullable();
            $table->string('rex_registration_no')->nullable();
            $table->string('iec_code')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
