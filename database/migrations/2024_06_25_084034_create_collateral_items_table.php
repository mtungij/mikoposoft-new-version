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
        Schema::create('collateral_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collateral_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('current_condition')->nullable();
            $table->integer('current_value')->nullable();
            $table->string('img_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collateral_items');
    }
};
