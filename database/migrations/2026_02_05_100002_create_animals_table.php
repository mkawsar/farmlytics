<?php

use App\Enums\Gender;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('animal_id')->unique()->comment('QR / RFID identifier');
            $table->string('breed');
            $table->string('gender')->default(Gender::MALE->value)->comment('Male or Female');
            $table->date('date_of_birth')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('current_weight', 8, 2)->nullable()->comment('Weight in kg');
            $table->string('status')->default(Status::ACTIVE->value)->comment('Active, Sold, Dead');
            $table->string('grouping')->nullable()->comment('Lactating, Dry, Pregnant, Calf, Fattening');
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
