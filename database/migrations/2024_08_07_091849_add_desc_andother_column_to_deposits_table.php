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
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('desc')->nullable()->after('customer_id');
            $table->string('status')->default('active');
            $table->string('duration')->nullable()->after('collection');
            $table->integer('repayments')->default(0)->after('duration');
            $table->date('next_return_date')->nullable()->after('repayments');
            $table->date('end_date')->nullable()->after('next_return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['desc', 'status', 'duration', 'repayments', 'next_return_date', 'end_date']);
        });
    }
};
