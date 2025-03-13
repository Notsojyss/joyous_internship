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
            // Add new columns
            $table->string('first_name')->after('full_name');
            $table->string('last_name')->after('first_name');
            $table->string('username')->unique()->after('last_name');
            // Drop the 'email' column
//            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the columns when rolling back
            $table->dropColumn(['first_name', 'last_name', 'username']);
        });
    }
};
