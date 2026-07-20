<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('contact_person', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'ordered', 'partial', 'received', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->date('order_date')->nullable();
            $table->date('expected_delivery')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('received_quantity', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['purchase_order_id', 'ingredient_id']);
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['addition', 'removal', 'correction', 'waste', 'return'])->default('correction');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
