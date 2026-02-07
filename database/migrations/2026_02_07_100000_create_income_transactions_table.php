<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('income_type')->comment('milk_sale, animal_sale, dung_sale, biogas_sale, calf_sale, other');
            $table->decimal('amount', 12, 2);
            $table->decimal('quantity_liter', 10, 2)->nullable()->comment('For milk sales');
            $table->decimal('rate_per_liter', 10, 2)->nullable()->comment('For milk sales');
            $table->date('transaction_date');
            $table->string('buyer')->nullable();
            $table->string('payment_status')->default('paid')->comment('pending, partial, paid');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_transactions');
    }
};
