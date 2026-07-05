<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('tag_line')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('landline')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('auth_sign')->nullable();
            $table->string('website')->nullable();
            $table->string('google_a')->nullable();      // Possibly Google Analytics
            $table->string('google_web')->nullable();    // Possibly Google Webmaster Tools
            $table->string('gst_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
