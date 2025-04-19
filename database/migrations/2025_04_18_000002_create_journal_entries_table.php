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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 20);
            $table->string('status', 20);
            $table->string('description');
            $table->dateTime('date');
            $table->string('currency', 3);
            $table->uuid('reversal_of')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
            
            $table->foreign('reversal_of')
                ->references('id')
                ->on('journal_entries')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
