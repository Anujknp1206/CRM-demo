<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->nullable()->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // creator
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete(); // assigned staff
            $table->date('quote_date');
            $table->string('pi_number')->nullable();
            $table->date('pi_date')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('office_address')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('special_clause')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->string('currency')->default('INR');
            $table->decimal('conversion_rate', 12, 4)->default(1);
            $table->enum('status', ['draft', 'sent', 'converted', 'rejected'])
                ->default('draft');

            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('component_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('hi_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('converted_unit_price', 12, 2)->nullable();
            $table->decimal('converted_total_price', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
