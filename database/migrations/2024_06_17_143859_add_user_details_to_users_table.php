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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->after('email')->nullable();
            $table->string('position')->default('admin')->after('phone')->nullable();
            $table->decimal('salary')->after('position')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable()->after('salary');
            $table->string('account')->after('salary')->nullable();
            $table->string('account_number')->after('account')->nullable();
            $table->string('status')->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'position', 'salary', 'gender', 'account', 'account_number', 'status']);
        });
    }
};
