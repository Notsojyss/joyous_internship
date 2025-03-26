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
        Schema::create('pvp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->enum('host_play', ['Rock', 'Paper', 'Scissor']);
            $table->decimal('money_betted', 10, 2);

            $table->foreignId('opponent_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('opponent_play', ['Rock', 'Paper', 'Scissor'])->nullable();

            $table->enum('status', ['waiting', 'finished'])->default('waiting');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pvp');
    }
};
