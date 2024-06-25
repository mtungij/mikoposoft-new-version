<?php

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
        Schema::create('loan_category_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_fee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_category_id')->constrained()->cascadeOnDelete();
            $table->string('fee_type');
            $table->integer('fee_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_category_fees');
    }
};
