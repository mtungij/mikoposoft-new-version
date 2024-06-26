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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('c_number');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('gender');
            $table->string('phone');
            $table->string('ward')->nullable();
            $table->string('street')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('nick_name')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('working_status')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_location')->nullable();
            $table->integer('monthly_income');
            $table->string('account_type');
            $table->string('img_url')->nullable();
            $table->string('status')->default('new');
            $table->string('full_name')->virtualAs('concat(first_name, \' \', middle_name, \' \', last_name)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
