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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->integer('min_quantity')->default(10);
            $table->decimal('quantity', 20, 6)->default(0);
            $table->unique(
                ['company_id', 'item_id', 'brand_id', 'condition_id', 'location_id', 'unit_id'],
                'stocks_unique'
            );
            $table->timestamps();
        });
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('grn_date')->nullable();          // today date
            $table->date('po_date')->nullable();           // from PO
            $table->date('supplier_date')->nullable();     // manual
            $table->string('supplier_document')->nullable(); // file path
            $table->string('doc_no')->nullable();
            $table->string('sup_doc_num')->nullable();
            $table->date('doc_date');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('supplier_rate', 12, 2)->nullable();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();

            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);

            $table->decimal('stock_quantity', 12, 2);
            $table->foreignId('stock_unit_id')
                ->constrained('units')
                ->cascadeOnDelete();
            $table->decimal('rate', 12, 2)->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
